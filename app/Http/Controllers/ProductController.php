<?php

namespace App\Http\Controllers;

use App\Attribute;
use App\Category;
use App\CategoryProduct;
use App\Employee;
use App\Media;
use App\OrderProduct;
use App\Product;
use App\ProductAttribute;
use App\ProductAttributeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        if ($query) {
            $data = Product::with('get_thumb', 'get_attributes', 'is_assigned')->where('name', 'LIKE', "%{$query}%")->orderBy('id', 'desc')->paginate(25);

        } else {
            $data = Product::with('get_thumb', 'get_attributes', 'is_assigned')->orderBy('id', 'desc')->paginate(25);
        }
        //$data = Product::with('get_thumb', 'get_attributes','is_assigned')->orderBy('id', 'desc')->paginate(25);
        $categories = Category::where('status', 1)->get();
        $attributes = Attribute::with('get_items')->where('status', 1)->get();
        $employees = Employee::where('status', 1)->pluck('name', 'id');
        //dd($data);
        return view('backEnd.admin.products.index', compact('data', 'categories', 'attributes', 'employees', 'query'));
    }
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $attributes = Attribute::with('get_items')->where('status', 1)->get();
        //dd($data);
        return view('backEnd.admin.products.create', compact('categories', 'attributes'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        /*$request->validate([
        'sku' => 'unique:products'
        ]);*/
        //feature image upload
        if ($request->hasFile('image')) {
            $uniq_id = uniqid();
            $destinationPath = public_path('uploads');
            $file1 = $request->file('image');

            $org_file_name1 = $file1->getClientOriginalName();
            $file_name = $uniq_id . '_800x800' . '.' . $file1->getClientOriginalExtension();
            $file_name2 = $uniq_id . '_400x400' . '.' . $file1->getClientOriginalExtension();

            $img = Image::make($file1->getRealPath());
            $img->resize(800, 800, function ( /*$constraint*/): void {
                /*$constraint->aspectRatio();*/
            })->save($destinationPath . '/' . $file_name, 70);

            $img->resize(400, 400, function ( /*$constraint*/): void {
                /*$constraint->aspectRatio();*/
            })->save($destinationPath . '/' . $file_name2, 70);

            $url = 'uploads/' . $file_name;
            $url2 = 'uploads/' . $file_name2;

            $file_id = Media::create([
                'type' => 1,
                'file_original_name' => $org_file_name1,
                'file_url' => $url,
                'user_id' => Auth::guard('admin')->check() ? Auth::guard('admin')->user()->id : Auth::guard('manager')->user()->id,
            ]);

            $file_id2 = Media::create([
                'type' => 2,
                'file_original_name' => $org_file_name1,
                'file_url' => $url2,
                'user_id' => Auth::guard('admin')->check() ? Auth::guard('admin')->user()->id : Auth::guard('manager')->user()->id,
            ]);

            $url = $file_id->id;
            $url2 = $file_id2->id;
        } else {
            $url = null;
            $url2 = null;
        }

        //gallery image upload
        $urls = '';
        if ($request->hasFile('gallery_image')) {
            foreach ($request->file('gallery_image') as $file) {
                $uniq_id = uniqid();
                $destinationPath = public_path('uploads');
                //$file = $request->file('gallery_image');

                $org_file_name1 = $file->getClientOriginalName();
                $file_name = $uniq_id . '_800x800' . '.' . $file->getClientOriginalExtension();

                $img = Image::make($file->getRealPath());
                $img->resize(800, 800, function ( /*$constraint*/): void {
                    /*$constraint->aspectRatio();*/
                })->save($destinationPath . '/' . $file_name, 70);

                $url3 = 'uploads/' . $file_name;

                $file_id = Media::create([
                    'type' => 1,
                    'file_original_name' => $org_file_name1,
                    'file_url' => $url3,
                    'user_id' => Auth::guard('admin')->check() ? Auth::guard('admin')->user()->id : Auth::guard('manager')->user()->id,
                ]);

                $urls .= ',' . $file_id->id;

            }
            $urls = substr($urls, 1);
        } else {
            $urls = null;
        }

        //generate slug
        $slug = Str::slug($request->name);
        if (Product::where('slug', $slug)->first()) {
            $slug .= '-1';
        }

        //insert data into product table
        $input = array_merge($request->all(), [
            'slug' => $slug,
            'thumb' => $url2,
            'image' => $url,
            'gallery_images' => $urls,
            'start_date' => $request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null,
            'end_date' => $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null,
        ]);
        // dd($input);
        $prod_id = Product::create($input);

        //insert categories
        foreach ($request->category_id as $cat) {
            DB::table('category_products')->insert([
                'category_id' => $cat,
                'product_id' => $prod_id->id,
            ]);
        }

        //insert attributes and its items
        if ($request->attribute_id) {
            foreach ($request->attribute_id as $att) {
                $att_id = ProductAttribute::create([
                    'product_id' => $prod_id->id,
                    'attribute_id' => $att,
                ]);
                if ($request->attribute_item_id) {
                    foreach ($request->attribute_item_id as $att_item) {
                        $aid = DB::table('attribute_items')->where('id', $att_item)->first();
                        if ($aid->attribute_id == $att) {
                            DB::table('product_attribute_items')->insert([
                                'product_attribute_id' => $att_id->id,
                                'product_attribute_item_id' => $att_item,
                            ]);
                        }
                    }
                }
            }
        }

        if (Auth::guard('admin')->check()) {
            return to_route('admin.product')->with('success', 'Product Added Successfully');
        } elseif (Auth::guard('manager')->check()) {
            return to_route('manager.product')->with('success', 'Product Added Successfully');
        } else {
            return back()->with('warning', 'Something Went Wrong');
        }

    }

    public function edit($id)
    {
        $data = Product::with('get_thumb', 'get_attributes')->find($id);
        //dd($data);
        $categories = Category::where('status', 1)->pluck('category_name', 'id');
        $p_c = $data->get_categories()->pluck('category_name', 'categories.id');
        $prod_cat = '';
        foreach ($p_c as $key => $item) {
            $prod_cat .= ',' . $key;
        }
        $prod_cat = substr($prod_cat, 1);
        //dd($prod_cat);
        $attributes = Attribute::with('get_items')->where('status', 1)->get();
        if ($data->get_attributes->count() > 0) {
            $prd_attr = '';
            $prd_attr_item = '';
            foreach ($data->get_attributes as $attr) {
                $prd_attr .= ',' . $attr->attribute_id;

                foreach ($attr->get_attribute_items as $attr_item) {
                    $prd_attr_item .= ',' . $attr_item->product_attribute_item_id;
                }

            }
            $prd_attr = substr($prd_attr, 1);
            $prd_attr_item = substr($prd_attr_item, 1);
            //dd($prd_attr);
        } else {
            $prd_attr = null;
            $prd_attr_item = null;
        }
        //dd($prd_attr_item);
        //dd($data->get_categories()->pluck('category_name','categories.id'));
        return view('backEnd.admin.products.edit', compact('data', 'categories', 'prod_cat', 'attributes', 'prd_attr', 'prd_attr_item'));
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        if ($request->hasFile('image')) {
            $uniq_id = uniqid();
            $destinationPath = public_path('uploads');
            $file1 = $request->file('image');

            $org_file_name1 = $file1->getClientOriginalName();
            $file_name = $uniq_id . '_800x800' . '.' . $file1->getClientOriginalExtension();
            $file_name2 = $uniq_id . '_400x400' . '.' . $file1->getClientOriginalExtension();

            $img = Image::make($file1->getRealPath());
            $img->resize(800, 800, function ( /*$constraint*/): void {
                /*$constraint->aspectRatio();*/
            })->save($destinationPath . '/' . $file_name, 70);

            $img->resize(400, 400, function ( /*$constraint*/): void {
                /*$constraint->aspectRatio();*/
            })->save($destinationPath . '/' . $file_name2, 70);

            $url = 'uploads/' . $file_name;
            $url2 = 'uploads/' . $file_name2;

            $file_id = Media::create([
                'type' => 1,
                'file_original_name' => $org_file_name1,
                'file_url' => $url,
                'user_id' => Auth::guard('admin')->check() ? Auth::guard('admin')->user()->id : Auth::guard('manager')->user()->id,
            ]);

            $file_id2 = Media::create([
                'type' => 2,
                'file_original_name' => $org_file_name1,
                'file_url' => $url2,
                'user_id' => Auth::guard('admin')->check() ? Auth::guard('admin')->user()->id : Auth::guard('manager')->user()->id,
            ]);

            $url = $file_id->id;
            $url2 = $file_id2->id;
        } else {
            $url = $request->image_old;
            $url2 = $request->thumb_old;
        }

        $urls = '';
        if ($request->hasFile('gallery_image')) {
            foreach ($request->file('gallery_image') as $file) {
                $uniq_id = uniqid();
                $destinationPath = public_path('uploads');
                //$file = $request->file('gallery_image');

                $org_file_name1 = $file->getClientOriginalName();
                $file_name = $uniq_id . '_800x800' . '.' . $file->getClientOriginalExtension();

                $img = Image::make($file->getRealPath());
                $img->resize(800, 800, function ( /*$constraint*/): void {
                    /*$constraint->aspectRatio();*/
                })->save($destinationPath . '/' . $file_name, 70);

                $url3 = 'uploads/' . $file_name;

                $file_id = Media::create([
                    'type' => 1,
                    'file_original_name' => $org_file_name1,
                    'file_url' => $url3,
                    'user_id' => Auth::guard('admin')->check() ? Auth::guard('admin')->user()->id : Auth::guard('manager')->user()->id,
                ]);

                $urls .= ',' . $file_id->id;

            }
            $urls = substr($urls, 1);
        } else {
            $urls = $request->gallery_images_old;
        }

        //generate slug
        $slug = Str::slug($request->name);
        if (Product::where('slug', $slug)->first()) {
            $slug .= '-1';
        }

        $input = array_merge($request->all(), [
            'slug' => $slug,
            'thumb' => $url2,
            'image' => $url,
            'gallery_images' => $urls,
            'start_date' =>$request->start_date ? date('Y-m-d', strtotime($request->start_date)) : null,
            'end_date' => $request->end_date ? date('Y-m-d', strtotime($request->end_date)) : null,
        ]);

        Product::find($id)->update($input);

        //update data into pivot table
        Product::find($id)->get_categories()->sync($request->category_id);

        //delete attributes and its items
        $prod = Product::with('get_attributes')->find($id);
        foreach ($prod->get_attributes as $att) {
            ProductAttributeItem::where('product_attribute_id', $att->id)->delete();
            ProductAttribute::where([['product_id', $id], ['attribute_id', $att->attribute_id]])->delete();
        }

        //insert attributes and its items
        if ($request->attribute_id) {
            foreach ($request->attribute_id as $att) {
                $att_id = ProductAttribute::create([
                    'product_id' => $id,
                    'attribute_id' => $att,
                ]);
                if ($request->attribute_item_id) {
                    foreach ($request->attribute_item_id as $att_item) {
                        $aid = DB::table('attribute_items')->where('id', $att_item)->first();
                        if ($aid->attribute_id == $att) {
                            DB::table('product_attribute_items')->insert([
                                'product_attribute_id' => $att_id->id,
                                'product_attribute_item_id' => $att_item,
                            ]);
                        }
                    }
                }
            }
        }

        if (Auth::guard('admin')->check()) {
            return to_route('admin.product')->with('success', 'Product Updated Successfully');
        } elseif (Auth::guard('manager')->check()) {
            return to_route('manager.product')->with('success', 'Product Updated Successfully');
        } else {
            return back()->with('warning', 'Something Went Wrong');
        }
    }

    public function delete($id)
    {
        $has_prod = OrderProduct::where('product_id', $id)->first();
        if ($has_prod) {
            return back()->with('warning', 'This Product Already In Order');
        } else {
            Product::find($id)->get_categories()->detach();
            Product::find($id)->delete();
            return back()->with('success', 'Product Deleted Successfully');
        }
    }

    //duplicate
    public function duplicate($product)
    {
        $product = Product::with('get_categories', 'get_attributes', 'is_assigned')->findOrFail($product);
        $new_product = $product->replicate();
        $new_product->name = $product->name . ' (Duplicated)';
        $new_product->slug = $product->slug . '_' . $product->id++;
        $new_product->sku = $product->sku . '_' . $product->id++;
        $new_product->save();

        // Replicate categories
        if ($product->get_categories) {
            foreach ($product->get_categories as $category) {
                DB::table('category_products')->insert([
                    'category_id' => $category->id,
                    'product_id' => $new_product->id,
                ]);
            }
        }

        //replicate attributes
        if ($product->get_attributes) {
            foreach ($product->get_attributes as $attribute) {
                $new_attribute = $attribute->replicate();
                $new_attribute->product_id = $new_product->id;
                $new_attribute->save();
                foreach ($attribute->get_attribute_items as $attribute_item) {
                    $new_attribute_item = $attribute_item->replicate();
                    $new_attribute_item->product_attribute_id = $new_attribute->id;
                    $new_attribute_item->save();
                }

            }
        }

        //replicate assign
        if ($product->is_assigned) {
            $new_assigned = $product->is_assigned->replicate();
            $new_assigned->product_id = $new_product->id;
            $new_assigned->save();
        }

        return back()->with('success', 'Product Duplicate Successfully');
        // dd($product);
    }

    /*public function skuCheck(Request $request)
    {
    $product = Product::where('sku', $request->sku)->first();
    if ($product) {
    $status = 'found';
    } else {
    $status = 'not_found';
    }
    return response()->json($status);
    }*/

    public function bulkDelete(Request $request)
    {
        foreach (explode(',', $request->all_id) as $item) {
            $has_prod = OrderProduct::where('product_id', $item)->first();
            if ($has_prod) {
                return back()->with('warning', 'This Product Already In Order');
            } else {
                CategoryProduct::where('product_id', $item)->delete();

                $prd_attr_ids = ProductAttribute::where('product_id', $item)->get();
                foreach ($prd_attr_ids as $ids) {
                    ProductAttributeItem::where('product_attribute_id', $ids->id)->delete();
                }

                ProductAttribute::where('product_id', $item)->delete();
                Product::find($item)->delete();
            }
        }
        return back()->with('success', 'Deleted Successfully');
    }

    public function bulkStatus(Request $request)
    {
        //dd($request->all());
        foreach (explode(',', $request->all_id) as $item) {
            Product::find($item)->update([
                'status' => $request->status,
            ]);
        }
        return back()->with('success', 'Status Updated Successfully');
    }

    public function positionUpdate(Request $request)
    {
        //dd($request->all());
        try {
            $product = Product::find($request->product_id);
            $product->update([
                'position' => $request->position,
            ]);
            return true;
        } catch (\Exception) {
            return false;
        }

    }
}
