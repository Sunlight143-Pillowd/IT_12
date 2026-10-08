<?php

namespace App\Http\Controllers;

use App\Models\Category;
<<<<<<< HEAD
use App\Models\HomepageImage;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
=======
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
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

<<<<<<< HEAD
        if (Category::query()->where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            throw ValidationException::withMessages([
                'name' => 'A category with this name already exists.',
            ]);
        }

=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
        $category->update([
            'name' => $name,
            'slug' => $slug,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Category updated successfully.');
    }
<<<<<<< HEAD

    public function destroy(Category $category): RedirectResponse
    {
        $imagePath = $category->image_path;

        $category->delete();

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('inventory.index')->with('success', 'Category deleted successfully.');
    }

    public function uploadImage(Request $request, string $categorySlug): RedirectResponse
    {
        abort_unless($request->user()?->canManageOrders(), 403);

        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:100'],
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

        abort_unless(Str::slug($data['category_name']) === $categorySlug, 404);

        $category = Category::firstOrCreate(
            ['slug' => $categorySlug],
            ['name' => trim($data['category_name'])]
        );

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
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
}
