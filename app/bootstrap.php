<?php

App()->registerDirs(['App\\'=>__DIR__]);

// 视图
Config('view.paths', array_merge(Config('view.paths'), [__DIR__.'/View']));

// 路由
if (Config('app.run-mode')=='api') {
    require __DIR__.'/API/route.php';
} elseif (Config('app.run-mode')=='mvc') {
    require __DIR__.'/Controller/route.php';
}

Route::dispatch( substr(uri(), strlen(Config('modules')[Config('app.active-module')][Config('app.run-mode').'-uri-prefix'])) ? : '/' );
