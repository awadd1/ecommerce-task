<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            return $this->successResponse([
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ], 'User registered successfully', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            return $this->successResponse([
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ], 'Login successful');

        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), 'Login failed');
        } catch (\Exception $e) {
            return $this->errorResponse('Login failed: ' . $e->getMessage(), 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return $this->successResponse(null, 'Logged out successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed: ' . $e->getMessage(), 500);
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getAuthUser($request->user());

            return $this->successResponse(
                new UserResource($user),
                'User retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user: ' . $e->getMessage(), 500);
        }
    }
}
