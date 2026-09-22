<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BaseApiController extends Controller
{
    /**
     * Standard Success Response
     */
    protected function success($data = null, string $message = 'Success', array $meta = [], int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'serverTime' => now()->toISOString(),
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $status);
    }

    /**
     * Standard Paginated Response
     */
    protected function paginated($paginator, $items = null, string $message = 'Success'): JsonResponse
    {
        if ($paginator instanceof CursorPaginator) {
            $meta = [
                'limit' => $paginator->perPage(),
                'cursor' => $paginator->cursor()?->encode(),
                'nextCursor' => $paginator->nextCursor()?->encode(),
                'prevCursor' => $paginator->previousCursor()?->encode(),
                'hasMore' => $paginator->hasMorePages(),
            ];
            $data = $items !== null ? $items : $paginator->items();
        } elseif ($paginator instanceof LengthAwarePaginator) {
            $meta = [
                'currentPage' => $paginator->currentPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
                'lastPage' => $paginator->lastPage(),
                'hasMore' => $paginator->hasMorePages(),
            ];
            $data = $items !== null ? $items : $paginator->items();
        } else {
            $data = $items !== null ? $items : $paginator;
            $meta = [
                'total' => count($data),
                'hasMore' => false
            ];
        }

        return $this->success($data, $message, $meta);
    }

    /**
     * Standard Error Response
     */
    protected function error(string $message = 'An error occurred', string $code = 'BAD_REQUEST', int $status = 400, array $fieldErrors = [], bool $retryable = false): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'fieldErrors' => (object) $fieldErrors,
                'retryable' => $retryable,
            ],
            'serverTime' => now()->toISOString(),
        ], $status);
    }

    /**
     * Convert Dollars / Decimal to integer minor units (Cents)
     */
    protected function toMinorUnits(float|int|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    /**
     * Convert minor units (Cents) to Major float
     */
    protected function fromMinorUnits(int $cents): float
    {
        return round($cents / 100, 2);
    }
}
