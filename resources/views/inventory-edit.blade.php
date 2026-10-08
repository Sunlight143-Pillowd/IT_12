<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Edit Inventory Item</h2>
            <a href="{{ route('inventory.index') }}" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Inventory</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('inventory.products.update', $product) }}" enctype="multipart/form-data" class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                @csrf
                @method('PATCH')
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="text-sm font-semibold text-gray-700">Name
                        <input name="name" required value="{{ old('name', $product->name) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                    </label>
                    <label class="text-sm font-semibold text-gray-700">Type
                        <select name="type" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                            @foreach ($productTypeOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $product->type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm font-semibold text-gray-700">Category
                        <input name="category" required list="product-categories" value="{{ old('category', $product->category) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        <datalist id="product-categories">
                            @foreach ($categories as $category)
                                <option value="{{ $category }}">
                            @endforeach
                        </datalist>
                    </label>
                    <label class="text-sm font-semibold text-gray-700">Selling price
                        <input type="number" name="price" min="0" step="1" required value="{{ old('price', $product->price) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                    </label>
                    <label class="text-sm font-semibold text-gray-700">Low-stock threshold
                        <input type="number" name="low_stock_threshold" min="0" required value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                    </label>
                    <label class="text-sm font-semibold text-gray-700">Location
                        <select name="stock_location" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                            @foreach (['warehouse' => 'Warehouse', 'store' => 'Store', 'used_in_pc' => 'Used in PC'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('stock_location', $product->stock_location) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm font-semibold text-gray-700">Warranty (months)
                        <input type="number" name="warranty_months" min="0" max="1200" value="{{ old('warranty_months', $product->warranty_months) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                    </label>
                    <label class="flex items-center gap-2 self-end pb-2 text-sm font-semibold text-gray-700">
                        <input type="checkbox" name="requires_serial" value="1" @checked(old('requires_serial', $product->requires_serial)) class="rounded border-gray-300">
                        Track serial number per unit
                    </label>
                    <label class="text-sm font-semibold text-gray-700 md:col-span-2">Description
                        <textarea name="description" rows="3" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">{{ old('description', $product->description) }}</textarea>
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    @foreach (['image' => ['Product photo', $product->image_path], 'before_image' => ['Before photo', $product->before_image_path], 'after_image' => ['After photo', $product->after_image_path]] as $field => [$label, $path])
<<<<<<< HEAD
                        <div class="text-sm font-semibold text-gray-700">
                            <p>{{ $label }}</p>
                            @if ($path)
                                <img src="{{ asset('storage/'.$path) }}" alt="{{ $label }} for {{ $product->name }}" class="mt-2 mb-2 aspect-video w-full rounded border border-gray-200 object-cover">
                            @endif
                            <label class="mt-2 inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 transition hover:border-purple-500 hover:text-purple-700">
                                <span aria-hidden="true" class="text-lg leading-none">+</span>
                                <span>Add Photo</span>
                                <input type="file" name="{{ $field }}" accept="image/jpeg,image/png,image/webp" class="sr-only">
                            </label>
                        </div>
=======
                        <label class="text-sm font-semibold text-gray-700">{{ $label }}
                            @if ($path)
                                <img src="{{ asset('storage/'.$path) }}" alt="{{ $label }} for {{ $product->name }}" class="mt-2 mb-2 aspect-video w-full rounded border border-gray-200 object-cover">
                            @endif
                            <input type="file" name="{{ $field }}" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-xs font-normal">
                        </label>
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
                    @endforeach
                </div>

                <p class="text-sm text-gray-500">Current stock: <strong class="text-gray-700">{{ $product->stock_quantity }}</strong>. Record quantity changes from a supplier delivery on the Stock In page.</p>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('inventory.index') }}" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Cancel</a>
                    <button type="submit" class="rounded bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-700">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout><div>
    <!-- Order your soul. Reduce your wants. - Augustine -->
</div>
