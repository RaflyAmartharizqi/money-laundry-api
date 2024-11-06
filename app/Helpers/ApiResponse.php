<?php

namespace App\Helpers;

use App\Constants\Messages;

class ApiResponse
{
    public static function success($message = Messages::SUCCESS_CREATED, $statusCode = 200, $data = null)
    {
        return response()->json([
            'status' => true,
            'statusCode' => $statusCode,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public static function error($message =null, $statusCode = null, $errors = [])
    {
        return response()->json([
            'status' => false,
            'statusCode' => $statusCode,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
}
