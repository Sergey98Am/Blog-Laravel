<?php

namespace App\Repositories\Admin\UserManagement\Permissions;

use App\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function getPermissions(): LengthAwarePaginator
    {
        if (Gate::allows('permission_access')) {
            $permissions = Permission::orderBy('id', 'DESC')->paginate(9);

            return $permissions;
        }
    }

    public function createPermission($request): object
    {
        if (Gate::allows('permission_create')) {
            $permission = Permission::create([
                'title' => $request->title,
            ]);

            if (!$permission) {
                throw new \Exception('Something went wrong');
            }

            return $permission;
        }
    }

    public function updatePermission($request, $permissionId): object
    {
        if (Gate::allows('permission_edit')) {
            $permission = Permission::find($permissionId);

            if (!$permission) {
                throw new \Exception('Permission does not exist');
            }

            $permission->update([
                'title' => $request->title,
            ]);

            return $permission;
        }
    }

    public function deletePermission($permissionId): object
    {
        if (Gate::allows('permission_delete')) {
            $permission = Permission::find($permissionId);

            if (!$permission) {
                throw new \Exception('Permission does not exist');
            }

            $permission->delete();

            return $permission;
        }
    }
}
