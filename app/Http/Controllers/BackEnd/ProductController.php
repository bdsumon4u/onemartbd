<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Employee;
use App\Models\Media;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeItem;
use App\Services\ProductForwardingService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function __construct(private ProductForwardingService $productForwardingService) {}

    public function index(Request $request)
    {
        $query = $request->input('query');
        $data = Product::with('get_thumb', 'get_attributes', 'is_assigned')
            ->when($query, fn ($q) => $q->where('name', 'LIKE', "%{$query}%"))
            ->orderByDesc('id')
            ->paginate(25);
        // $data = Product::with('get_thumb', 'get_attributes','is_assigned')->orderBy('id', 'desc')->paginate(25);
        $categories = Category::where('status', 1)->get();
        $attributes = Attribute::with('get_items')->where('status', 1)->get();
        $employees = Employee::where('status', 1)->pluck('name', 'id');

        // dd($data);
        return view('backEnd.admin.products.index', compact('data', 'categories', 'attributes', 'employees', 'query'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $attributes = Attribute::with('get_items')->where('status', 1)->get();

        // dd($data);
        return view('backEnd.admin.products.create', compact('categories', 'attributes'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $userId = $this->getAuthenticatedUserId();
        if (! $userId) {
            return back()->with('warning', 'Something Went Wrong');
        }

        [$imageId, $thumbId] = $this->uploadFeatureImages($request->file('image'), $userId);
        $galleryIds = $this->uploadGalleryImages($request->file('gallery_image', []), $userId);

        $slug = $this->uniqueSlugForName((string) $request->name);

        // insert data into product table
        $input = array_merge($request->all(), [
            'slug' => $slug,
            'thumb' => $thumbId,
            'image' => $imageId,
            'gallery_images' => $galleryIds,
            'start_date' => $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null,
            'end_date' => $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null,
        ]);
        // dd($input);
        $product = DB::transaction(function () use ($input, $request) {
            $product = Product::create($input);

            $categoryIds = (array) $request->input('category_id', []);
            $product->get_categories()->sync($categoryIds);

            $this->syncAttributesAndItems($product->id, (array) $request->input('attribute_id', []), (array) $request->input('attribute_item_id', []));

            return $product;
        });

        // Forward product to master (if configured)
        try {
            if ($product instanceof Product) {
                $this->productForwardingService->forwardIfConfigured($product);
            }
        } catch (\Throwable $e) {
            // don't block UI on forwarding failures
            report($e);
        }

        $route = $this->productIndexRouteName();
        if (! $route) {
            return back()->with('warning', 'Something Went Wrong');
        }

        return to_route($route)->with('success', 'Product Added Successfully');
    }

    public function edit($id)
    {
        $data = Product::with('get_thumb', 'get_attributes.get_attribute_items')->findOrFail($id);
        // dd($data);
        $categories = Category::where('status', 1)->pluck('category_name', 'id');

        $prod_cat = $data->get_categories()->pluck('categories.id')->implode(',') ?: null;
        // dd($prod_cat);
        $attributes = Attribute::with('get_items')->where('status', 1)->get();

        $prd_attr = $data->get_attributes->pluck('attribute_id')->implode(',') ?: null;
        $prd_attr_item = $data->get_attributes
            ->flatMap(fn ($attr) => $attr->get_attribute_items->pluck('product_attribute_item_id'))
            ->implode(',') ?: null;

        // dd($prd_attr_item);
        // dd($data->get_categories()->pluck('category_name','categories.id'));
        return view('backEnd.admin.products.edit', compact('data', 'categories', 'prod_cat', 'attributes', 'prd_attr', 'prd_attr_item'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $product = Product::findOrFail($id);
        $userId = $this->getAuthenticatedUserId();
        if (! $userId) {
            return back()->with('warning', 'Something Went Wrong');
        }

        if ($request->hasFile('image')) {
            [$imageId, $thumbId] = $this->uploadFeatureImages($request->file('image'), $userId);
        } else {
            $imageId = $request->image_old;
            $thumbId = $request->thumb_old;
        }

        $galleryIds = $request->hasFile('gallery_image')
            ? $this->uploadGalleryImages($request->file('gallery_image', []), $userId)
            : $request->gallery_images_old;

        $slug = $this->uniqueSlugForName((string) $request->name, (int) $product->id);

        $input = array_merge($request->all(), [
            'slug' => $slug,
            'thumb' => $thumbId,
            'image' => $imageId,
            'gallery_images' => $galleryIds,
            'start_date' => $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null,
            'end_date' => $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null,
        ]);

        DB::transaction(function () use ($product, $input, $request): void {
            $product->update($input);

            $categoryIds = (array) $request->input('category_id', []);
            $product->get_categories()->sync($categoryIds);

            $this->deleteAttributesAndItems($product->id);
            $this->syncAttributesAndItems($product->id, (array) $request->input('attribute_id', []), (array) $request->input('attribute_item_id', []));
        });

        // try forwarding update to master
        try {
            $this->productForwardingService->forwardIfConfigured($product);
        } catch (\Throwable $e) {
            report($e);
        }

        $route = $this->productIndexRouteName();
        if (! $route) {
            return back()->with('warning', 'Something Went Wrong');
        }

        return to_route($route)->with('success', 'Product Updated Successfully');
    }

    public function delete($id)
    {
        if (OrderProduct::where('product_id', $id)->exists()) {
            return back()->with('warning', 'This Product Already In Order');
        }

        $product = Product::findOrFail($id);
        $product->get_categories()->detach();
        $product->delete();

        return back()->with('success', 'Product Deleted Successfully');
    }

    // duplicate
    public function duplicate($product)
    {
        $product = Product::with('get_categories', 'get_attributes.get_attribute_items', 'is_assigned')->findOrFail($product);
        $new_product = $product->replicate();
        $new_product->name = $product->name.' (Duplicated)';
        $new_product->slug = $product->slug.'_'.$product->id;
        $new_product->sku = $product->sku.'_'.$product->id;
        $new_product->save();

        // Replicate categories
        $new_product->get_categories()->sync($product->get_categories->pluck('id')->all());

        // replicate attributes
        foreach ($product->get_attributes as $attribute) {
            $newAttribute = $attribute->replicate();
            $newAttribute->product_id = $new_product->id;
            $newAttribute->save();

            $newItems = $attribute->get_attribute_items->map(function (ProductAttributeItem $item) use ($newAttribute) {
                return [
                    'product_attribute_id' => $newAttribute->id,
                    'product_attribute_item_id' => $item->product_attribute_item_id,
                ];
            })->all();

            if (! empty($newItems)) {
                DB::table('product_attribute_items')->insert($newItems);
            }
        }

        // replicate assign
        if ($product->is_assigned) {
            $new_assigned = $product->is_assigned->replicate();
            $new_assigned->product_id = $new_product->id;
            $new_assigned->save();
        }

        return back()->with('success', 'Product Duplicate Successfully');
        // dd($product);
    }

    public function retryForwarding(int $id)
    {
        $product = Product::findOrFail($id);

        try {
            $this->productForwardingService->retryForwarding($product);

            return back()->with('success', 'Forwarding retried.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('warning', 'Retry failed: '.$e->getMessage());
        }
    }

    public function filterNonForwarded()
    {
        $searchQuery = request('query');

        $data = Product::with('get_thumb', 'get_attributes', 'is_assigned')
            ->when($searchQuery, fn ($q) => $q->where('name', 'LIKE', "%{$searchQuery}%")->orWhere('sku', 'LIKE', "%{$searchQuery}%"))
            ->whereNull('master_id')
            ->where(function ($q) {
                $q->whereNull('forwarding_status')
                    ->orWhere('forwarding_status', 'failed')
                    ->orWhere('forwarding_status', 'pending');
            })
            ->orderByDesc('id')
            ->paginate(25);

        $categories = Category::where('status', 1)->get();
        $attributes = Attribute::with('get_items')->where('status', 1)->get();
        $employees = Employee::where('status', 1)->pluck('name', 'id');

        return view('backEnd.admin.products.index', compact('data', 'categories', 'attributes', 'employees', 'searchQuery'))->with('query', $searchQuery);
    }

    public function bulkForwardToMaster(Request $request)
    {
        $productIds = $request->input('product_ids');

        // Handle both JSON string and array formats
        if (is_string($productIds)) {
            $productIds = json_decode($productIds, true);
        }

        if (! is_array($productIds) || empty($productIds)) {
            return back()->with('error', 'No products selected.');
        }

        \App\Jobs\BulkForwardProductsToMaster::dispatch($productIds);

        return back()->with('success', 'Products queued for forwarding to master. Processing will start shortly.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = array_values(array_filter(array_map('intval', explode(',', (string) $request->all_id))));
        if (empty($ids)) {
            return back()->with('warning', 'No Product Selected');
        }

        if (OrderProduct::whereIn('product_id', $ids)->exists()) {
            return back()->with('warning', 'This Product Already In Order');
        }

        DB::transaction(function () use ($ids): void {
            CategoryProduct::whereIn('product_id', $ids)->delete();

            $attributeIds = ProductAttribute::whereIn('product_id', $ids)->pluck('id');
            if ($attributeIds->isNotEmpty()) {
                ProductAttributeItem::whereIn('product_attribute_id', $attributeIds)->delete();
            }

            ProductAttribute::whereIn('product_id', $ids)->delete();
            Product::whereIn('id', $ids)->delete();
        });

        return back()->with('success', 'Deleted Successfully');
    }

    public function bulkStatus(Request $request)
    {
        // dd($request->all());
        $ids = array_values(array_filter(array_map('intval', explode(',', (string) $request->all_id))));
        if (! empty($ids)) {
            Product::whereIn('id', $ids)->update([
                'status' => $request->status,
            ]);
        }

        return back()->with('success', 'Status Updated Successfully');
    }

    public function positionUpdate(Request $request)
    {
        // dd($request->all());
        $product = Product::find($request->product_id);
        if (! $product) {
            return false;
        }

        $product->update([
            'position' => $request->position,
        ]);

        return true;

    }

    private function productIndexRouteName(): ?string
    {
        if (Auth::guard('admin')->check()) {
            return 'admin.product';
        }

        if (Auth::guard('manager')->check()) {
            return 'manager.product';
        }

        return null;
    }

    private function getAuthenticatedUserId(): ?int
    {
        if (Auth::guard('admin')->check()) {
            return (int) Auth::guard('admin')->id();
        }

        if (Auth::guard('manager')->check()) {
            return (int) Auth::guard('manager')->id();
        }

        return null;
    }

    private function uniqueSlugForName(string $name, ?int $ignoreProductId = null): string
    {
        $slug = Str::slug($name);

        $query = Product::query()->where('slug', $slug);
        if ($ignoreProductId) {
            $query->where('id', '!=', $ignoreProductId);
        }

        if ($query->exists()) {
            $slug .= '-1';
        }

        return $slug;
    }

    /**
     * @return array{0:int|null,1:int|null}
     */
    private function uploadFeatureImages(?UploadedFile $file, ?int $userId): array
    {
        if (! $file) {
            return [null, null];
        }

        $uniqId = uniqid();
        $destinationPath = public_path('uploads');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $file800 = $uniqId.'_800x800'.'.'.$extension;
        $file400 = $uniqId.'_400x400'.'.'.$extension;

        $img = Image::make($file->getRealPath());
        $img->resize(800, 800, function (/* $constraint */): void {
            /* $constraint->aspectRatio(); */
        })->save($destinationPath.'/'.$file800, 70);

        $img->resize(400, 400, function (/* $constraint */): void {
            /* $constraint->aspectRatio(); */
        })->save($destinationPath.'/'.$file400, 70);

        $image = Media::create([
            'type' => 1,
            'file_original_name' => $originalName,
            'file_url' => 'uploads/'.$file800,
            'user_id' => $userId,
        ]);

        $thumb = Media::create([
            'type' => 2,
            'file_original_name' => $originalName,
            'file_url' => 'uploads/'.$file400,
            'user_id' => $userId,
        ]);

        return [$image->id, $thumb->id];
    }

    private function uploadGalleryImages(array $files, ?int $userId): ?string
    {
        if (empty($files)) {
            return null;
        }

        $destinationPath = public_path('uploads');
        $ids = [];

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $uniqId = uniqid();
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $file800 = $uniqId.'_800x800'.'.'.$extension;

            $img = Image::make($file->getRealPath());
            $img->resize(800, 800, function (/* $constraint */): void {
                /* $constraint->aspectRatio(); */
            })->save($destinationPath.'/'.$file800, 70);

            $media = Media::create([
                'type' => 1,
                'file_original_name' => $originalName,
                'file_url' => 'uploads/'.$file800,
                'user_id' => $userId,
            ]);

            $ids[] = $media->id;
        }

        return empty($ids) ? null : implode(',', $ids);
    }

    private function deleteAttributesAndItems(int $productId): void
    {
        $attributeIds = ProductAttribute::where('product_id', $productId)->pluck('id');
        if ($attributeIds->isNotEmpty()) {
            ProductAttributeItem::whereIn('product_attribute_id', $attributeIds)->delete();
        }

        ProductAttribute::where('product_id', $productId)->delete();
    }

    private function syncAttributesAndItems(int $productId, array $attributeIds, array $attributeItemIds): void
    {
        $attributeIds = array_values(array_filter(array_map('intval', $attributeIds)));
        if (empty($attributeIds)) {
            return;
        }

        $attributeItemIds = array_values(array_filter(array_map('intval', $attributeItemIds)));

        $itemsByAttribute = collect();
        if (! empty($attributeItemIds)) {
            $itemsByAttribute = DB::table('attribute_items')
                ->select('id', 'attribute_id')
                ->whereIn('id', $attributeItemIds)
                ->get()
                ->groupBy('attribute_id');
        }

        $pivotRows = [];
        foreach ($attributeIds as $attributeId) {
            $productAttribute = ProductAttribute::create([
                'product_id' => $productId,
                'attribute_id' => $attributeId,
            ]);

            foreach (($itemsByAttribute[$attributeId] ?? collect()) as $item) {
                $pivotRows[] = [
                    'product_attribute_id' => $productAttribute->id,
                    'product_attribute_item_id' => $item->id,
                ];
            }
        }

        if (! empty($pivotRows)) {
            DB::table('product_attribute_items')->insert($pivotRows);
        }
    }
}
