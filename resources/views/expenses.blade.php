<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Business Expenses</h2>
            <a href="{{ route('dashboard') }}" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <p class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                <ul class="list-disc rounded border border-red-200 bg-red-50 px-4 py-3 pl-8 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-gray-900">Record Expense</h3>
                <form method="POST" action="{{ route('expenses.store') }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    @csrf
                    <input name="type" required maxlength="100" placeholder="Expense type" class="rounded border-gray-300 text-sm">
                    <input name="amount" type="number" min="0.01" step="0.01" required placeholder="Amount" class="rounded border-gray-300 text-sm">
                    <input name="expense_date" type="date" required value="{{ today()->toDateString() }}" class="rounded border-gray-300 text-sm">
                    <input name="budget_code" maxlength="100" placeholder="Budget code" class="rounded border-gray-300 text-sm">
                    <textarea name="description" maxlength="2000" rows="2" placeholder="Description" class="rounded border-gray-300 text-sm sm:col-span-2 xl:col-span-3"></textarea>
                    <button class="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">Save expense</button>
                </form>
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-5">
                    <h3 class="text-lg font-bold text-gray-900">Recent Expenses</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr><th class="p-3">Date</th><th class="p-3">Type / Description</th><th class="p-3">Budget code</th><th class="p-3">Recorded by</th><th class="p-3 text-right">Amount</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($expenses as $expense)
                                <tr>
                                    <td class="p-3">{{ $expense->expense_date->format('M j, Y') }}</td>
                                    <td class="p-3"><strong>{{ $expense->type }}</strong><div class="text-xs text-gray-500">{{ $expense->description }}</div></td>
                                    <td class="p-3">{{ $expense->budget_code ?: '—' }}</td>
                                    <td class="p-3">{{ $expense->recorder?->name ?: 'Account removed' }}</td>
                                    <td class="p-3 text-right font-semibold">₱{{ number_format((float) $expense->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-5 text-center text-gray-500">No expenses recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
