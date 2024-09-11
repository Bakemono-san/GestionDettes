<?php

namespace App\Exceptions;

use Exception;
use App\Traits\RestResponseTrait;
use App\Enums\StateEnum;
use Illuminate\Support\Facades\Log;

class ValidationException extends Exception
{
    use RestResponseTrait;

    protected $errors;

    public function __construct($errors = [], $message = "Validation failed", $code = 411)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;

        // Log the validation error details
        Log::error('Validation Exception: ' . $message, [
            'errors' => $this->errors,
        ]);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render()
    {
        return $this->sendResponse(
            ["erreur" => $this->errors],
            StateEnum::ECHEC,
            "Erreur de validation",
            411
        );
    }
}
