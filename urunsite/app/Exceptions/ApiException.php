<?php

namespace App\Exceptions;

use Exception;
use App\Helpers\ApiResponse;

class ApiException extends Exception
{
    protected int $status;

    public function __construct(string $message, int $status = 500)
    {
        parent::__construct($message);
        $this->status = $status;
    }

    public function render($request)
    {
        return ApiResponse::error($this->getMessage(), $this->status);
    }
}
