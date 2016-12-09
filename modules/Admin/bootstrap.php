<?php

App()->registerDirs(['App\\'=>__DIR__]);

Config('view.paths', array_merge(Config('view.paths'), [__DIR__.'/View']));

// api路由
Route::group('/api', function () {
    require __DIR__.'/API/route.php';
});

// controller路由
Route::get('/', 'IndexController::index');
