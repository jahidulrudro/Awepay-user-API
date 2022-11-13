<?php

namespace App\Http\Transformers\Api;

use Illuminate\Http\JsonResponse;

class ResponseTransformer
{
    /**
     * success response method.
     * @param mixed $result
     *  @param string $message
     *  @param ineger statuscode
     * @return \Illuminate\Http\ResponseJsonResponse
     */

    public static function success($result, string $message, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * return error response.
     * @param string $error
     *  @param array $errorMessages
     *  @param ineger statuscode
     *  @return \Illuminate\Http\ResponseJsonResponse
     */
    public static function error(string $error, $errorMessages = [], int $statusCode = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $statusCode);
    }
}
