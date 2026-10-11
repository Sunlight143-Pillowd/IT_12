<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-purple-700">Custom configuration</p>
                <h1 class="mt-1 text-2xl font-black text-gray-900">Build your PC</h1>
            </div>
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-purple-700 hover:underline">View my orders and builds</a>
            @endauth
        </div>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-7xl space-y-7 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <form id="customer-build-form" method="POST" action="{{ route('buildpc.customer.store') }}" class="space-y-5">
                    @csrf

                    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                        <div class="mb-5 grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="customer_name" class="mb-2 block text-sm font-bold text-gray-800">Your name</label>
                                <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name', auth()->user()?->name ?? '') }}" required maxlength="150" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="John Doe">
                            </div>
                            <div>
                                <label for="customer_email" class="mb-2 block text-sm font-bold text-gray-800">Email address <span class="font-normal text-gray-400">(optional)</span></label>
                                <input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email', auth()->user()?->email ?? '') }}" maxlength="150" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" placeholder="you@example.com">
                            </div>
                        </div>
                        <div class="mb-5">
                            <h2 class="text-lg font-black text-gray-900">Choose your components</h2>
                            <p class="mt-1 text-sm text-gray-500">Choose from currently available products. Select at least one component; our team will review your request and provide a quote.</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($componentGroups as $type => $label)
                                <div>
                                    <label for="component-{{ $type }}" class="mb-2 block text-sm font-bold text-gray-800">{{ $label }}</label>
                                    <select id="component-{{ $type }}" name="components[{{ $type }}]" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm transition focus:border-purple-500 focus:ring-purple-500">
                                        <option value="">Select {{ $label }} (optional)</option>
                                        @foreach ($groupedProducts[$type] ?? [] as $product)
                                            <option value="{{ $product->id }}" @selected((string) old('components.'.$type) === (string) $product->id)>
                                                {{ $product->name }} — {{ $product->availableStock() }} available
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                        <label for="build-notes" class="mt-5 block text-sm font-bold text-gray-800">
                            Additional notes <span class="font-normal text-gray-400">(optional)</span>
                            <textarea id="build-notes" name="notes" rows="3" maxlength="2000" placeholder="Budget, intended use, or other preferences" class="mt-2 block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('notes') }}</textarea>
                        </label>
                        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                            @auth
                                <button type="submit" class="inline-flex justify-center rounded-xl bg-purple-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">Submit build request</button>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex justify-center rounded-xl bg-purple-700 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-purple-800">Sign in to submit a request</a>
                            @endauth
                            <p class="text-xs text-gray-500">Our team will review your specifications and contact you with a quote.</p>
                        </div>
                    </section>
                </form>

            </div>

            @auth
                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-xl font-black text-gray-900">PC builds</h2>
                        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-bold text-purple-800">{{ $builds->count() }} build(s)</span>
                    </div>
                    @forelse ($builds as $build)
                        <article class="mb-4 rounded-xl border border-gray-200 p-4 last:mb-0">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Build ID · {{ $build->build_number }}</p>
                                    <h3 class="mt-1 text-lg font-bold text-gray-900">{{ $build->customer_name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $build->created_at->format('M j, Y') }}</p>
                                    @if ($build->notes)
                                        <p class="mt-2 whitespace-pre-line text-sm text-gray-700">{{ $build->notes }}</p>
                                    @endif
                                </div>
                                <span class="w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-800">{{ ucfirst($build->status) }}</span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('buildpc.show', $build) }}" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">View</a>
                                <a href="{{ route('buildpc.print', $build) }}" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">Print</a>
                                <a href="{{ route('buildpc.customer.edit', $build) }}" class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-gray-700 transition hover:border-purple-600 hover:text-purple-700">Edit</a>
                                <form action="{{ route('buildpc.customer.destroy', $build) }}" method="POST" onsubmit="return confirm('Delete this build?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center rounded border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-red-700 transition hover:border-red-300 hover:bg-red-100">Delete</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">No builds yet. Start by building your first custom PC.</div>
                    @endforelse
                </section>
            @endauth
        </div>
    </div>

</x-app-layout>
