<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success(string $message, $data = null, int $status = 200)
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data'    => $data
    ], $status);
}


    public static function error(string $message, $errors = null, int $status = 500)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}
