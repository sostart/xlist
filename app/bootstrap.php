<?php

App()->registerDirs(['App\\'=>__DIR__]);

// 视图
Config('view.paths', array_merge(Config('view.paths'), [__DIR__.'/View']));

// 路由
if (Config('app.run-mode')=='api') {
    require __DIR__.'/API/route.php';
} elseif (Config('app.run-mode')=='mvc') {
    require __DIR__.'/Controller/route.php';
} elseif (Config('app.run-mode')=='cli') {
    require __DIR__.'/Command/route.php';
}

Route::dispatch( path() );
