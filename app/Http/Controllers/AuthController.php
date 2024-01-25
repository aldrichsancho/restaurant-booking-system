<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Services\AuthService;
use App\Domains\Auth\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;
    protected $userService;

    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return commonResponse(400, 'Failed to register', ['errors' => $validator->errors()]);
        }

        if (!$this->authService->attempt($request->email, $request->password)) {
            return commonResponse(401, 'Invalid credentials');
        }

        $user = Auth::user();
        $token = $user->createToken('api')->plainTextToken;

        return commonResponse(200, 'Successfully login', compact('token'));
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return commonResponse(400, 'Failed to register', ['errors' => $validator->errors()]);
        }

        $user = $this->userService->createUser($request->all());

        // Log in the newly registered user
        $token = $this->authService->attempt($request->email, $request->password);

        return commonResponse(200, 'Successfully register', compact('token'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return commonResponse(200, 'Successfully logged out');
    }
}
