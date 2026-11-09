<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $auth)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->auth->attempt(
            $request->validated('email'),
            $request->validated('password'),
            [User::ROLE_OWNER, User::ROLE_CUSTOMER]
        );

        return $this->session($user);
    }

    public function adminLogin(LoginRequest $request): JsonResponse
    {
        $user = $this->auth->attempt(
            $request->validated('email'),
            $request->validated('password'),
            [User::ROLE_ADMIN]
        );

        return $this->session($user);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->auth->registerCustomer($request->validated());

        return $this->session($user, 201);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()->load('shop')),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());

        return response()->json(['message' => 'Signed out.']);
    }

    private function session(User $user, int $status = 200): JsonResponse
    {
        return response()->json([
            'token' => $this->auth->issueToken($user),
            'user' => new UserResource($user->load('shop')),
        ], $status);
    }
}
