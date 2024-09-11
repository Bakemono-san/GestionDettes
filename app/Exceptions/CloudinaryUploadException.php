<?php

namespace App\Exceptions;

use App\Enums\StateEnum;
use App\Traits\RestResponseTrait;
use Exception;
use Illuminate\Support\Facades\Log;

class CloudinaryUploadException extends Exception
{
    use RestResponseTrait;
    protected $file;

    public function __construct($message = "Cloudinary upload failed", $file = null)
    {
        parent::__construct($message);
        $this->file = $file;

        // Log the error details
        Log::error($message, [
            'file' => $this->file,
        ]);
    }

    /**
     * Report the exception.
     */
    public function report()
    {
        // Custom reporting logic can go here if needed
        Log::alert('CloudinaryUploadException occurred.', ['file' => $this->file]);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render($request)
    {
        return $this->sendResponse(null,StateEnum::ECHEC,"Cloudinary upload failed", 400);
    }
}
