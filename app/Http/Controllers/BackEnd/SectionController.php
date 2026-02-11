<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function index(): View
    {
        $sections = Section::withCount('products')
            ->orderBy('sort_order')
            ->get();

        return view('backEnd.admin.sections.index', compact('sections'));
    }

    public function create(): View
    {
        return view('backEnd.admin.sections.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:0,1'],
        ]);

        $maxSortOrder = Section::max('sort_order') ?? 0;

        Section::create([
            'name' => $validated['name'],
            'status' => (int) $validated['status'],
            'sort_order' => $maxSortOrder + 1,
        ]);

        return to_route('admin.sections')->with('success', 'Section Created Successfully');
    }

    public function edit(int $id): View
    {
        $section = Section::findOrFail($id);

        return view('backEnd.admin.sections.edit', compact('section'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:0,1'],
        ]);

        $section = Section::findOrFail($id);
        $section->update([
            'name' => $validated['name'],
            'status' => (int) $validated['status'],
        ]);

        return to_route('admin.sections')->with('success', 'Section Updated Successfully');
    }

    public function delete(int $id): RedirectResponse
    {
        Section::findOrFail($id)->delete();

        return back()->with('success', 'Section Deleted Successfully');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'exists:sections,id'],
        ]);

        foreach ($validated['order'] as $position => $sectionId) {
            Section::where('id', $sectionId)->update(['sort_order' => $position + 1]);
        }

        return back()->with('success', 'Sections Reordered Successfully');
    }

    public function products(int $id): View
    {
        $section = Section::with(['products' => function ($query): void {
            $query->with('get_thumb');
        }])->findOrFail($id);

        return view('backEnd.admin.sections.products', compact('section'));
    }

    public function addProduct(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $section = Section::findOrFail($id);

        if ($section->products()->where('product_id', $validated['product_id'])->exists()) {
            return back()->with('error', 'Product already exists in this section');
        }

        $maxSortOrder = $section->products()->max('section_products.sort_order') ?? 0;

        $section->products()->attach($validated['product_id'], [
            'sort_order' => $maxSortOrder + 1,
        ]);

        return back()->with('success', 'Product Added Successfully');
    }

    public function removeProduct(int $id, int $productId): RedirectResponse
    {
        $section = Section::findOrFail($id);
        $section->products()->detach($productId);

        return back()->with('success', 'Product Removed Successfully');
    }

    public function reorderProducts(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'exists:products,id'],
        ]);

        $section = Section::findOrFail($id);

        foreach ($validated['order'] as $position => $productId) {
            $section->products()->updateExistingPivot($productId, ['sort_order' => $position + 1]);
        }

        return back()->with('success', 'Products Reordered Successfully');
    }

    public function searchProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = $request->input('q', '');

        $products = Product::query()
            ->where('status', 1)
            ->where(function ($q) use ($query): void {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with('get_thumb')
            ->take(15)
            ->get(['id', 'name', 'sku', 'thumb', 'price', 'sale_price']);

        return response()->json($products);
    }
}
