<?php

Route::group('v1', function () {
    Route::get('/', 'v1\IndexAPI::index');
});
