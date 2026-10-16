<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()->withCount('users')->orderBy('name')->get();
        $permissionOptions = Role::permissionOptions();

        return view('roles', compact('roles', 'permissionOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(array_keys(Role::permissionOptions()))],
        ]);

        $slug = Str::slug($data['name']);
        if (Role::where('slug', $slug)->exists()) {
            throw ValidationException::withMessages(['name' => 'A role with this name already exists.']);
        }

        Role::create([
            'name' => $data['name'],
            'slug' => $slug,
            'permissions' => array_values($data['permissions'] ?? []),
            'is_system' => false,
        ]);

        return back()->with('success', 'Role created.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(array_keys(Role::permissionOptions()))],
        ]);
        $permissions = array_values($data['permissions'] ?? []);

        if ($role->slug === 'owner' && ! in_array('employees.manage', $permissions, true)) {
            throw ValidationException::withMessages([
                'permissions' => ['The Owner role must retain employee-management access.'],
            ]);
        }

        $role->update([
            'name' => $data['name'],
            'permissions' => $permissions,
        ]);

        return back()->with('success', 'Role permissions updated.');
    }
}
