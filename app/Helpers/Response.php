<?php

namespace App\Helpers;

class Response
{
    public static function commonResponse(string $message, $data, int $code)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'code' => $code
        ], $code);
    }

}