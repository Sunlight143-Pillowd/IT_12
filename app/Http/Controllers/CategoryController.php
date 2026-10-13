<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HomepageImage;
use App\Models\Product;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canManageOrders(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $name = trim($data['name']);
        $slug = Str::slug($name);

        if ($slug === '') {
            return redirect()->route('inventory.index')->with('error', 'Category name is required.');
        }

        Category::firstOrCreate(['slug' => $slug], [
            'name' => $name,
            'sort_order' => ((int) Category::max('sort_order')) + 1,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Category added successfully.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        abort_unless($request->user()?->canManageOrders(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $name = trim($data['name']);
        $slug = Str::slug($name);

        if ($slug === '') {
            return redirect()->route('inventory.index')->with('error', 'Category name is required.');
        }

        if (Category::query()->where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A category with this name already exists.',
            ]);
        }

        DB::transaction(function () use ($category, $name, $slug): void {
            Product::query()
                ->where('category', $category->name)
                ->update(['category' => $name]);

            $category->update([
                'name' => $name,
                'slug' => $slug,
            ]);
        });

        return redirect()->route('inventory.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        abort_unless($request->user()?->canManageOrders(), 403);

        $imagePath = $category->image_path;

        $category->delete();

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('inventory.index')->with('success', 'Category deleted successfully.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canManageOrders(), 403);

        $validated = $request->validate([
            'category_ids' => ['required', 'array'],
            'category_ids.*' => ['required', 'integer', 'distinct', 'exists:categories,id'],
        ]);
        $categoryIds = $validated['category_ids'];

        if (count($categoryIds) !== Category::count()) {
            throw ValidationException::withMessages([
                'category_ids' => 'The category order is incomplete. Refresh and try again.',
            ]);
        }

        DB::transaction(function () use ($categoryIds): void {
            foreach ($categoryIds as $position => $categoryId) {
                Category::query()
                    ->whereKey($categoryId)
                    ->update(['sort_order' => $position]);
            }
        });

        return redirect()->route('inventory.index')->with('success', 'Category order saved.');
    }

    public function uploadImage(Request $request, string $categorySlug): RedirectResponse
    {
        abort_unless($request->user()?->canManageOrders(), 403);

        $category = Category::query()->where('slug', $categorySlug)->firstOrFail();
        $request->validate([
            'image' => [
                'required',
                'file',
                'max:5120',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $imageInfo = $value instanceof UploadedFile && $value->isValid()
                        ? getimagesize($value->getRealPath())
                        : false;

                    if (! is_array($imageInfo) || ! in_array($imageInfo['mime'] ?? '', [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/bmp',
                        'image/webp',
                    ], true)) {
                        $fail('Please choose a valid JPG, PNG, GIF, BMP, or WEBP image.');
                    }
                },
            ],
        ], [
            'image.max' => 'The image must be 5 MB or smaller.',
        ]);

        $previousImage = $category->image_path;
        $category->update([
            'image_path' => $request->file('image')->store('categories', 'public'),
        ]);

        if ($previousImage) {
            Storage::disk('public')->delete($previousImage);
        }

        return back()->with('status', 'Photo uploaded for '.$category->name.'.');
    }

    public function uploadHomepageImage(Request $request, string $imageKey): RedirectResponse
    {
        abort_unless($request->user()?->canManageOrders(), 403);
        abort_unless(in_array($imageKey, ['hero-main', 'hero-secondary'], true), 404);

        $data = $request->validate([
            'image' => [
                'required',
                'file',
                'max:5120',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $imageInfo = $value instanceof UploadedFile && $value->isValid()
                        ? getimagesize($value->getRealPath())
                        : false;

                    if (! is_array($imageInfo) || ! in_array($imageInfo['mime'] ?? '', [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/bmp',
                        'image/webp',
                    ], true)) {
                        $fail('Please choose a valid JPG, PNG, GIF, BMP, or WEBP image.');
                    }
                },
            ],
        ], [
            'image.max' => 'The image must be 5 MB or smaller.',
        ]);

        $homepageImage = HomepageImage::firstOrNew(['key' => $imageKey]);
        $previousImage = $homepageImage->image_path;
        $homepageImage->image_path = $request->file('image')->store('homepage', 'public');
        $homepageImage->save();

        if ($previousImage) {
            Storage::disk('public')->delete($previousImage);
        }

        return back()->with('status', 'Homepage photo uploaded successfully.');
    }
}
