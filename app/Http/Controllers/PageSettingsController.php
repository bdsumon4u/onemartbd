<?php

namespace App\Http\Controllers;

use App\Attribute;
use App\AttributeItem;
use App\Category;
use App\HomeProduct;
use App\Media;
use App\PageSetting;
use App\WebSettings;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Auth;

class PageSettingsController extends Controller
{
    public function index()
    {
        $data = PageSetting::find(1);
        return view('backEnd.admin.page_settings', compact('data'));
    }

    public function update(Request $request)
    {
        try {
            PageSetting::find(1)->update($request->all());
            return redirect()->back()->with('success', 'Page Settings Updated Successfully');
        } catch (\Exception $e) {
            //dd($e);
            return redirect()->back()->with('error', $e);
        }
    }
}
