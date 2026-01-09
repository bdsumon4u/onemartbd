<?php

namespace App\Http\Controllers;

use App\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use File;
use Intervention\Image\Facades\Image;

class MediaController extends Controller
{
    public function index()
    {
        $data = Media::where('user_id', Auth::guard('admin')->user()->id)->orderBy('id', 'desc')->paginate(25);
        return view('backEnd.admin.media.index', compact('data'));
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $org_file_name = $file->getClientOriginalName();

            $file_name = uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads');
            $file->move($destinationPath, $file_name);

            $url = 'uploads/' . $file_name;

            Media::create([
                'file_original_name' => $org_file_name,
                'file_url' => $url,
                'user_id' => Auth::guard('admin')->user()->id,
            ]);

            return back()->with('success', 'File Uploaded Successfully');
        } else {
            return back()->with('error', 'Please Select A File');
        }
    }

    public function update(Request $request)
    {
        if ($request->hasFile('file')) {
            $img_url = Media::find($request->id);
            if (file_exists(public_path($img_url->file_url))) {
                File::delete(public_path($img_url->file_url));
            }

            if ($img_url->type == 1) {
                $type_name = '_800x800';
                $height = 800;
                $width = 800;
            }

            if ($img_url->type == 2) {
                $type_name = '_180x180';
                $height = 180;
                $width = 180;
            }

            if ($img_url->type == 3) {
                $type_name = '_1110x280';
                $height = 280;
                $width = 1110;
            }

            $uniq_id = uniqid();
            $destinationPath = public_path('uploads');
            $file = $request->file('file');

            $org_file_name = $file->getClientOriginalName();

            if ($img_url->type == 0) {
                $file_name = $uniq_id . '.' . $file->getClientOriginalExtension();
            } else {
                $file_name = $uniq_id . $type_name . '.' . $file->getClientOriginalExtension();
            }

            if ($img_url->type == 0) {
                $file->move($destinationPath, $file_name);
            } else {
                $img = Image::make($file->getRealPath());
                $img->resize($width, $height, function (/*$constraint*/) {
                    /*$constraint->aspectRatio();*/
                })->save($destinationPath . '/' . $file_name, 90);
            }

            $url = 'uploads/' . $file_name;

            $img_url->update([
                'type' => $img_url->type,
                'file_original_name' => $org_file_name,
                'file_url' => $url,
                'user_id' => Auth::guard('admin')->user()->id,
            ]);


            return back()->with('success', 'File Updated Successfully');
        } else {
            return back()->with('error', 'Please Select A File');
        }
    }

    public function delete(Request $request, $id)
    {
        $img_url = Media::find($id);
        if (file_exists(public_path($img_url->file_url))) {
            File::delete(public_path($img_url->file_url));
        }

        $img_url->find($id)->delete();
        return back()->with('success', 'File Deleted Successfully');
    }
}
