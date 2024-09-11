<?php

namespace App\Exceptions;

use App\Enums\StateEnum;
use App\Traits\RestResponseTrait;
use Exception;
use Illuminate\Support\Facades\Log;

class DatabaseRequestException extends Exception
{
    use RestResponseTrait;
    protected $query;
    protected $bindings;

    public function __construct($message = "Database request failed", $query = null, $bindings = [])
    {
        parent::__construct($message);
        $this->query = $query;
        $this->bindings = $bindings;

        // Log the error details
        Log::error($message, [
            'query' => $this->query,
            'bindings' => $this->bindings,
        ]);
    }

    /**
     * Report the exception.
     */
    public function report()
    {
        // Custom reporting logic (like sending alerts) can go here if needed
        Log::alert('DatabaseRequestException occurred.', [
            'query' => $this->query,
            'bindings' => $this->bindings,
        ]);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render()
    {
        return $this->sendResponse(null,StateEnum::ECHEC,"Database request failed", 400);
    }
}
