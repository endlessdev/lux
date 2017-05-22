<?php

namespace App\Helpers;

class Response
{

    public static $DEFAULT_MESSAGE = [
        200 => 'Successfully processed request.',
        201 => 'Successfully created resources.',
        401 => 'Authentication failed.',
        410 => 'Resource has already been deleted.',
        404 => 'No resources found.',
        500 => 'An internal error has occurred.',
        403 => 'Not have enough permissions to process request.',
    ];


    public static function common(int $code, $data = null, $message = null)
    {
        return response()->json([
            'code' => $code,
            'message' => $message ?? Response::$DEFAULT_MESSAGE[$code],
            'data' => $data ?? []
        ], $code);
    }

}