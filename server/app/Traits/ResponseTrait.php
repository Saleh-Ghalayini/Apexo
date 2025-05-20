<?php

namespace App\Traits;

trait ResponseTrait
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
            "error" => "An unexpected error occurred. Please try again later."
        ], $code);
    }
}
