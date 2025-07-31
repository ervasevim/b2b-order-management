<?php

namespace App\Http\Trait;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait HttpResponse
{
    public function success(array|ResourceCollection $data, string|array|null $message = null, int $code = 200): JsonResponse
    {
        return new JsonResponse([
            'status' => 'success',
            'data' => $data,
            'message' => is_array($message) ? implode($message) : $message], $code);
    }

    public function error(array $data, int $code, string|array|null $userMessage = null, string|array|null $errorMessage = null): JsonResponse
    {
        return new JsonResponse([
            'status' => 'false',
            'data' => $data,
            'message' => is_array($userMessage) ? implode($userMessage) : $userMessage,
            'errorMessage' => is_array($errorMessage) ? implode($errorMessage) : $errorMessage,
        ], $code);
    }

    public function unauthorized(int $code, string|array|null $message = null): JsonResponse
    {
        return new JsonResponse([
            'status' => 'false',
            'message' => is_array($message) ? implode($message) : $message], $code);
    }

}
