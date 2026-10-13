<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePcBuildRequest;
use App\Models\PcBuild;
use App\Models\Product;
use App\Models\User;
use App\Services\PcBuildService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PcBuildController extends Controller
{
    public function __construct(protected PcBuildService $pcBuildService) {}

    public function index(): View
    {
        $builds = PcBuild::query()
            ->whereNull('user_id')
            ->with(['items.product', 'reservations.product'])
            ->orderByDesc('created_at')
            ->get();
        $customerBuildManagement = PcBuild::query()
            ->whereNotNull('user_id')
            ->with(['items.product', 'user'])
            ->latest()
            ->get();

        $products = $this->pcBuildService->availableProducts();

        return view('build-pc', [
            'builds' => $builds,
            'customerBuildManagement' => $customerBuildManagement,
            'buildStatusOptions' => [
                'pending' => ['cancelled'],
                'accepted' => ['building', 'cancelled'],
                'building' => ['testing', 'cancelled'],
                'testing' => ['ready', 'cancelled'],
                'ready' => ['completed', 'cancelled'],
                'completed' => [],
                'cancelled' => [],
            ],
            'products' => $products,
            'componentGroups' => $this->pcBuildService->componentGroups(),
            'groupedProducts' => $this->groupProductsByType($products),
        ]);
    }

    public function store(StorePcBuildRequest $request): RedirectResponse
    {
        $build = $this->pcBuildService->createBuild(auth()->user(), $request->validated());

        return redirect()->route('buildpc.index')->with('success', 'PC build #'.$build->build_number.' saved and reserved successfully.');
    }

    public function customerIndex(Request $request): View
    {
        $builds = auth()->check()
            ? PcBuild::query()
                ->where('user_id', auth()->id())
                ->with('items.product')
                ->latest()
                ->get()
            : collect();
        $products = $this->pcBuildService->availableProducts();

        return view('store.build-pc', [
            'builds' => $builds,
            'componentGroups' => $this->pcBuildService->componentGroups(),
            'groupedProducts' => $this->groupProductsByType($products),
            'pendingBuild' => $request->session()->pull('pending_customer_build', []),
        ]);
    }

    public function storeCustomerBuild(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            $submittedComponents = $request->input('components', []);
            $pendingComponents = [];

            if (is_array($submittedComponents)) {
                foreach ($this->pcBuildService->componentGroups() as $component => $label) {
                    $productId = $submittedComponents[$component] ?? '';
                    $pendingComponents[$component] = is_scalar($productId) ? (string) $productId : '';
                }
            }

            $request->session()->put('pending_customer_build', [
                'customer_name' => is_string($request->input('customer_name')) ? $request->input('customer_name') : '',
                'customer_email' => is_string($request->input('customer_email')) ? $request->input('customer_email') : '',
                'components' => $pendingComponents,
                'notes' => is_string($request->input('notes')) ? $request->input('notes') : '',
            ]);
            $request->session()->put('url.intended', route('buildpc.customer'));

            return redirect()->route('login');
        }

        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:150'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'components' => ['required', 'array:cpu,motherboard,gpu,ram,storage,power_supply,case,cpu_cooler'],
            'components.*' => ['nullable', 'integer', 'exists:products,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $componentGroups = $this->pcBuildService->componentGroups();
        $products = $this->pcBuildService->availableProducts()->keyBy('id');
        $estimatedCost = 0.0;
        $componentNotes = collect($validated['components'])
            ->map(function (int|string|null $productId, string $component) use ($componentGroups, $products, &$estimatedCost): ?string {
                if ($productId === null) {
                    return null;
                }

                $product = $products->get((int) $productId);

                if ($product === null || $this->pcBuildService->componentGroupForProduct($product) !== $component) {
                    throw ValidationException::withMessages([
                        'components.'.$component => ['Select an available '.$componentGroups[$component].' from the list.'],
                    ]);
                }

                $estimatedCost += (float) $product->price;

                return $componentGroups[$component].': '.$product->name;
            })
            ->filter()
            ->values();

        if ($componentNotes->isEmpty()) {
            throw ValidationException::withMessages([
                'components' => ['Select at least one component for your build request.'],
            ]);
        }

        $additionalNotes = trim((string) ($validated['notes'] ?? ''));
        $validated['notes'] = $componentNotes
            ->when($additionalNotes !== '', fn ($lines) => $lines->push('Additional notes: '.$additionalNotes))
            ->implode("\n");

        if (mb_strlen($validated['notes']) > 2000) {
            throw ValidationException::withMessages([
                'notes' => ['The combined component details must not exceed 2,000 characters.'],
            ]);
        }

        $build = $this->pcBuildService->createCustomerBuild($user, $validated, $estimatedCost);
        $request->session()->forget('pending_customer_build');

        return redirect()->route('buildpc.customer')->with('success', 'Build '.$build->build_number.' submitted. You can follow its status here and in your dashboard.');
    }

    public function updateCustomerBuild(Request $request, PcBuild $pcBuild): RedirectResponse
    {
        abort_unless($request->user()?->canManageOrders(), 403);
        abort_if($pcBuild->user_id === null, 404);

        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,accepted,building,testing,ready,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'product_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'extensions:jpg,jpeg,png', 'max:5120'],
            'before_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'extensions:jpg,jpeg,png', 'max:5120'],
            'after_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'extensions:jpg,jpeg,png', 'max:5120'],
        ]);

        if (! empty($validated['status']) && $validated['status'] !== $pcBuild->status) {
            $this->pcBuildService->updateCustomerBuildStatus($pcBuild, $validated['status']);
        }

        if (array_key_exists('notes', $validated)) {
            $pcBuild->update(['notes' => trim((string) $validated['notes']) ?: null]);
        }

        $photoColumns = [
            'product_photo' => 'product_photo_path',
            'before_photo' => 'before_photo_path',
            'after_photo' => 'after_photo_path',
        ];
        $paths = [];
        foreach ($photoColumns as $input => $column) {
            if ($request->hasFile($input)) {
                $paths[$column] = $request->file($input)->store('pc-builds/'.$pcBuild->build_number, 'public');
            }
        }

        if ($paths !== []) {
            $previousPaths = array_filter(array_map(
                fn (string $column): ?string => $pcBuild->{$column},
                array_keys($paths)
            ));
            $pcBuild->update($paths);
            Storage::disk('public')->delete($previousPaths);
        }

        return back()->with('status', 'Build '.$pcBuild->build_number.' updated.');
    }

    public function show(PcBuild $pcBuild): View
    {
        abort_unless(auth()->user()?->canManageOrders() || $pcBuild->user_id === auth()->id(), 403);
        $pcBuild->load(['items.product', 'reservations.product']);

        return view('build-pc-show', compact('pcBuild'));
    }

    public function print(PcBuild $pcBuild): View
    {
        abort_unless(auth()->user()?->canManageOrders() || $pcBuild->user_id === auth()->id(), 403);
        $pcBuild->load(['items.product', 'reservations.product']);

        return view('build-pc-print', compact('pcBuild'));
    }

    public function edit(PcBuild $pcBuild): View
    {
        abort_unless(auth()->user()?->canManageOrders() || $pcBuild->user_id === auth()->id(), 403);
        $pcBuild->load('items.product');

        $products = $this->pcBuildService->availableProducts((int) $pcBuild->getKey());
        $availableQuantities = $this->pcBuildService->availableQuantities($products, (int) $pcBuild->getKey());
        $products->each(function (Product $product) use ($availableQuantities): void {
            $product->setAttribute('available_for_build', $availableQuantities[$product->id] ?? 0);
        });
        $selectedProducts = [];

        foreach ($pcBuild->items as $item) {
            $group = $this->pcBuildService->componentGroupForProduct($item->product);
            if ($group !== null) {
                $selectedProducts[$group] = $item->product_id;
            }
        }

        return view('build-pc-edit', [
            'pcBuild' => $pcBuild,
            'selectedProducts' => $selectedProducts,
            'products' => $products,
            'componentGroups' => $this->pcBuildService->componentGroups(),
            'groupedProducts' => $this->groupProductsByType($products),
            'isCustomer' => false,
        ]);
    }

    public function customerEdit(PcBuild $pcBuild): View
    {
        abort_unless(auth()->user()?->canManageOrders() || $pcBuild->user_id === auth()->id(), 403);
        $pcBuild->load('items.product');

        $products = $this->pcBuildService->availableProducts((int) $pcBuild->getKey());
        $availableQuantities = $this->pcBuildService->availableQuantities($products, (int) $pcBuild->getKey());
        $products->each(function (Product $product) use ($availableQuantities): void {
            $product->setAttribute('available_for_build', $availableQuantities[$product->id] ?? 0);
        });
        $selectedProducts = [];

        foreach ($pcBuild->items as $item) {
            $group = $this->pcBuildService->componentGroupForProduct($item->product);
            if ($group !== null) {
                $selectedProducts[$group] = $item->product_id;
            }
        }

        return view('store.build-pc-edit', [
            'pcBuild' => $pcBuild,
            'selectedProducts' => $selectedProducts,
            'products' => $products,
            'componentGroups' => $this->pcBuildService->componentGroups(),
            'groupedProducts' => $this->groupProductsByType($products),
            'isCustomer' => true,
        ]);
    }

    public function update(Request $request, PcBuild $pcBuild): RedirectResponse
    {
        abort_unless(auth()->user()?->canManageOrders() || $pcBuild->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
        ]);

        $this->pcBuildService->updateBuildItems($pcBuild, $validated);

        $route = $pcBuild->user_id !== null ? 'buildpc.customer' : 'buildpc.index';

        return redirect()->route($route)->with('success', 'Build '.$pcBuild->build_number.' updated successfully.');
    }

    public function destroy(PcBuild $pcBuild): RedirectResponse
    {
        abort_unless(auth()->user()?->canManageOrders() || $pcBuild->user_id === auth()->id(), 403);
        $this->pcBuildService->deleteBuild($pcBuild);

        return back()->with('success', 'PC build deleted successfully.');
    }

    public function cancel(PcBuild $pcBuild): RedirectResponse
    {
        abort_if($pcBuild->user_id !== null, 404);
        $this->pcBuildService->cancel($pcBuild);

        return back()->with('success', 'PC build cancelled and stock has been released.');
    }

    public function sell(PcBuild $pcBuild): RedirectResponse
    {
        abort_if($pcBuild->user_id !== null, 404);
        $this->pcBuildService->sell($pcBuild, auth()->user());

        return back()->with('success', 'PC build sold and stock deducted permanently.');
    }

    protected function groupProductsByType($products): array
    {
        $groups = [];

        foreach ($this->pcBuildService->componentGroups() as $type => $label) {
            $groups[$type] = $products
                ->filter(function ($product) use ($type): bool {
                    $group = $this->pcBuildService->componentGroupForProduct($product);

                    return $group === $type;
                })
                ->values();
        }

        return $groups;
    }
}
