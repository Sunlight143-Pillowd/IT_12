<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePcBuildRequest;
use App\Models\PcBuild;
use App\Services\PcBuildService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PcBuildController extends Controller
{
    public function __construct(protected PcBuildService $pcBuildService)
    {
    }

    public function index(): View
    {
        $builds = PcBuild::query()
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

        return redirect()->route('buildpc.index')->with('success', 'PC build #' . $build->build_number . ' saved and reserved successfully.');
    }

    public function show(PcBuild $pcBuild): View
    {
        $pcBuild->load(['items.product', 'reservations.product']);

        return view('build-pc-show', compact('pcBuild'));
    }

    public function print(PcBuild $pcBuild): View
    {
        $pcBuild->load(['items.product', 'reservations.product']);

        return view('build-pc-print', compact('pcBuild'));
    }

    public function cancel(PcBuild $pcBuild): RedirectResponse
    {
        $this->pcBuildService->cancel($pcBuild);

        return back()->with('success', 'PC build cancelled and stock has been released.');
    }

    public function sell(PcBuild $pcBuild): RedirectResponse
    {
        $this->pcBuildService->sell($pcBuild, auth()->user());

        return back()->with('success', 'PC build sold and stock deducted permanently.');
    }

    protected function groupProductsByType($products): array
    {
        $groups = [];

        foreach ($this->pcBuildService->componentGroups() as $type => $label) {
            $groups[$type] = $products
                ->filter(fn ($product) => strtolower((string) $product->type) === strtolower((string) $type) || strtolower((string) $product->category) === strtolower((string) $label))
                ->values();
        }

        return $groups;
    }
}
