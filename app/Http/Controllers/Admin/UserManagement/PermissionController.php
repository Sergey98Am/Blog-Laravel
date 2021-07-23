<?php

namespace App\Http\Controllers\Admin\UserManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserManagement\CreatePermissionRequest;
use App\Http\Requests\Admin\UserManagement\UpdatePermissionRequest;
use App\Repositories\Admin\UserManagement\Permissions\PermissionRepository;

class PermissionController extends Controller
{
    private $repository;

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->repository = $permissionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $permissions = $this->repository->getPermissions();

            return response()->json([
                'permissions' => $permissions
            ], 200);
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
    public function store(CreatePermissionRequest $request)
    {
        try {
            $permission = $this->repository->createPermission($request);

            return response()->json([
                'permission' => $permission,
                'message' => 'Permission successfully created'
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
     * @param $permissionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePermissionRequest $request, int $permissionId)
    {
        try {
            $permission = $this->repository->updatePermission($request, $permissionId);

            return response()->json([
                'permission' => $permission,
                'message' => 'Permission successfully updated'
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
     * @param $permissionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $permissionId)
    {
        try {
            $permission = $this->repository->deletePermission($permissionId);

            return response()->json([
                'permission' => $permission,
                'message' => 'Permission successfully deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
