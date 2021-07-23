<?php namespace App\Repositories\Admin\UserManagement\Users;

interface UserRepositoryInterface
{
    public function getUsers();

    public function createUser($request);

    public function updateUser($request, $userId);

    public function deleteUser($userId);
}
