<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        $expenses = Expense::query()->with('recorder')->latest('expense_date')->latest()->limit(100)->get();

        return view('expenses', compact('expenses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'expense_date' => ['required', 'date'],
            'budget_code' => ['nullable', 'string', 'max:100'],
        ]);

        Expense::create([
            ...$data,
            'recorded_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Expense recorded.');
    }
}
