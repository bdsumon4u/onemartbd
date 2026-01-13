<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeItem;
use App\Models\Media;
use App\Models\WebSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            if ($request->hasFile('website_header_logo')) {
                $file = $request->file('website_header_logo');
                $org_file_name = $file->getClientOriginalName();

                $file_name = uniqid().'.'.$file->getClientOriginalExtension();
                $destinationPath = public_path('uploads');
                $file->move($destinationPath, $file_name);

                $url = 'uploads/'.$file_name;

                $file_id = Media::create([
                    'file_original_name' => $org_file_name,
                    'file_url' => $url,
                    'user_id' => Auth::guard('admin')->user()->id,
                ]);

                $url = $file_id->id;

            } else {
                $url = $request->website_header_logo_old;
            }

            if ($request->hasFile('website_favicon')) {
                $file2 = $request->file('website_favicon');
                $org_file_name2 = $file2->getClientOriginalName();

                $file_name2 = uniqid().'.'.$file2->getClientOriginalExtension();
                $destinationPath2 = public_path('uploads');
                $file2->move($destinationPath2, $file_name2);

                $url2 = 'uploads/'.$file_name2;

                $file_id2 = Media::create([
                    'file_original_name' => $org_file_name2,
                    'file_url' => $url2,
                    'user_id' => Auth::guard('admin')->user()->id,
                ]);

                $url2 = $file_id2->id;

            } else {
                $url2 = $request->website_favicon_old;
            }

            if ($request->is_order_confirm_sms) {
                $is_order_confirm_sms = 1;
            } else {
                $is_order_confirm_sms = 0;
            }

            $input = array_merge($request->all(), [
                'website_header_logo' => $url,
                'website_favicon' => $url2,
                'is_order_confirm_sms' => $is_order_confirm_sms,
            ]);

            WebSettings::find(1)->update($input);

            return back()->with('success', 'Website Settings Updated Successfully');
        } catch (\Exception $e) {
            dd($e);

            return back()->with('error', $e);
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

        return back()->with('success', 'Website Settings Updated Successfully');
    }
}
