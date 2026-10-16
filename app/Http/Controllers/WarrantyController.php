<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ProductUnit;
use App\Models\ServiceCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WarrantyController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $units = collect();

        if ($search !== '') {
            $units = ProductUnit::query()
                ->with(['product', 'stockInItem.stockIn.supplier', 'saleItem.sale.customer'])
                ->where(function ($query) use ($search): void {
                    $query->where('serial_number', $search)
                        ->orWhere('lot_code', $search)
                        ->orWhereHas('product', function ($productQuery) use ($search): void {
                            $productQuery->where('sku', $search)
                                ->orWhere('name', 'like', '%'.$search.'%');
                        });
                })
                ->latest()
                ->limit(100)
                ->get();
        }

        $serviceCases = ServiceCase::query()
            ->with(['customer', 'productUnit.product'])
            ->latest()
            ->limit(50)
            ->get();
        $customers = Customer::query()->orderBy('name')->get();

        return view('warranty', compact('search', 'units', 'serviceCases', 'customers'));
    }

    public function storeCase(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_unit_id' => ['required', 'integer', Rule::exists('product_units', 'id')->where('status', 'sold')],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'case_type' => ['required', Rule::in(['warranty', 'replacement'])],
            'description' => ['required', 'string', 'max:3000'],
        ]);

        ServiceCase::create([
            ...$data,
            'case_number' => 'CASE-'.now()->format('ymd').'-'.Str::upper(Str::random(6)),
            'created_by' => $request->user()->id,
            'status' => 'open',
        ]);

        return back()->with('success', 'Service transaction opened.');
    }

    public function updateCase(Request $request, ServiceCase $serviceCase): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['open', 'under_review', 'replacement_in_progress', 'completed'])],
            'resolution' => ['nullable', 'string', 'max:3000'],
        ]);

        $serviceCase->update([
            ...$data,
            'completed_at' => $data['status'] === 'completed' ? ($serviceCase->completed_at ?? now()) : null,
        ]);

        return back()->with('success', 'Transaction status updated.');
    }
}
