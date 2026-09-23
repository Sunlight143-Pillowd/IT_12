<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $name = trim($data['name']);
        $slug = Str::slug($name);

        if ($slug === '') {
            return redirect()->route('inventory.index')->with('error', 'Category name is required.');
        }

        Category::firstOrCreate(
            ['slug' => $slug],
            ['name' => $name]
        );

        return redirect()->route('inventory.index')->with('success', 'Category added successfully.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $name = trim($data['name']);
        $slug = Str::slug($name);

        if ($slug === '') {
            return redirect()->route('inventory.index')->with('error', 'Category name is required.');
        }

        $category->update([
            'name' => $name,
            'slug' => $slug,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Category updated successfully.');
    }
}
