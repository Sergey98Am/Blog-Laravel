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
});
