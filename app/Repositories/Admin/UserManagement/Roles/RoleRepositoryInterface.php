<?php namespace App\Repositories\Admin\UserManagement\Roles;

interface RoleRepositoryInterface
{
    public function getRoles();

    public function createRole($request);

    public function updateRole($request, $roleId);

    public function deleteRole($roleId);

    public function abilities();
}
