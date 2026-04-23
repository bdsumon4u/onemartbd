<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryProduct;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    public function index()
    {
        $data = Category::tree();

        return view('backEnd.admin.categories.index', compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:255'],
            'parent' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $validated['image'] = $this->uploadCategoryImage($request->file('image'));

        Category::create($validated);

        return back()->with('success', 'Category Added Successfully');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:categories,id'],
            'category_name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $category = Category::findOrFail((int) $validated['id']);

        $imagePath = $this->uploadCategoryImage($request->file('image'));
        if ($imagePath) {
            $validated['image'] = $imagePath;
        } else {
            unset($validated['image']);
        }

        unset($validated['id']);

        $category->update($validated);

        return back()->with('success', 'Category Updated Successfully');
    }

    public function delete($id)
    {
        if (CategoryProduct::where('category_id', $id)->exists()) {
            return back()->with('error', 'This Category Already In Product');
        } else {
            Category::find($id)->delete();

            return back()->with('success', 'Category Deleted Successfully');
        }
    }

    private function uploadCategoryImage(?UploadedFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        $destinationPath = public_path('uploads/categories');
        if (! is_dir($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $filename = Str::uuid()->toString().'_category.'.$file->getClientOriginalExtension();

        $image = Image::make($file->getRealPath());
        $image->fit(400, 300, function ($constraint): void {
            $constraint->upsize();
        })->save($destinationPath.'/'.$filename, 80);

        return 'uploads/categories/'.$filename;
    }
}
