<?php

namespace App\Helpers;

class ApiResponseHelper
{
    /* **************************************** Static **************************************** */
    public static function error($errors = [], $message = 'Error', $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }

    public static function success($data = [], $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);
    }
}
