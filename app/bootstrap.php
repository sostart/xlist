<?php

App()->registerDirs(['App\\'=>__DIR__]);

// 视图
Config('view.paths', array_merge(Config('view.paths'), [__DIR__.'/View']));

// API 路由
Route::group('/api', function () {
    require __DIR__.'/API/route.php';
});

// controller 路由
Route::get('/', 'IndexController::index');
