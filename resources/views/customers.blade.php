<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Customers, Credit & Feedback</h2>
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
                <h3 class="mb-4 text-lg font-bold text-gray-900">Register Customer</h3>
                <form method="POST" action="{{ route('customers.store') }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    @csrf
                    <input name="name" value="{{ old('name') }}" required maxlength="150" placeholder="Customer name" class="rounded border-gray-300 text-sm">
                    <input name="email" type="email" value="{{ old('email') }}" placeholder="Email" class="rounded border-gray-300 text-sm">
                    <input name="phone" value="{{ old('phone') }}" placeholder="Phone" class="rounded border-gray-300 text-sm">
                    <select name="loyalty_tier" class="rounded border-gray-300 text-sm">
                        @foreach (['Regular', 'Silver', 'Gold', 'Platinum'] as $tier)
                            <option value="{{ $tier }}">{{ $tier }}</option>
                        @endforeach
                    </select>
                    <input name="address" value="{{ old('address') }}" placeholder="Address" class="rounded border-gray-300 text-sm sm:col-span-2">
                    <input name="registered_at" type="date" value="{{ old('registered_at', today()->toDateString()) }}" class="rounded border-gray-300 text-sm">
                    <button class="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">Add customer</button>
                    <textarea name="notes" rows="2" placeholder="Notes" class="rounded border-gray-300 text-sm sm:col-span-2 xl:col-span-4"></textarea>
                </form>
            </section>

            <section class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900">Customer Records (latest 100)</h3>
                @forelse ($customers as $customer)
                    <article class="space-y-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="grid gap-4 xl:grid-cols-2">
                            <form method="POST" action="{{ route('customers.update', $customer) }}" class="grid gap-2 sm:grid-cols-2">
                                @csrf
                                @method('PATCH')
                                <input name="name" value="{{ $customer->name }}" required aria-label="Customer name" class="rounded border-gray-300 text-sm">
                                <input name="email" type="email" value="{{ $customer->email }}" aria-label="Customer email" class="rounded border-gray-300 text-sm">
                                <input name="phone" value="{{ $customer->phone }}" aria-label="Customer phone" class="rounded border-gray-300 text-sm">
                                <select name="loyalty_tier" class="rounded border-gray-300 text-sm">
                                    @foreach (['Regular', 'Silver', 'Gold', 'Platinum'] as $tier)
                                        <option value="{{ $tier }}" @selected($customer->loyalty_tier === $tier)>{{ $tier }}</option>
                                    @endforeach
                                </select>
                                <input name="address" value="{{ $customer->address }}" aria-label="Customer address" class="rounded border-gray-300 text-sm sm:col-span-2">
                                <input name="registered_at" type="date" value="{{ $customer->registered_at?->toDateString() }}" aria-label="Registration date" class="rounded border-gray-300 text-sm">
                                <button class="rounded border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-800 hover:bg-purple-100">Save details</button>
                                <textarea name="notes" rows="2" aria-label="Customer notes" class="rounded border-gray-300 text-sm sm:col-span-2">{{ $customer->notes }}</textarea>
                            </form>

                            <div class="space-y-3 rounded-lg bg-gray-50 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <h4 class="font-bold text-gray-900">Credit balance</h4>
                                    <p class="text-sm font-bold text-purple-800">₱{{ number_format((float) $customer->credits->sum('balance_due'), 2) }}</p>
                                </div>
                                <form method="POST" action="{{ route('customers.credits.store', $customer) }}" class="grid gap-2 sm:grid-cols-2">
                                    @csrf
                                    <input name="credit_date" type="date" required value="{{ today()->toDateString() }}" class="rounded border-gray-300 text-sm">
                                    <input name="amount_due" type="number" min="0.01" step="0.01" required placeholder="Amount due" class="rounded border-gray-300 text-sm">
                                    <input name="description" maxlength="255" placeholder="Credit purchase details" class="rounded border-gray-300 text-sm sm:col-span-2">
                                    <button class="rounded bg-purple-700 px-3 py-2 text-sm font-semibold text-white hover:bg-purple-800 sm:col-span-2">Record credit purchase</button>
                                </form>
                                @foreach ($customer->credits as $credit)
                                    <div class="border-t border-gray-200 pt-3 text-sm">
                                        <div class="flex flex-wrap justify-between gap-2">
                                            <span>{{ $credit->credit_date->format('M j, Y') }} · {{ $credit->description ?: 'Credit purchase' }}</span>
                                            <span class="font-semibold">Due ₱{{ number_format((float) $credit->balance_due, 2) }} / ₱{{ number_format((float) $credit->amount_due, 2) }}</span>
                                        </div>
                                        @if ((float) $credit->balance_due > 0)
                                            <form method="POST" action="{{ route('credits.payments.store', $credit) }}" class="mt-2 flex flex-wrap gap-2">
                                                @csrf
                                                <input name="amount" type="number" min="0.01" max="{{ $credit->balance_due }}" step="0.01" required placeholder="Payment amount" class="w-44 rounded border-gray-300 text-sm">
                                                <button class="rounded border border-green-200 bg-green-50 px-3 py-2 text-xs font-bold text-green-800 hover:bg-green-100">Record payment</button>
                                            </form>
                                        @endif
                                        @foreach ($credit->payments as $payment)
                                            <p class="mt-1 text-xs text-gray-500">Payment ₱{{ number_format((float) $payment->amount, 2) }} · {{ $payment->paid_at->format('M j, Y g:i A') }}</p>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <h4 class="mb-3 font-bold text-gray-900">Record Product Feedback</h4>
                            <form method="POST" action="{{ route('customers.feedback.store', $customer) }}" class="grid gap-2 sm:grid-cols-4">
                                @csrf
                                <select name="product_id" required class="rounded border-gray-300 text-sm sm:col-span-2">
                                    <option value="">Select product</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                <select name="rating" required class="rounded border-gray-300 text-sm">
                                    <option value="">Rating</option>
                                    @foreach (range(1, 5) as $rating)
                                        <option value="{{ $rating }}">{{ $rating }} / 5</option>
                                    @endforeach
                                </select>
                                <button class="rounded bg-purple-700 px-3 py-2 text-sm font-semibold text-white hover:bg-purple-800">Save feedback</button>
                                <textarea name="comment" maxlength="2000" rows="2" placeholder="Customer comment" class="rounded border-gray-300 text-sm sm:col-span-4"></textarea>
                            </form>
                            @foreach ($customer->feedback as $feedback)
                                <p class="mt-2 text-sm text-gray-600">{{ $feedback->product->name }} · {{ $feedback->rating }}/5 — {{ $feedback->comment }}</p>
                            @endforeach
                        </div>
                    </article>
                @empty
                    <p class="rounded-lg bg-white p-5 text-sm text-gray-500">No customer records yet.</p>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
