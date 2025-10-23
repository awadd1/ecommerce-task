<?php

namespace App\Traits;
use Illuminate\Http\JsonResponse;
trait ApiResponse
{
  protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
  {
    return response()->json([
      'success' => true,
      'message' => $message,
      'data' => $data,
    ], $code);
  }


  protected function errorResponse(string $message = 'Error occurred', int $code = 400, $errors = null): JsonResponse
  {
    $response = [
      'success' => false,
      'message' => $message,
    ];

    if ($errors !== null) {
      $response['errors'] = $errors;
    }

    return response()->json($response, $code);
  }

  protected function validationErrorResponse($errors, string $message = 'Validation failed'): JsonResponse
  {
    return $this->errorResponse($message, 422, $errors);
  }

  protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
  {
    return $this->errorResponse($message, 404);
  }

  protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
  {
    return $this->errorResponse($message, 401);
  }

  protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
  {
    return $this->errorResponse($message, 403);
  }
}