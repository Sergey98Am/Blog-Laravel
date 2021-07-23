<?php

namespace App\Http\Controllers\Admin\UserManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserManagement\CreateRoleRequest;
use App\Http\Requests\Admin\UserManagement\UpdateRoleRequest;
use App\Models\Role;
use App\Repositories\Admin\UserManagement\Roles\RoleRepository;

class RoleController extends Controller
{
    private $repository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->repository = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $rolesAndPermissions = $this->repository->getRoles();

            return response()->json($rolesAndPermissions, 200);
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
    public function store(CreateRoleRequest $request)
    {
        try {
            $role = $this->repository->createRole($request);

            return response()->json([
                'role' => $role->load('permissions'),
                'message' => 'Role successfully created'
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
     * @param $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRoleRequest $request, int $roleId)
    {
        try {
            $role = $this->repository->updateRole($request, $roleId);

            return response()->json([
                'role' => $role->load('permissions'),
                'message' => 'Role successfully updated'
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
     * @param $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $roleId)
    {
        try {
            $role = $this->repository->deleteRole($roleId);

            return response()->json([
                'role' => $role,
                'message' => 'Role successfully deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function abilities()
    {
        try {
            $permissions = $this->repository->abilities();

            return response()->json([
                'permissions' => $permissions,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
