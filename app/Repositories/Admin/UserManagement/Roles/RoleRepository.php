<?php

namespace App\Repositories\Admin\UserManagement\Roles;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class RoleRepository implements RoleRepositoryInterface
{
    public function getRoles(): array
    {
        if (Gate::allows('role_access')) {
            $roles = Role::with('permissions')->orderBy('id', 'DESC')->paginate(9);
            $permissions = Permission::all();

            return [
                'roles' => $roles,
                'permissions' => $permissions
            ];
        }
    }

    public function createRole($request): object
    {
        if (Gate::allows('role_create')) {
            $role = Role::create([
                'title' => $request->title,
            ]);

            if (!$role) {
                throw new \Exception('Something went wrong');
            }

            $role->permissions()->attach($request->permissions);

            return $role;
        }
    }

    public function updateRole($request, $roleId): object
    {
        if (Gate::allows('role_edit')) {
            $role = Role::find($roleId);

            if (!$role) {
                throw new \Exception('Role does not exist');
            }

            $role->update([
                'title' => $request->title,
            ]);

            $role->permissions()->sync($request->permissions);

            return $role;
        }
    }

    public function deleteRole($roleId): object
    {
        if (Gate::allows('role_delete')) {
            $role = Role::find($roleId);

            if (!$role) {
                throw new \Exception('Role does not exist');
            }

            $role->delete();

            return $role;
        }
    }

    public function abilities(): Collection
    {
        $user = Auth::user();
        $permissions = $user->role()->with('permissions')->get()->pluck('permissions')->flatten()->pluck('title');

        return $permissions;
    }
}
