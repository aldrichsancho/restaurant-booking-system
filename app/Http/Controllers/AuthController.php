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
            return response()->json([
                'status' => 400,
                'message' => "Failed to login",
                "errors" => $validator->errors()
            ], 400);
        }

        if (!$this->authService->attempt($request->email, $request->password)) {
            return response()->json([
                'status' => 401,
                'message' => "Invalid credentials"
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'status' => 200,
            'message' => "Successfully login",
            'token' => $token
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to register',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $this->userService->createUser($request->all());

        // Log in the newly registered user
        $token = $this->authService->attempt($request->email, $request->password);

        return response()->json([
            'status' => 200,
            'message' => "Successfully register",
            'token' => $token
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Successfully logged out'
        ]);
    }
}
