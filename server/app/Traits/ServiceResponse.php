<?php

namespace App\Traits;

trait ServiceResponse
{
    public function successResponse($data, $code = 200)
    {
        return response()->json([
            "success" => true,
            "payload" => $data
        ], $code);
    }

    public function errorResponse($error, $code)
    {
        return response()->json([
            "success" => false,
            "error" => $error
        ], $code);
    }
}
