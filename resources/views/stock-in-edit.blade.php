<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit supplier delivery</h2>
            <a href="{{ route('stock-in.index') }}" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600 hover:text-purple-700">Back to Stock In</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-gray-900">Delivery details</h3>
                <form method="POST" action="{{ route('stock-in.update', $stockIn) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <label class="text-sm font-semibold text-gray-700">Supplier
                            <select name="supplier_id" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                                <option value="">No linked supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id', $stockIn->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <input name="supplier_name" required value="{{ old('supplier_name', $stockIn->supplier_name) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        </label>
                        <label class="text-sm font-semibold text-gray-700">Supplier contact
                            <input name="supplier_contact" value="{{ old('supplier_contact', $stockIn->supplier_contact) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        </label>
                        <label class="text-sm font-semibold text-gray-700">Invoice number
                            <input name="invoice_number" required value="{{ old('invoice_number', $stockIn->invoice_number) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        </label>
                        <label class="text-sm font-semibold text-gray-700">Date received
                            <input type="date" name="received_at" required value="{{ old('received_at', $stockIn->received_at->toDateString()) }}" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">
                        </label>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach ([
                            'delivery_document' => ['Delivery document', $stockIn->delivery_document_path, '.pdf,.jpg,.jpeg,.png,.webp'],
                            'before_photo' => ['Before delivery photo', $stockIn->before_photo_path, 'image/jpeg,image/png,image/webp'],
                            'after_photo' => ['After delivery photo', $stockIn->after_photo_path, 'image/jpeg,image/png,image/webp'],
                        ] as $field => [$label, $path, $accept])
                            <label class="text-sm font-semibold text-gray-700">{{ $label }}
                                @if ($path)
                                    <a href="{{ asset('storage/'.$path) }}" target="_blank" rel="noopener" class="mt-1 block text-xs font-medium text-purple-700 hover:underline">View current file</a>
                                @endif
                                <input type="file" name="{{ $field }}" accept="{{ $accept }}" class="mt-1 block w-full text-sm font-normal">
                            </label>
                        @endforeach
                    </div>

                    <div class="rounded border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                        Delivered products, quantities, unit costs, warranties, and serial numbers are read-only. You can still update supplier details, notes, and attached files.
                    </div>
                    <div class="space-y-2">
                        <h4 class="font-bold text-gray-900">Delivered items <span class="font-normal text-gray-500">(read-only)</span></h4>
                        @foreach ($stockIn->items as $item)
                            @php
                                $itemVat = \App\Support\PhilippineVat::breakdown((float) $item->unit_cost * $item->quantity);
                            @endphp
                            <div class="rounded border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                                <span class="font-semibold">{{ $item->product->name }}</span>
                                <span> · {{ $item->quantity }} units · ₱{{ number_format((float) $item->unit_cost, 2) }} each · ₱{{ number_format($itemVat['total'], 2) }} total (VAT included)</span>
                                <span class="mt-1 block text-xs text-gray-500">VAT (12%, included): ₱{{ number_format($itemVat['vat'], 2) }}</span>
                                @foreach ($item->units->pluck('serial_number')->filter() as $serialNumber)
                                    <span class="mt-1 block text-xs text-gray-500">Serial: {{ $serialNumber }}</span>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    <label class="block text-sm font-semibold text-gray-700">Delivery notes
                        <textarea name="notes" rows="2" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 font-normal">{{ old('notes', $stockIn->notes) }}</textarea>
                    </label>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded bg-purple-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-purple-700">Save changes</button>
                    </div>
                </form>
            </section>
        </div>
    </div>

</x-app-layout>
