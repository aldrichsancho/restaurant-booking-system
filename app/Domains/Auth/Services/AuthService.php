<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auth\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->userRepository->findByEmail($email);
        return ($user && Auth::attempt(['email' => $email, 'password' => $password]));
    }

    public function logout(): void
    {
        Auth::user()->tokens()->delete();
    }
}
