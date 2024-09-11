<?php

namespace App\Services;

use App\Contracts\UploadImageServiceInt;
use App\Exceptions\CloudinaryUploadException;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Cloudinary;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class UploadImageService implements UploadImageServiceInt
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }
    public function uploadImage($file)
    {
        $filePath = $file->store('photos', 'public');
        return base64_encode(Storage::get('public/' .
            $filePath));
    }

    public function withCloudinary($file)
    {
        try {
            if (!file_exists($file)) {
                Log::error('File does not exist', [
                    "File" => $file,
                ]);
            }

            $result = $this->cloudinary->uploadApi()->upload($file, [
                'folder' => 'profiles',
                'public_id' => uniqid(),
            ]);

            Log::info('file uploaded successfully', ['result' => $result]);

            return $result['secure_url'];
        } catch (Exception $e) {
            // Log the exception details
            Log::error('Cloudinary upload failed: ', [
                'exception' => $e->getMessage(),
            ]);

            $originalFilename = pathinfo($file, PATHINFO_BASENAME);
            $filePath = Storage::disk('public')->putFileAs('relance', $file, $originalFilename);

            Log::info('File saved to relance folder:', [
                'file' => $filePath,
            ]);


            return null;
        }
    }

    public function getImage($path)
    {
        return Storage::get($path);
    }
}
