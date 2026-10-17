<?php

namespace App\Services;

use App\Contracts\ImageServiceInterface;
use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ImageService implements ImageServiceInterface
{
    public function replace(Model $model, UploadedFile $file): Image
    {
        $existing = $model->image;
        $oldPath  = $existing?->path;
        $oldDisk  = $existing?->disk ?? 'public';

        $newPath = $file->store('images', 'public');

        try {
            $image = $model->image()->updateOrCreate([], [
                'path' => $newPath,
                'disk' => 'public',
            ]);
        } catch (Throwable $e) {
            Storage::disk('public')->delete($newPath);
            throw $e;
        }

        if ($oldPath && $oldPath !== $newPath && Storage::disk($oldDisk)->exists($oldPath)) {
            Storage::disk($oldDisk)->delete($oldPath);
        }

        return $image;
    }
}
