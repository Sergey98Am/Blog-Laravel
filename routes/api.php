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
    Route::get('post/{postId}', 'PostController@post');
    Route::post('save-like/post/{postId}', 'PostController@saveLike');
    Route::get('user-notifications', 'NotificationController@userNotifications');
    Route::get('unread-notifications-count', 'NotificationController@unreadNotificationsCount');
    Route::get('mark-all-as-read', 'NotificationController@markAllAsRead');
    Route::get('mark-as-read/{notificationId}', 'NotificationController@markAsRead');
    Route::post('load-more-notifications', 'NotificationController@loadMoreData');
    // Comments
    Route::get('posts/{postId}/comments', 'CommentController@index');
    Route::post('posts/{postId}/comments', 'CommentController@store');
    Route::post('posts/{postId}/comments/{commentId}/replies', 'CommentController@reply');
    Route::put('posts/{postId}/comments/{commentId}', 'CommentController@update');
    Route::delete('posts/{postId}/comments/{commentId}', 'CommentController@destroy');
    Route::post('posts/{postId}/load-more-comments', 'CommentController@loadMoreComments');
    Route::post('posts/{postId}/comments/{commentId}/load-more-replies', 'CommentController@loadMoreReplies');

    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
        Route::resource('permissions', 'UserManagement\PermissionController');
        Route::resource('roles', 'UserManagement\RoleController');
        Route::resource('users', 'UserManagement\UserController');
        Route::get('abilities', 'UserManagement\RoleController@abilities');
        Route::resource('posts', 'PostController');
        Route::put('check-post/{postId}', 'PostController@checkPost');
        // Comment
        Route::get('posts/{postId}/comments', 'CommentController@index');
        Route::delete('posts/{postId}/comments/{commentId}', 'CommentController@destroy');
    });
});
