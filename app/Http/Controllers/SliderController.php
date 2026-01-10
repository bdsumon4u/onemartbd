<?php

namespace App\Http\Controllers;

use App\Media;
use App\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class SliderController extends Controller
{
    public function index()
    {
        $data = Slider::with('get_img')->get();
        return view('backEnd.admin.sliders.index', compact('data'));
    }

    public function store(Request $request)
    {
        if ($request->hasFile('slider_image')) {
            $uniq_id = uniqid();
            $destinationPath = public_path('uploads');
            $file1 = $request->file('slider_image');

            $org_file_name1 = $file1->getClientOriginalName();
            $file_name = $uniq_id . '_1445x365' . '.' . $file1->getClientOriginalExtension();

            $img = Image::make($file1->getRealPath());
            $img->resize(1445, 365, function (/*$constraint*/): void {
                /*$constraint->aspectRatio();*/
            })->save($destinationPath . '/' . $file_name, 95);


            $url = 'uploads/' . $file_name;

            $file_id = Media::create([
                'type' => 3,
                'file_original_name' => $org_file_name1,
                'file_url' => $url,
                'user_id' => Auth::guard('admin')->user()->id,
            ]);

            $url = $file_id->id;
        } else {
            return back()->with('error', 'No Image Selected');
        }

        Slider::create([
            'slider_image' => $url,
            'status' => $request->status,
        ]);

        return to_route('admin.sliders')->with('success', 'Slider Added Successfully');
    }

    public function update(Request $request)
    {
        if ($request->hasFile('slider_image')) {
            $uniq_id = uniqid();
            $destinationPath = public_path('uploads');
            $file1 = $request->file('slider_image');

            $org_file_name1 = $file1->getClientOriginalName();
            $file_name = $uniq_id . '_1445x365' . '.' . $file1->getClientOriginalExtension();

            $img = Image::make($file1->getRealPath());
            $img->resize(1445, 365, function (/*$constraint*/): void {
                /*$constraint->aspectRatio();*/
            })->save($destinationPath . '/' . $file_name, 95);


            $url = 'uploads/' . $file_name;

            $file_id = Media::create([
                'type' => 3,
                'file_original_name' => $org_file_name1,
                'file_url' => $url,
                'user_id' => Auth::guard('admin')->user()->id,
            ]);

            $url = $file_id->id;
        } else {
            $url = $request->slider_image_old;
        }

        Slider::find($request->id)->update([
            'slider_image' => $url,
            'status' => $request->status,
        ]);

        return to_route('admin.sliders')->with('success', 'Slider Added Successfully');
    }

    public function delete($id)
    {
        Slider::find($id)->delete();
        return back()->with('success', 'Slider Deleted Successfully');
    }
}
