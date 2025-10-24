<?php

namespace App\Shared\Base;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiTrait
{
    protected function data(
        mixed $data = [],
        string $message = 'Success',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function success(
        string $message = 'Success',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'status' => true,
            'message' => $message,
        ], $status);
    }

    protected function error(
        string $message = 'Something went wrong.',
        int $status = 400,
        string $code = 'error',
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'status' => false,
            'message' => $message,
            'code' => $code,
            'errors' => $errors,
        ], $status);
    }

    protected function created(mixed $data = [], string $message = 'Created'): JsonResponse
    {
        return $this->data($data, $message, 201);
    }

    protected function notFound(string $message = 'Not Found'): JsonResponse
    {
        return $this->error($message, 404, 'not_found');
    }

    protected function validationError(array $errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->error($message, 422, 'validation_error', $errors);
    }

    protected function paginate(LengthAwarePaginator $paginator, string $message = 'Success'): JsonResponse
    {
        return $this->data(
            $paginator->items(),
            $message,
            200,
            [
                'total'        => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'last_page'    => $paginator->lastPage(),
            ]
        );
    }
}
