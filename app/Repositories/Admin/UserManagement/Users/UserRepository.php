<?php

namespace App\Repositories\Admin\UserManagement\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function getUsers(): array
    {
        if (Gate::allows('user_access')) {
            $users = User::with('role')->orderBy('id', 'DESC')->paginate(9);
            $roles = Role::all();

            return [
                'users' => $users,
                'roles' => $roles
            ];
        }
    }

    public function createUser($request): object
    {
        if (Gate::allows('user_create')) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            if (!$user) {
                throw new \Exception('Something went wrong');
            }

            return $user;
        }
    }

    public function updateUser($request, $userId): object
    {
        if (Gate::allows('user_edit')) {
            $user = User::find($userId);

            if (!$user) {
                throw new \Exception('User does not exist');
            }

            $user->update([
                'name' => $request->name,
                'role_id' => $request->role_id,
            ]);

            return $user;
        }
    }

    public function deleteUser($userId): object
    {
        if (Gate::allows('user_delete')) {
            $user = User::find($userId);

            if (!$user) {
                throw new \Exception('User does not exist');
            }

            $user->delete();

            return $user;
        }
    }
}
