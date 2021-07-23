<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Repositories\User\UserRepository;
use JWTAuth;

class UserController extends Controller
{
    private $repository;

    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }

    public function changeDetails(UserRequest $request)
    {
        try {
            $user = $this->repository->changeDetails($request);

            return response()->json([
                'message' => 'Profile updated successfully!',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
