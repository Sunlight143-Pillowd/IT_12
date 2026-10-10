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
            ->with('items.product')
            ->latest()
            ->get();

        $products = $this->pcBuildService->availableProducts();

        return view('build-pc', [
            'builds' => $builds,
            'customerBuildManagement' => $customerBuildManagement,
            'buildStatusOptions' => [
                'pending' => ['accepted', 'cancelled'],
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

    public function customerIndex(): View
    {
        $products = $this->pcBuildService->availableProducts();

        $builds = auth()->check()
            ? PcBuild::query()
                ->where('user_id', auth()->id())
                ->with('items.product')
                ->latest()
                ->get()
            : collect();

        return view('store.build-pc', [
            'builds' => $builds,
            'componentGroups' => $this->pcBuildService->componentGroups(),
            'groupedProducts' => $this->groupProductsByType($products),
        ]);
    }

    public function storeCustomerBuild(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:150'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'items' => ['required', 'array'],
            'items.*' => ['array'],
            'items.*.product_id' => ['nullable', 'integer', 'exists:products,id'],
        ]);

        abort_unless($request->user() instanceof User, 401);
        $build = $this->pcBuildService->createCustomerBuild($request->user(), $validated);

        return redirect()->route('buildpc.customer')->with('success', 'Build '.$build->build_number.' submitted. You can follow its status here and in your dashboard.');
    }

    public function updateCustomerBuild(Request $request, PcBuild $pcBuild): RedirectResponse
    {
        abort_unless($request->user()?->canManageOrders(), 403);
        abort_if($pcBuild->user_id === null, 404);

        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,accepted,building,testing,ready,completed,cancelled'],
            'product_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'before_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'after_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if (! empty($validated['status']) && $validated['status'] !== $pcBuild->status) {
            $this->pcBuildService->updateCustomerBuildStatus($pcBuild, $validated['status']);
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
            $selectedProducts[$group ?? 'other'] = $item->product_id;
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
            $selectedProducts[$group ?? 'other'] = $item->product_id;
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

                    return $type === 'other' ? $group === 'other' : $group === $type;
                })
                ->values();
        }

        return $groups;
    }
}
