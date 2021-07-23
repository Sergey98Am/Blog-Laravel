<?php

namespace App\Http\Controllers\Admin\UserManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserManagement\CreateUserRequest;
use App\Http\Requests\Admin\UserManagement\UpdateUserRequest;
use App\Repositories\Admin\UserManagement\Users\UserRepository;

class UserController extends Controller
{
    private $repository;

    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $usersAndRoles = $this->repository->getUsers();

            return response()->json($usersAndRoles, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUserRequest $request)
    {
        try {
            $user = $this->repository->createUser($request);

            return response()->json([
                'user' => $user->load('role'),
                'message' => 'User successfully created'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, int $userId)
    {
        try {
            $user = $this->repository->updateUser($request, $userId);

            return response()->json([
                'user' => $user->load('role'),
                'message' => 'User successfully updated'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $userId)
    {
        try {
            $user = $this->repository->deleteUser($userId);

            return response()->json([
                'user' => $user,
                'message' => 'User successfully deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
