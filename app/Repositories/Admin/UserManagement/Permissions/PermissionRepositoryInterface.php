<?php namespace App\Repositories\Admin\UserManagement\Permissions;

interface PermissionRepositoryInterface
{
    public function getPermissions();

    public function createPermission($request);

    public function updatePermission($request, $permissionId);

    public function deletePermission($permissionId);
}
