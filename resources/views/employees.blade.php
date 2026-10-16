<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Employee Accounts</h2>
            <div class="flex gap-2">
                <a href="{{ route('roles.index') }}" class="rounded border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-800 hover:bg-purple-100">Manage Roles</a>
                <a href="{{ route('dashboard') }}" class="rounded border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-purple-600">Back to Dashboard</a>
            </div>
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
                <h3 class="mb-4 text-lg font-bold text-gray-900">Create Employee Account</h3>
                <form method="POST" action="{{ route('employees.store') }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @csrf
                    <input name="name" value="{{ old('name') }}" required placeholder="Employee name" class="rounded border-gray-300 text-sm">
                    <input name="email" type="email" value="{{ old('email') }}" required placeholder="Email" class="rounded border-gray-300 text-sm">
                    <select name="role_id" required class="rounded border-gray-300 text-sm">
                        <option value="">Assign role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <input name="password" type="password" required minlength="8" placeholder="Temporary password" class="rounded border-gray-300 text-sm">
                    <input name="password_confirmation" type="password" required minlength="8" placeholder="Confirm password" class="rounded border-gray-300 text-sm">
                    <button class="rounded bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">Create account</button>
                </form>
            </section>

            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 p-5">
                    <h3 class="text-lg font-bold text-gray-900">Staff Directory</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse ($employees as $employee)
                        <form method="POST" action="{{ route('employees.update', $employee) }}" class="grid gap-3 p-5 sm:grid-cols-2 xl:grid-cols-4">
                            @csrf
                            @method('PATCH')
                            <input name="name" value="{{ $employee->name }}" required aria-label="Employee name" class="rounded border-gray-300 text-sm">
                            <input name="email" type="email" value="{{ $employee->email }}" required aria-label="Employee email" class="rounded border-gray-300 text-sm">
                            <select name="role_id" required aria-label="Employee role" class="rounded border-gray-300 text-sm">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @selected($employee->role_id === $role->id)>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" @checked($employee->is_active) class="rounded border-gray-300 text-purple-700">
                                Active
                            </label>
                            <input name="password" type="password" minlength="8" placeholder="New password (optional)" class="rounded border-gray-300 text-sm">
                            <input name="password_confirmation" type="password" minlength="8" placeholder="Confirm new password" class="rounded border-gray-300 text-sm">
                            <button class="rounded border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-800 hover:bg-purple-100 xl:col-span-2">Save account</button>
                        </form>
                    @empty
                        <p class="p-5 text-sm text-gray-500">No employee accounts yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
