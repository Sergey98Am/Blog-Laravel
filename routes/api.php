<?php

Route::group([
    'prefix' => 'auth',
    'namespace' => 'Auth',
    'middleware' => 'guest'
], function () {

});

Route::middleware('jwt')->group(function () {

});
