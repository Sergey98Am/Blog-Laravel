<?php

Route::group([
    'prefix' => 'auth',
    'namespace' => 'Auth',
    'middleware' => 'guest'
], function () {
    Route::post('register', 'AuthController@register');
});

Route::middleware('jwt')->group(function () {
});
