<?php
namespace App\Exceptions;

use App\Enums\StateEnum;
use App\Traits\RestResponseTrait;
use Exception;
use Illuminate\Support\Facades\Log;

class CloudinaryUploadException extends Exception
{
    use RestResponseTrait;


    public function __construct(string $message = "Cloudinary upload failed")
    {
        parent::__construct($message);
    }

    /**
     * Report the exception.
     */
    public function report()
    {
        // Custom reporting logic can go here if needed
        Log::alert('CloudinaryUploadException occurred.');
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render($request)
    {
        return $this->sendResponse(null, StateEnum::ECHEC, "Cloudinary upload failed", 400);
    }
}
