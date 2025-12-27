<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    protected $disk;

    protected $url;

    public function __construct()
    {
        $this->disk = config('filesystems.default');
    }

    public function upload($file, $disk = null, $folder = 'uploads')
    {
        $disk = $disk ?: $this->disk;

        // Create unique filename: original-name_without_spaces_timestamp.extension
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Store and return relative path
        $path = $file->storeAs($folder, $filename, $disk);

        return $path;
    }

    public function delete($path, $disk = null)
    {
        $disk = $disk ?: $this->disk;

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
            return true;
        }

        return false;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
