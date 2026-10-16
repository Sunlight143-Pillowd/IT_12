<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = User::query()
            ->with('role')
            ->whereHas('role', fn ($query) => $query->where('slug', '!=', 'customer'))
            ->orderBy('name')
            ->get();
        $roles = Role::query()->where('slug', '!=', 'customer')->orderBy('name')->get();

        return view('employees', compact('employees', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $roleIds = Role::query()->where('slug', '!=', 'customer')->pluck('id')->all();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'integer', Rule::in($roleIds)],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Employee account created.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $roleIds = Role::query()->where('slug', '!=', 'customer')->pluck('id')->all();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'integer', Rule::in($roleIds)],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($request->user()->is($user) && ! $request->boolean('is_active')) {
            return back()->withErrors(['is_active' => 'You cannot deactivate your own account.']);
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'role_id' => $data['role_id'],
            'is_active' => $data['is_active'],
        ]);
        if (filled($data['password'] ?? null)) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return back()->with('success', 'Employee account updated.');
    }
}
