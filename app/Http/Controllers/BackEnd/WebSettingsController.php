<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeItem;
use App\Models\Media;
use App\Models\WebSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WebSettingsController extends Controller
{
    public function index()
    {
        $data = WebSettings::find(1);

        return view('backEnd.admin.web_settings', compact('data'));
    }

    public function update(Request $request)
    {
        try {
            $headerLogoId = $this->handleUpload($request, 'website_header_logo', $request->website_header_logo_old);
            $faviconId = $this->handleUpload($request, 'website_favicon', $request->website_favicon_old);

            WebSettings::find(1)?->update(array_merge($request->all(), [
                'website_header_logo' => $headerLogoId,
                'website_favicon' => $faviconId,
                'is_order_confirm_sms' => $request->boolean('is_order_confirm_sms'),
                'is_order_defender_enabled' => $request->boolean('is_order_defender_enabled'),
                'auto_block_ip_on_limit' => $request->boolean('auto_block_ip_on_limit'),
                'auto_flag_fake_on_limit' => $request->boolean('auto_flag_fake_on_limit'),
            ]));

            Artisan::call('optimize:clear');

            return back()->with('success', 'Website Settings Updated Successfully');
        } catch (\Exception $e) {
            Log::error('Web settings update failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Something went wrong while updating settings');
        }
    }

    // attribute div
    public function attribute()
    {
        $data = Attribute::with('get_items')->get();

        // dd($data);
        return view('backEnd.admin.attribute_settings.index', compact('data'));
    }

    public function attributeStore(Request $request)
    {
        Attribute::create($request->all());

        return back()->with('success', 'Attribute Added Successfully');
    }

    public function attributeUpdate(Request $request)
    {
        Attribute::find($request->id)->update($request->all());

        return back()->with('success', 'Attribute Updated Successfully');
    }

    public function attributeDelete($id)
    {
        Attribute::find($id)->delete();
        AttributeItem::where('attribute_id', $id)->delete();

        return back()->with('success', 'Attribute Deleted Successfully');
    }

    public function attributeItemStore(Request $request)
    {
        AttributeItem::create($request->all());

        return back()->with('success', 'Attribute Item Added Successfully');
    }

    public function attributeItemUpdate(Request $request)
    {
        AttributeItem::find($request->id)->update($request->all());

        return back()->with('success', 'Attribute Item Updated Successfully');
    }

    public function attributeItemDelete($id)
    {
        AttributeItem::find($id)->delete();

        return back()->with('success', 'Attribute Item Deleted Successfully');
    }

    public function colorSettings(Request $request)
    {
        $data = WebSettings::find(1);

        return view('backEnd.admin.color_settings', compact('data'));
    }

    public function colorSettingsUpdate(Request $request)
    {
        WebSettings::find(1)->update($request->all());

        Artisan::call('optimize:clear');

        return back()->with('success', 'Website Settings Updated Successfully');
    }

    private function handleUpload(Request $request, string $field, $fallback = null)
    {
        if (! $request->hasFile($field)) {
            return $fallback;
        }

        $file = $request->file($field);
        $originalName = $file->getClientOriginalName();
        $fileName = uniqid().'.'.$file->getClientOriginalExtension();

        $file->move(public_path('uploads'), $fileName);
        $url = 'uploads/'.$fileName;

        return Media::create([
            'file_original_name' => $originalName,
            'file_url' => $url,
            'user_id' => Auth::guard('admin')->id(),
        ])->id;
    }
}
