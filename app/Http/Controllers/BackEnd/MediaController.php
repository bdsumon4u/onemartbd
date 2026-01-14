<?php

namespace App\Http\Controllers\BackEnd;

use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Intervention\Image\Facades\Image;

class MediaController extends Controller
{
    public function index(): View
    {
        $data = Media::query()
            ->where('user_id', $this->adminId())
            ->latest()
            ->paginate(25);

        return view('backEnd.admin.media.index', compact('data'));
    }

    public function store(StoreMediaRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $stored = $this->storeFile($file, MediaType::Original);

        Media::query()->create([
            'type' => MediaType::Original->value,
            'file_original_name' => $file->getClientOriginalName(),
            'file_url' => $stored,
            'user_id' => $this->adminId(),
        ]);

        return back()->with('success', 'File Uploaded Successfully');
    }

    public function update(UpdateMediaRequest $request): RedirectResponse
    {
        $media = Media::query()->findOrFail($request->validated()['id']);
        $this->deleteFromDisk($media->file_url);

        $type = MediaType::tryFrom((int) $media->type) ?? MediaType::Original;
        $file = $request->file('file');
        $stored = $this->storeFile($file, $type);

        $media->update([
            'type' => $type->value,
            'file_original_name' => $file->getClientOriginalName(),
            'file_url' => $stored,
            'user_id' => $this->adminId(),
        ]);

        return back()->with('success', 'File Updated Successfully');
    }

    public function delete(int $id): RedirectResponse
    {
        $media = Media::query()->findOrFail($id);
        $this->deleteFromDisk($media->file_url);
        $media->delete();

        return back()->with('success', 'File Deleted Successfully');
    }

    private function adminId(): int
    {
        return (int) Auth::guard('admin')->id();
    }

    private function storeFile(UploadedFile $file, MediaType $type): string
    {
        $destinationPath = public_path('uploads');
        File::ensureDirectoryExists($destinationPath);

        $fileName = uniqid().$type->fileSuffix().'.'.$file->getClientOriginalExtension();
        $fullPath = $destinationPath.'/'.$fileName;

        $dimensions = $type->dimensions();
        if ($dimensions === null) {
            $file->move($destinationPath, $fileName);

            return 'uploads/'.$fileName;
        }

        Image::make($file->getRealPath())
            ->resize($dimensions['width'], $dimensions['height'], function (): void {})
            ->save($fullPath, 90);

        return 'uploads/'.$fileName;
    }

    private function deleteFromDisk(?string $relativePath): void
    {
        if (! is_string($relativePath) || $relativePath === '') {
            return;
        }

        $fullPath = public_path($relativePath);
        if (file_exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
