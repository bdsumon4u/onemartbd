<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class LandingPageController extends Controller
{
    public function index()
    {
        $landingPages = LandingPage::with('product', 'bannerMedia')
            ->orderBy('id', 'desc')
            ->paginate(25);

        return view('backEnd.admin.landing-pages.index', compact('landingPages'));
    }

    public function create()
    {
        return view('backEnd.admin.landing-pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:landing_pages,slug|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'about_section_head' => 'nullable|string|max:255',
            'about_section_body' => 'nullable',
            'gallery_section_head' => 'nullable|string|max:255',
            'why_section_head' => 'nullable|string|max:255',
            'why_section_body' => 'nullable',
            'review_section_head' => 'nullable|string|max:255',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userId = Auth::id();

        DB::transaction(function () use ($request, $userId): void {
            // Upload banner image if provided
            $bannerImageId = null;
            if ($request->hasFile('banner_image')) {
                $bannerImageId = $this->uploadBannerImage($request->file('banner_image'), $userId);
            }

            // Upload gallery images if provided
            $galleryImageIds = null;
            if ($request->hasFile('gallery_images')) {
                $galleryImageIds = $this->uploadGalleryImages($request->file('gallery_images'), $userId);
            }

            // Upload review images if provided
            $reviewImageIds = null;
            if ($request->hasFile('review_images')) {
                $reviewImageIds = $this->uploadGalleryImages($request->file('review_images'), $userId);
            }

            // Use custom slug or generate from title
            $slug = $request->filled('slug')
                ? $request->slug
                : $this->generateUniqueSlug($request->title);

            // Create landing page
            LandingPage::create([
                'product_id' => $request->product_id,
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'slug' => $slug,
                'banner_image' => $bannerImageId,
                'about_section_head' => $request->about_section_head,
                'about_section_body' => $request->about_section_body,
                'gallery_images' => $galleryImageIds,
                'gallery_section_head' => $request->gallery_section_head ?: 'Gallery Images',
                'why_section_head' => $request->why_section_head,
                'why_section_body' => $request->why_section_body,
                'review_images' => $reviewImageIds,
                'review_section_head' => $request->review_section_head,
                'status' => $request->has('status'),
            ]);
        });

        return redirect()->route('landing-pages.index')->with('success', 'Landing Page Created Successfully');
    }

    public function edit(LandingPage $landingPage)
    {
        $landingPage->load('product', 'bannerMedia');

        return view('backEnd.admin.landing-pages.edit', compact('landingPage'));
    }

    public function update(Request $request, LandingPage $landingPage)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:landing_pages,slug,'.$landingPage->id.'|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'about_section_head' => 'nullable|string|max:255',
            'about_section_body' => 'nullable',
            'gallery_section_head' => 'nullable|string|max:255',
            'why_section_head' => 'nullable|string|max:255',
            'why_section_body' => 'nullable',
            'review_section_head' => 'nullable|string|max:255',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userId = Auth::id();

        DB::transaction(function () use ($request, $landingPage, $userId): void {
            // Handle banner image upload
            $bannerImageId = $landingPage->banner_image;
            if ($request->hasFile('banner_image')) {
                $bannerImageId = $this->uploadBannerImage($request->file('banner_image'), $userId);
            }

            // Handle gallery images upload
            $galleryImageIds = $landingPage->gallery_images;
            if ($request->hasFile('gallery_images')) {
                $galleryImageIds = $this->uploadGalleryImages($request->file('gallery_images'), $userId);
            }

            // Handle review images upload
            $reviewImageIds = $landingPage->review_images;
            if ($request->hasFile('review_images')) {
                $reviewImageIds = $this->uploadGalleryImages($request->file('review_images'), $userId);
            }

            // Use custom slug, or regenerate if title changed
            $slug = $landingPage->slug;
            if ($request->filled('slug') && $request->slug !== $landingPage->slug) {
                $slug = $request->slug;
            } elseif ($request->title !== $landingPage->title && ! $request->filled('slug')) {
                $slug = $this->generateUniqueSlug($request->title, $landingPage->id);
            }

            // Update landing page
            $landingPage->update([
                'product_id' => $request->product_id,
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'slug' => $slug,
                'banner_image' => $bannerImageId,
                'about_section_head' => $request->about_section_head,
                'about_section_body' => $request->about_section_body,
                'gallery_images' => $galleryImageIds,
                'gallery_section_head' => $request->gallery_section_head ?: 'Gallery Images',
                'why_section_head' => $request->why_section_head,
                'why_section_body' => $request->why_section_body,
                'review_images' => $reviewImageIds,
                'review_section_head' => $request->review_section_head,
                'status' => $request->has('status'),
            ]);
        });

        return redirect()->route('landing-pages.index')->with('success', 'Landing Page Updated Successfully');
    }

    public function destroy(LandingPage $landingPage)
    {
        $landingPage->delete();

        return redirect()->route('landing-pages.index')->with('success', 'Landing Page Deleted Successfully');
    }

    public function duplicate(LandingPage $landingPage)
    {
        $newSlug = $this->generateUniqueSlug($landingPage->title);

        $clone = $landingPage->replicate(['id', 'created_at', 'updated_at']);
        $clone->slug = $newSlug;
        $clone->save();

        return redirect()->route('landing-pages.edit', $clone)->with('success', 'Landing Page Duplicated Successfully');
    }

    // Frontend method to display landing page
    public function show($slug)
    {
        $landingPage = LandingPage::with(['product.get_thumb', 'product.get_image', 'bannerMedia'])
            ->where(['slug' => $slug, 'status' => true])
            ->firstOrFail();

        // Determine if product has active promotion (free delivery)
        $product = $landingPage->product;
        $isFreeDelivery = $product->start_date && $product->end_date;

        // Load shipping methods for non-free delivery
        $shippingMethods = \App\Models\ShippingMethod::where('status', true)->get();

        return view('frontEnd.landing-page', compact('landingPage', 'isFreeDelivery', 'shippingMethods'));
    }

    // API endpoint for product selection
    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');

        $products = Product::where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('sku', 'LIKE', "%{$search}%");
            })
            ->select('id', 'name', 'sku', 'price', 'sale_price')
            ->limit(20)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->name.' (SKU: '.$product->sku.')',
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                ];
            });

        return response()->json([
            'results' => $products,
        ]);
    }

    private function uploadBannerImage(?UploadedFile $file, ?int $userId): ?int
    {
        if (! $file) {
            return null;
        }

        $uniqId = uniqid();
        $destinationPath = public_path('uploads');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = $uniqId.'_banner_800x400.'.$extension;

        $img = Image::make($file->getRealPath());
        $img->resize(800, 400, function ($constraint): void {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($destinationPath.'/'.$filename, 80);

        $media = Media::create([
            'type' => 1,
            'file_original_name' => $originalName,
            'file_url' => 'uploads/'.$filename,
            'user_id' => $userId,
        ]);

        return $media->id;
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
            $filename = $uniqId.'_gallery_600x600.'.$extension;

            $img = Image::make($file->getRealPath());
            $img->resize(600, 600, function ($constraint): void {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save($destinationPath.'/'.$filename, 80);

            $media = Media::create([
                'type' => 1,
                'file_original_name' => $originalName,
                'file_url' => 'uploads/'.$filename,
                'user_id' => $userId,
            ]);

            $ids[] = $media->id;
        }

        return empty($ids) ? null : implode(',', $ids);
    }

    // Order placement method for landing pages
    public function placeOrder(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'landing_page_id' => 'required|exists:landing_pages,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => ['required', 'min:11', 'max:11'],
            'customer_address' => 'required|string',
            'qty' => 'required|integer|min:1|max:10',
        ], [
            'customer_phone.required' => 'অনুগ্রহ করে আপনার মোবাইল নাম্বারটি দিন',
            'customer_phone.min' => 'আপনার মোবাইল নাম্বারটি সঠিক নয়',
            'customer_phone.max' => 'আপনার মোবাইল নাম্বারটি সঠিক নয়',
            'customer_name.required' => 'অনুগ্রহ করে আপনার নাম দিন',
            'customer_address.required' => 'অনুগ্রহ করে আপনার ঠিকানা দিন',
        ]);

        // Load required models
        $product = \App\Models\Product::findOrFail($request->product_id);
        $landingPage = LandingPage::findOrFail($request->landing_page_id);

        $ip = $this->getClientIP($request);

        // Check IP restrictions
        if (! $this->checkIPAllowed($ip)) {
            return redirect()->back()->with('success', 'অর্ডারটি সফলভাবে সম্পন্ন হয়েছে');
        }

        return DB::transaction(function () use ($request, $product, $ip) {
            // Create or get customer
            $customer = $this->getOrCreateCustomer($request);
            if (! $customer) {
                return redirect()->back()->with('success', 'অর্ডারটি সফলভাবে সম্পন্ন হয়েছে');
            }

            // Generate invoice ID
            $invoiceId = $this->generateInvoiceId();

            // Calculate pricing
            $unitPrice = $product->sale_price > 0 && $product->sale_price < $product->price
                ? $product->sale_price
                : $product->price;
            $quantity = (int) $request->qty;
            $subTotal = $unitPrice * $quantity;

            // Determine shipping cost
            $isFreeDelivery = $product->start_date && $product->end_date;
            $shippingCost = 0;
            $shippingMethodId = null;

            if (! $isFreeDelivery && $request->filled('shipping_method')) {
                $shippingMethod = \App\Models\ShippingMethod::find($request->shipping_method);
                $shippingCost = $shippingMethod?->amount ?? 0;
                $shippingMethodId = $shippingMethod?->id;
            }

            $total = $subTotal + $shippingCost;

            // Create order
            $order = \App\Models\Order::create([
                'invoice_id' => $invoiceId,
                'order_date' => Date::now()->toDateString(),
                'customer_id' => $customer->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'sub_total' => $subTotal,
                'shipping_method' => $shippingMethodId,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'due' => $total,
                'status' => 2, // Pending status
                'ip_address' => $ip,
                'source' => 'landing_page',
                'utm_source' => 'landing_page',
            ]);

            // Add order product
            \App\Models\OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'qty' => $quantity,
                'price' => $unitPrice,
                'purchase_cost' => $product->purchase_cost ?? 0,
            ]);

            // Assign employee (if exists)
            $this->assignRandomEmployee($order);

            // Handle notifications (only for non-test environments)
            if (! str_ends_with($request->getHost(), '.test')) {
                $sms = \App\Models\SmsSetting::where('status', $order->status)->first();
                if ($sms) {
                    // You can implement notification service here if needed
                }
            }

            $orderInfo = [
                'name' => $customer->name,
                'order_id' => $order->invoice_id,
                'total' => $order->total,
            ];

            return redirect()->route('confirm.order')
                ->with('success', 'অর্ডারটি সফলভাবে সম্পন্ন হয়েছে')
                ->with('order_info', $orderInfo);
        });
    }

    private function getClientIP(Request $request): string
    {
        $ip = $request->server('HTTP_CLIENT_IP')
            ?? $request->server('HTTP_X_FORWARDED_FOR')
            ?? $request->server('REMOTE_ADDR');

        return $ip === '::1' ? gethostname() : $ip;
    }

    private function checkIPAllowed(string $ip): bool
    {
        $checkIp = DB::table('i_p_s')->where('ip_address', $ip)->first();

        if ($checkIp && $checkIp->status == 1) {
            return false;
        }

        if (! $checkIp) {
            \App\Models\IP::create(['ip_address' => $ip]);
        }

        return true;
    }

    private function getOrCreateCustomer(Request $request): ?\App\Models\User
    {
        $checkCus = \App\Models\User::where('phone', $request->customer_phone)->first();

        if ($checkCus) {
            return $checkCus->status == 1 ? $checkCus : null;
        }

        return \App\Models\User::create([
            'name' => $request->customer_name,
            'phone' => $request->customer_phone,
            'address' => $request->customer_address,
            'password' => Hash::make($request->customer_phone),
        ]);
    }

    private function generateInvoiceId(): string
    {
        if (\App\Models\Order::withTrashed()->count() > 0) {
            $invoiceId = \App\Models\Order::withTrashed()->latest('id')->first()->invoice_id;
            $invoiceId = trim((string) $invoiceId, 'INV');
            $invoiceId++;

            return 'INV'.$invoiceId;
        }

        return 'INV1';
    }

    private function assignRandomEmployee(\App\Models\Order $order): ?int
    {
        // Get available employees
        $employees = \App\Models\Employee::where('status', 1)->pluck('id')->toArray();

        if (empty($employees)) {
            return null;
        }

        $employeeId = $employees[array_rand($employees)];

        \App\Models\OrderAssign::create([
            'employee_id' => $employeeId,
            'order_id' => $order->id,
        ]);

        return $employeeId;
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = LandingPage::where('slug', $slug);

            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (! $query->exists()) {
                break;
            }

            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
