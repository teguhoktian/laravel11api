<?php

namespace App;

use Illuminate\Http\JsonResponse;

class APIResponseBuilder
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function success(
        $data = [],
        $message = "The operation was successful.",
        $code = 200,
        $additional = []
    ): JsonResponse {
        return self::buildResponse(true, $message, $data, $code, $additional);
    }

    public static function error(
        $message = "An error has occurred. Please try again.",
        $errors = [],
        $code = 400,
        $additional = []
    ): JsonResponse {
        return self::buildResponse(false, $message, [], $code, $additional, $errors);
    }

    public static function invalidData(
        $message = "The provided data is invalid or incorrect.",
        $errors = [],
        $code = 422,
        $additional = []
    ): JsonResponse {
        return self::error($message, $errors, $code, $additional);
    }

    public static function notFound(
        $message = "The requested resource could not be found.",
        $code = 404,
        $additional = []
    ): JsonResponse {
        return self::error($message, [], $code, $additional);
    }

    public static function unauthorized(
        $message = "Unauthorized access detected. Access is denied.",
        $code = 401,
        $additional = []
    ): JsonResponse {
        return self::error($message, [], $code, $additional);
    }

    protected static function buildResponse($success, $message, $data, $code, $additional, $errors = []): JsonResponse
    {
        $response = [
            'success' => $success,
            'code' => $code,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        if (!empty($additional)) {
            $response = array_merge($response, $additional);
        }

        return response()->json($response, $code);
    }
}
