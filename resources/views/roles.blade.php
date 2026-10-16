<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Roles & Permissions</h2>
            <a href="{{ route('employees.index') }}" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Employees</a>
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
                <h3 class="mb-4 text-lg font-bold text-gray-900">Create Role</h3>
                <form method="POST" action="{{ route('roles.store') }}" class="space-y-4">
                    @csrf
                    <input name="name" required maxlength="100" placeholder="Role name" class="w-full rounded border-gray-300 text-sm sm:max-w-md">
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($permissionOptions as $permission => $label)
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="permissions[]" value="{{ $permission }}" class="rounded border-gray-300 text-purple-700">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <button class="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">Create role</button>
                </form>
            </section>

            <section class="grid gap-4 lg:grid-cols-2">
                @foreach ($roles as $role)
                    <form method="POST" action="{{ route('roles.update', $role) }}" class="space-y-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        @csrf
                        @method('PATCH')
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $role->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $role->users_count }} assigned account(s) · {{ $role->is_system ? 'System role' : 'Custom role' }}</p>
                            </div>
                            <input name="name" value="{{ $role->name }}" required maxlength="100" aria-label="Role name" class="w-40 rounded border-gray-300 text-sm">
                        </div>
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach ($permissionOptions as $permission => $label)
                                <label class="flex items-start gap-2 text-xs text-gray-700">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission }}" @checked(in_array($permission, $role->permissions ?? [], true)) class="mt-0.5 rounded border-gray-300 text-purple-700">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                        <button class="rounded border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-800 hover:bg-purple-100">Save permissions</button>
                    </form>
                @endforeach
            </section>
        </div>
    </div>
</x-app-layout>
