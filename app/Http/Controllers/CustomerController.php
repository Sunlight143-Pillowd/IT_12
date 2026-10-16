<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\CustomerFeedback;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::query()
            ->with(['credits.payments.receiver', 'feedback.product'])
            ->latest()
            ->limit(100)
            ->get();
        $products = Product::query()->where('is_active', true)->orderBy('name')->get();

        return view('customers', compact('customers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:2000'],
            'loyalty_tier' => ['required', 'string', 'max:50'],
            'registered_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        Customer::create($data);

        return back()->with('success', 'Customer record created.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customer)],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:2000'],
            'loyalty_tier' => ['required', 'string', 'max:50'],
            'registered_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $customer->update($data);

        return back()->with('success', 'Customer record updated.');
    }

    public function storeCredit(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'credit_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount_due' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
        ]);

        $customer->credits()->create([
            ...$data,
            'balance_due' => $data['amount_due'],
        ]);

        return back()->with('success', 'Customer credit purchase recorded.');
    }

    public function recordPayment(Request $request, CustomerCredit $customerCredit): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
        ]);

        DB::transaction(function () use ($request, $data, $customerCredit): void {
            $credit = CustomerCredit::query()->lockForUpdate()->findOrFail($customerCredit->id);
            $amount = (float) $data['amount'];

            if ($amount > (float) $credit->balance_due) {
                throw ValidationException::withMessages([
                    'amount' => ['Payment cannot exceed the outstanding credit balance.'],
                ]);
            }

            $credit->payments()->create([
                'received_by' => $request->user()->id,
                'amount' => $amount,
                'paid_at' => now(),
            ]);
            $credit->decrement('balance_due', $amount);
        });

        return back()->with('success', 'Credit payment recorded and balance updated.');
    }

    public function storeFeedback(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        CustomerFeedback::create([
            ...$data,
            'customer_id' => $customer->id,
            'recorded_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Customer product feedback recorded.');
    }
}
