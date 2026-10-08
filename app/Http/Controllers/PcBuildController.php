<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePcBuildRequest;
use App\Models\PcBuild;
<<<<<<< HEAD
use App\Models\User;
use App\Services\PcBuildService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
=======
use App\Services\PcBuildService;
use Illuminate\Http\RedirectResponse;
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
use Illuminate\View\View;

class PcBuildController extends Controller
{
<<<<<<< HEAD
    public function __construct(protected PcBuildService $pcBuildService) {}
=======
    public function __construct(protected PcBuildService $pcBuildService)
    {
    }
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce

    public function index(): View
    {
        $builds = PcBuild::query()
<<<<<<< HEAD
            ->whereNull('user_id')
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
            ->with(['items.product', 'reservations.product'])
            ->orderByDesc('created_at')
            ->get();

        $products = $this->pcBuildService->availableProducts();

        return view('build-pc', [
            'builds' => $builds,
            'products' => $products,
            'componentGroups' => $this->pcBuildService->componentGroups(),
            'groupedProducts' => $this->groupProductsByType($products),
        ]);
    }

    public function store(StorePcBuildRequest $request): RedirectResponse
    {
        $build = $this->pcBuildService->createBuild(auth()->user(), $request->validated());

<<<<<<< HEAD
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
=======
        return redirect()->route('buildpc.index')->with('success', 'PC build #' . $build->build_number . ' saved and reserved successfully.');
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
    }

    public function show(PcBuild $pcBuild): View
    {
<<<<<<< HEAD
        abort_unless(auth()->user()?->canManageOrders() || $pcBuild->user_id === auth()->id(), 403);
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
        $pcBuild->load(['items.product', 'reservations.product']);

        return view('build-pc-show', compact('pcBuild'));
    }

    public function print(PcBuild $pcBuild): View
    {
<<<<<<< HEAD
        abort_unless(auth()->user()?->canManageOrders() || $pcBuild->user_id === auth()->id(), 403);
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
        $pcBuild->load(['items.product', 'reservations.product']);

        return view('build-pc-print', compact('pcBuild'));
    }

    public function cancel(PcBuild $pcBuild): RedirectResponse
    {
<<<<<<< HEAD
        abort_if($pcBuild->user_id !== null, 404);
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
        $this->pcBuildService->cancel($pcBuild);

        return back()->with('success', 'PC build cancelled and stock has been released.');
    }

    public function sell(PcBuild $pcBuild): RedirectResponse
    {
<<<<<<< HEAD
        abort_if($pcBuild->user_id !== null, 404);
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
        $this->pcBuildService->sell($pcBuild, auth()->user());

        return back()->with('success', 'PC build sold and stock deducted permanently.');
    }

    protected function groupProductsByType($products): array
    {
        $groups = [];

        foreach ($this->pcBuildService->componentGroups() as $type => $label) {
            $groups[$type] = $products
<<<<<<< HEAD
                ->filter(function ($product) use ($type): bool {
                    $group = $this->pcBuildService->componentGroupForProduct($product);

                    return $type === 'other' ? $group === 'other' : $group === $type;
                })
=======
                ->filter(fn ($product) => strtolower((string) $product->type) === strtolower((string) $type) || strtolower((string) $product->category) === strtolower((string) $label))
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                ->values();
        }

        return $groups;
    }
}
