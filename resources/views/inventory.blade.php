<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Inventory') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('dashboard') }}" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Dashboard</a>
                <a href="{{ route('stock-in.index') }}" class="rounded bg-purple-600 px-3 py-2 text-sm font-semibold text-white hover:bg-purple-700">Stock In Delivery</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Add New Item</h3>
                    <p class="text-sm text-gray-500">Create and manage stock items below.</p>
                </div>

                @php
                    $productTypeOptions = \App\Http\Controllers\InventoryController::productTypeOptions();
                @endphp

                <form method="POST" action="{{ route('inventory.store') }}" enctype="multipart/form-data" class="grid gap-3 md:grid-cols-2 xl:grid-cols-7">
                    @csrf
                    <div class="xl:col-span-2">
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Name</label>
                        <input type="text" name="name" required class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Type</label>
                        <select name="type" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                            @foreach ($productTypeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Category</label>
                        <select name="category" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                            <option value="general" selected>general</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Price</label>
                        <input type="number" name="price" min="0" value="0" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Location</label>
                        <select name="stock_location" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                            <option value="warehouse">Warehouse</option>
                            <option value="store">Store</option>
                            <option value="used_in_pc">Used in PC</option>
                        </select>
                    </div>
                    <div class="xl:col-span-5">
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Low Stock Alert</label>
                        <input type="number" name="low_stock_threshold" min="0" value="5" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div class="xl:col-span-2">
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Description</label>
                        <input type="text" name="description" class="w-full rounded border border-gray-300 px-3 py-2 text-sm" placeholder="Optional description">
                    </div>
                    @foreach (['image' => 'Product photo', 'before_image' => 'Before photo', 'after_image' => 'After photo'] as $field => $label)
                        <div @class(['xl:col-span-2' => $field === 'image'])>
                            <p class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">{{ $label }}</p>
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-500 hover:text-purple-700">
                                <span aria-hidden="true" class="text-lg leading-none">+</span>
                                <span>Add Photo</span>
                                <input type="file" name="{{ $field }}" accept="image/jpeg,image/png,image/webp" class="sr-only">
                            </label>
                        </div>
                    @endforeach
                    <div>
                        <label class="mb-1 block text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500">Warranty (months)</label>
                        <input type="number" name="warranty_months" min="0" max="1200" class="w-full rounded border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 xl:col-span-2">
                        <input type="checkbox" name="requires_serial" value="1" class="rounded border-gray-300">
                        Track serial number for each unit
                    </label>
                    <div class="xl:col-span-7 flex justify-end">
                        <button type="submit" class="rounded bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">Add Item</button>
                    </div>
                </form>

                <div class="mt-6 border-t border-gray-200 pt-5">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Categories</h3>
                            <p class="text-sm text-gray-500">Add and manage the categories used by inventory items.</p>
                        </div>
                        <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-semibold text-purple-700">{{ $categoryRecords->count() }} categories</span>
                    </div>

                    <div class="space-y-4">
                        <form method="POST" action="{{ route('inventory.categories.store') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            @csrf
                            <div class="min-w-0 flex-1">
                                <label for="new-category-name" class="mb-1 block text-xs font-semibold text-gray-700">Add Category</label>
                                <input id="new-category-name" type="text" name="name" placeholder="e.g. GPU, Monitor, SSD" required class="w-full rounded-lg border-gray-300 bg-white px-3 py-2 text-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-lg bg-purple-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-purple-800">Add Category</button>
                        </form>

                        @if ($categoryRecords->isNotEmpty())
                            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4">
                                @foreach ($categoryRecords as $category)
                                    <div class="flex min-w-0 items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 p-2">
                                        <form method="POST" action="{{ route('inventory.categories.update', $category->id) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="text" name="name" value="{{ $category->name }}" aria-label="Editable category name for {{ $category->name }}" required class="w-36 rounded-lg border-gray-300 bg-white px-2 py-1.5 text-sm focus:border-purple-500 focus:ring-purple-500">
                                            <button type="submit" class="rounded-lg bg-purple-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-purple-800">Edit</button>
                                        </form>
                                        <form method="POST" action="{{ route('inventory.categories.destroy', $category->id) }}" onsubmit="return confirm('Delete the {{ $category->name }} category?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100" aria-label="Delete {{ $category->name }} category">Delete</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="border-t border-gray-100 pt-4 text-sm text-gray-500">No categories yet. Add one to get started.</p>
                        @endif
                    </div>
                </div>
            </section>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-gray-700">
                        <thead class="bg-gray-50 text-xs uppercase tracking-[0.2em] text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Photo</th>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Price</th>
                                <th class="px-4 py-3">Stock</th>
                                <th class="px-4 py-3">Stock status</th>
                                <th class="px-4 py-3">Location</th>
                                <th class="px-4 py-3">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr class="border-t border-gray-200">
                                    <td class="px-4 py-3">
                                        @if ($product->image_path)
                                            <img src="{{ asset('storage/'.$product->image_path) }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-lg bg-gray-100 object-cover">
                                        @else
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-2xl font-light text-purple-500" aria-label="No product photo available">＋</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $product->name }}</td>
                                    <td class="px-4 py-3 capitalize">{{ $product->type }}</td>
                                    <td class="px-4 py-3">{{ $product->category }}</td>
                                    <td class="px-4 py-3">₱{{ number_format($product->price, 2) }}</td>
                                    <td class="px-4 py-3 {{ $product->stock_quantity <= $product->low_stock_threshold ? 'font-bold text-red-600' : '' }}">
                                        {{ $product->stock_quantity }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($product->stock_quantity <= 0)
                                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-800">OUT OF STOCK</span>
                                        @elseif ($product->stock_quantity <= $product->low_stock_threshold)
                                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">LOW STOCK</span>
                                            <p class="mt-1 text-xs font-medium text-amber-700">Low-stock warning · threshold {{ $product->low_stock_threshold }}</p>
                                        @else
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">IN STOCK</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $product->stock_location) }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('inventory.products.edit', $product) }}" class="rounded border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-purple-600 hover:text-purple-700">Edit item</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
