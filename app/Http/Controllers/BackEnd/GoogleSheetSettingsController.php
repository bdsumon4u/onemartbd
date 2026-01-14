<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\GoogleSheetSettings;
use Illuminate\Http\Request;

class GoogleSheetSettingsController extends Controller
{
    // attribute div
    public function index()
    {
        $data = GoogleSheetSettings::find(1);

        return view('backEnd.admin.google_sheet_settings', compact('data'));
    }

    public function update(Request $request)
    {
        GoogleSheetSettings::find(1)->update($request->all());

        return back()->with('success', 'Google Sheet Settings Updated Successfully');
    }
}
