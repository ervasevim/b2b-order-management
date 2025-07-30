<?php

namespace App\Http\Trait;

use Illuminate\Http\JsonResponse;

trait HttpResponseTrait
{
    public function success(array $data, string|array|null $message = null): JsonResponse
    {
        return new JsonResponse([
            'status' => 'success',
            'data' => $data,
            'message' => is_array($message) ? implode($message) : $message]);
    }

    public function error(array $data, int $code, string|array|null $message = null): JsonResponse
    {
        return new JsonResponse([
            'status' => 'false',
            'data' => $data,
            'message' => is_array($message) ? implode($message) : $message], $code);
    }

}
