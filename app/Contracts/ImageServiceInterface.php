<?php

namespace App\Contracts;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

interface ImageServiceInterface
{
    public function replace(Model $model, UploadedFile $file): Image;
}
