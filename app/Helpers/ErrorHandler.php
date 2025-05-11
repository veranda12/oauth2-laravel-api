<?php
// app/Helpers/ErrorHandler.php

namespace App\Helpers;

use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use app\Exceptions\UnexpectedFieldException;
use Throwable;

class ErrorHandler
{
    // Method untuk menangani error validasi secara global
    public static function handleValidationError(ValidationException $e)
    {
        if ($e instanceof UnexpectedFieldException) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'extra_fields' => $e->getExtraFields(),
            ], 422);
        }
        return response()->json([
            'error' => 'Validation Error',
            'messages' => $e->errors(),
        ], 422);
    }

    // Method untuk menangani kesalahan umum
    public static function handleGeneralError(Throwable $e)
    {
        return response()->json([
            'error' => 'Internal Server Error',
            'message' => $e->getMessage(),
        ], 500);
    }
}
