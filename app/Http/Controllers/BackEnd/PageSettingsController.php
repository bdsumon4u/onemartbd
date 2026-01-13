<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\PageSetting;
use Illuminate\Http\Request;

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

            return back()->with('success', 'Page Settings Updated Successfully');
        } catch (\Exception $e) {
            // dd($e);
            return back()->with('error', $e);
        }
    }
}
