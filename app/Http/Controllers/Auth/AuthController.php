<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\Auth\AuthRepositoryInterface;

class AuthController extends Controller
{
    private $repository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->repository = $authRepository;
    }

    public function checkToken()
    {
        return response()->json([
            'success' => true,
        ], 200);
    }

    public function register(RegisterRequest $request)
    {
        try {
            $userAndToken = $this->repository->register($request);

            return response()->json($userAndToken, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $userAndToken = $this->repository->login($request);

            return response()->json($userAndToken, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function logout()
    {
        try {
            $this->repository->logout();

            return response()->json([
                'message' => 'Successfully logged out'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
