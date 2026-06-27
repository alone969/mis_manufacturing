<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;

class Controller
{
    /**
     * Log an activity for the current user.
     */
    protected function logActivity(
        string $action,
        string $description,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $properties = null
    ): void {
        ActivityLog::log(
            user: request()->user(),
            action: $action,
            description: $description,
            entityType: $entityType,
            entityId: $entityId,
            properties: $properties
        );
    }

    /**
     * Return a JSON success response.
     */
    protected function successResponse(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return a JSON error response.
     */
    protected function errorResponse(string $message = 'Error', int $status = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
