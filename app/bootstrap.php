<?php

App()->registerDirs(['App\\'=>__DIR__]);

// 视图
Config('view.paths', array_merge(Config('view.paths'), [__DIR__.'/View']));

// 缓存
Config('cache.stores.prefix', 'xlist');

// 路由
if (Config('app.run-mode')=='api') {
    // 中间件
    Config('middleware', [
        'auth' => 'App\API\Middleware\Auth::mustLogin',
        'mustnotlogin' => 'App\API\Middleware\Auth::mustNotLogin'
    ]);
    require __DIR__.'/API/route.php';
} elseif (Config('app.run-mode')=='mvc') {
    // 中间件
    Config('middleware', [
        'auth' => 'App\Controller\Middleware\Auth::mustLogin',
        'mustnotlogin' => 'App\Controller\Middleware\Auth::mustNotLogin'
    ]);
    require __DIR__.'/Controller/route.php';
} elseif (Config('app.run-mode')=='cli') {
    require __DIR__.'/Command/route.php';
}

Route::dispatch( path() );
