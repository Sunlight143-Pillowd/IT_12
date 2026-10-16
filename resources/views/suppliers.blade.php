<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Suppliers</h2>
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
                <h3 class="mb-4 text-lg font-bold text-gray-900">Add Supplier</h3>
                <form method="POST" action="{{ route('suppliers.store') }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @csrf
                    <input name="name" required maxlength="150" placeholder="Supplier name" class="rounded border-gray-300 text-sm">
                    <input name="contact_name" placeholder="Contact person" class="rounded border-gray-300 text-sm">
                    <input name="phone" placeholder="Phone" class="rounded border-gray-300 text-sm">
                    <input name="email" type="email" placeholder="Email" class="rounded border-gray-300 text-sm">
                    <input name="address" placeholder="Address" class="rounded border-gray-300 text-sm sm:col-span-2">
                    <textarea name="notes" rows="2" placeholder="Notes" class="rounded border-gray-300 text-sm sm:col-span-2 xl:col-span-3"></textarea>
                    <button class="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800 xl:col-span-3">Add supplier</button>
                </form>
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-5">
                    <h3 class="text-lg font-bold text-gray-900">Supplier Directory</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($suppliers as $supplier)
                        <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="grid gap-3 p-5 sm:grid-cols-2 xl:grid-cols-4">
                            @csrf
                            @method('PATCH')
                            <input name="name" value="{{ $supplier->name }}" required aria-label="Supplier name" class="rounded border-gray-300 text-sm">
                            <input name="contact_name" value="{{ $supplier->contact_name }}" aria-label="Contact name" class="rounded border-gray-300 text-sm">
                            <input name="phone" value="{{ $supplier->phone }}" aria-label="Supplier phone" class="rounded border-gray-300 text-sm">
                            <input name="email" type="email" value="{{ $supplier->email }}" aria-label="Supplier email" class="rounded border-gray-300 text-sm">
                            <input name="address" value="{{ $supplier->address }}" aria-label="Supplier address" class="rounded border-gray-300 text-sm sm:col-span-2">
                            <textarea name="notes" rows="2" aria-label="Supplier notes" class="rounded border-gray-300 text-sm sm:col-span-2">{{ $supplier->notes }}</textarea>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" @checked($supplier->is_active) class="rounded border-gray-300 text-purple-700">
                                Active
                            </label>
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs text-gray-500">{{ $supplier->stock_ins_count }} deliveries</span>
                                <button class="rounded border border-purple-200 bg-purple-50 px-3 py-2 text-xs font-bold text-purple-800 hover:bg-purple-100">Save</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="flex justify-end px-5 pb-4" onsubmit="return confirm('Remove this supplier? Linked delivery history will remain.')">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs font-semibold text-red-700 hover:underline">Remove supplier</button>
                        </form>
                    @empty
                        <p class="p-5 text-sm text-gray-500">No suppliers have been added.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
