<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class SliderController extends Controller
{
    public function index()
    {
        $data = Slider::with('get_img')->latest()->get();

        return view('backEnd.admin.sliders.index', compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request, true);

        $mediaId = $this->uploadSliderImage($validated['slider_image']);

        Slider::create([
            'slider_image' => $mediaId,
            'status' => (int) $validated['status'],
        ]);

        return to_route('admin.sliders')->with('success', 'Slider Added Successfully');
    }

    public function update(Request $request)
    {
        $validated = $this->validatePayload($request, false);
        $slider = Slider::query()->findOrFail((int) $validated['id']);

        $sliderImage = $validated['slider_image'] instanceof UploadedFile
            ? $this->uploadSliderImage($validated['slider_image'])
            : $request->input('slider_image_old', $slider->slider_image);

        $slider->update([
            'slider_image' => $sliderImage,
            'status' => (int) $validated['status'],
        ]);

        return to_route('admin.sliders')->with('success', 'Slider Added Successfully');
    }

    public function delete(int $id)
    {
        Slider::query()->findOrFail($id)->delete();

        return back()->with('success', 'Slider Deleted Successfully');
    }

    private function validatePayload(Request $request, bool $requireImage): array
    {
        $rules = [
            'status' => ['required', 'in:0,1'],
            'slider_image' => [$requireImage ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ];

        if (! $requireImage) {
            $rules['id'] = ['required', 'integer', 'exists:sliders,id'];
        }

        return $request->validate($rules);
    }

    private function uploadSliderImage(UploadedFile $file): int
    {
        $fileName = uniqid().'_1445x365'.'.'.$file->getClientOriginalExtension();
        $destinationPath = public_path('uploads');

        Image::make($file->getRealPath())
            ->resize(1445, 365, function (): void {
                // Intentionally keep explicit dimensions.
            })
            ->save($destinationPath.'/'.$fileName, 95);

        $media = Media::create([
            'type' => 3,
            'file_original_name' => $file->getClientOriginalName(),
            'file_url' => 'uploads/'.$fileName,
            'user_id' => Auth::guard('admin')->id(),
        ]);

        return $media->id;
    }
}
