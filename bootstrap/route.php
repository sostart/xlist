<?php

Route::callback(function ($routeInfo) {
    $uri = rawurldecode( 
        isset($_SERVER['PATH_INFO']) ? ($_SERVER['PATH_INFO']?:'/') :
        ((false !== $pos = strpos($uri, '?')) ? substr($_SERVER['REQUEST_URI'], 0, $pos) : $_SERVER['REQUEST_URI'])
    );

    if ($routeInfo[0] == Route::FOUND) {
        if (is_callable($routeInfo[1][1])) {
            $callable = $routeInfo[1][1];
        } elseif (is_string($routeInfo[1][1]) && strpos($routeInfo[1][1], 'Controller')) {
            $callable = 'App\Controller\\'.$routeInfo[1][1];
        }
    } elseif ($routeInfo[0] == Route::NOT_FOUND) {
        $arr = explode('/', trim($uri, '/'));
        $action = array_pop($arr);
        $callable = ['App\Controller\\'.implode('\\', $arr).'Controller', $action];
    }

    if (is_callable($callable)) {
        call_user_func_array($callable, $routeInfo[2]);
    } else {
        header('HTTP/1.1 404 Not Found');
        echo View('errors.404');
    }
});

// 根据域名或uri 载入模块及配置
call_user_func(function () {
    foreach (Config('modules') as $module=>$dir) {
        $module = strtolower($module);
        if ($module=='app') {
            require Config('modules.App').'/bootstrap.php';
        } else {
            Route::group($module, function () use ($dir) {
                require $dir.'/bootstrap.php';
            });
        }
    }
});
