<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\NoteSettings;
use Illuminate\Http\Request;

class NoteSettingsController extends Controller
{
    public function index()
    {
        $data = NoteSettings::get();

        return view('backEnd.admin.note_settings.index', compact('data'));
    }

    public function store(Request $request)
    {
        NoteSettings::create($request->all());

        return back()->with('success', 'Note Settings Added Successfully');
    }

    public function update(Request $request)
    {
        NoteSettings::find($request->id)->update($request->all());

        return back()->with('success', 'Note Settings Updated Successfully');
    }

    public function delete($id)
    {
        NoteSettings::find($id)->delete();

        return back()->with('success', 'Note Settings Deleted Successfully');

    }
}
