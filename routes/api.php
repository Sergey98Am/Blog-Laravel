<?php

Route::group([
    'prefix' => 'auth',
    'namespace' => 'Auth',
    'middleware' => 'guest'
], function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
});

Route::middleware('jwt')->group(function () {
    Route::post('/check-token', 'Auth\AuthController@checkToken');
    Route::get('logout', 'Auth\AuthController@logout');
    Route::post('/change-details', 'UserController@changeDetails');
    Route::resource('posts', 'PostController');
    Route::get('all-posts', 'PostController@allPosts');

    // Admin
    Route::resource('permissions', 'Admin\UserManagement\PermissionController');
    Route::resource('roles', 'Admin\UserManagement\RoleController');
    Route::resource('users', 'Admin\UserManagement\UserController');
    Route::get('abilities', 'Admin\UserManagement\RoleController@abilities');
});
