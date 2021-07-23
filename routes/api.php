<?php

Route::group([
    'prefix' => 'auth',
    'namespace' => 'Auth',
    'middleware' => 'guest'
], function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
});

Route::get('all-posts', 'PostController@allPosts');

Route::middleware('jwt')->group(function () {
    Route::post('/check-token', 'Auth\AuthController@checkToken');
    Route::get('logout', 'Auth\AuthController@logout');
    Route::post('/change-details', 'UserController@changeDetails');
    Route::resource('posts', 'PostController');
    Route::post('save-like/post/{postId}', 'PostController@saveLike');

    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
        Route::resource('permissions', 'UserManagement\PermissionController');
        Route::resource('roles', 'UserManagement\RoleController');
        Route::resource('users', 'UserManagement\UserController');
        Route::get('abilities', 'UserManagement\RoleController@abilities');
        Route::resource('posts', 'PostController');
        Route::put('check-post/{postId}', 'PostController@checkPost');
    });
});
