<?php

namespace App\Providers;

use App\Repositories\Admin\Posts\AdminPostRepositoryInterface;
use App\Repositories\Admin\Posts\AdminPostRepository;
use App\Repositories\Admin\UserManagement\Permissions\PermissionRepository;
use App\Repositories\Admin\UserManagement\Permissions\PermissionRepositoryInterface;
use App\Repositories\Admin\UserManagement\Roles\RoleRepository;
use App\Repositories\Admin\UserManagement\Roles\RoleRepositoryInterface;
use App\Repositories\Admin\UserManagement\Users\UserRepository;
use App\Repositories\Admin\UserManagement\Users\UserRepositoryInterface;
use App\Repositories\Posts\PostRepository;
use App\Repositories\Posts\PostRepositoryInterface;
use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\Auth\AuthRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AdminPostRepositoryInterface::class, AdminPostRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
