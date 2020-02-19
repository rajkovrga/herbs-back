<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileExistsException;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ImageService
{

    protected $image;

    public function __construct(ImageManager $image)
    {
        $this->image = $image;
    }

    public function uploadImage($model, $imageForUpload,string $type = 'HERBS')
    {
        $ext = $imageForUpload->extension();
        $path = $this->createName($ext, $type);
        $img = $this->image->make($imageForUpload)->resize(250, 250);
        $img->stream();
        Storage::disk('local')->put($path, $img);
        if (!Storage::disk('local')->exists($path)) {
            throw new FileExistsException('File not saved', 400);
        }
        return $path;
    }

    public function changeImagePhoto($model, $imageForUpload, int $id, string $type = 'HERBS')
    {
        $ext = $imageForUpload->extension();
        $row = $model::query()->findOrFail($id);
        $old_image = $row->image_url;
        $path = $this->createName($ext, $type);
        $img = $this->image->make($imageForUpload)->resize(250, 250);
        $img->stream();
        Storage::disk('local')->put($path, $img);
        if (! Storage::disk('local')->exists($path)) {
            throw new FileExistsException('File not saved', 400);
        }
        $row->image_url = $path;
        $row->saveOrFail();
        if ($old_image != null) {
            Storage::disk('local')->delete($old_image);
        }
    }

    public function createName(string $ext, string $type = 'HERBS')
    {
        return 'IMG'. $type . hash('sha384', Carbon::now()->toDateTimeString()) . '.' . $ext;
    }

}
