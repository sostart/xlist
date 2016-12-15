<?php

App::registerTools(['Module'=>[__DIR__.'/../MyKits', function () {
    $module = Module::getInstance();
    $module->register(Config('modules'))->resolve();
    return $module;
}]])->loadTools(['Module']);

(Config('app.active-module')===true) && Config('app.active-module', Module()->active);
(Config('app.run-mode')===true) && Config('app.run-mode', Module()->mode);

// 根据运行模式设置dispatch
if (Config('app.run-mode')=='mvc') {

    Route::setDispatcher(function ($routeInfo) {
        if ($routeInfo[0] == Route::FOUND) {
            if (is_callable($routeInfo[1][1])) {
                $callable = $routeInfo[1][1];
            } elseif (is_string($routeInfo[1][1]) && strpos($routeInfo[1][1], 'Controller')) {
                $callable = 'App\Controller\\'.$routeInfo[1][1];
            }
        } elseif ($routeInfo[0] == Route::NOT_FOUND) {
            if (Config('app.mvc-defaut-route')) {
                $arr = explode(
                    '/',
                    trim((Config('app.active-module')?substr(uri(), strlen(Config('modules')[Config('app.active-module')][Config('app.run-mode').'-uri-prefix'])):uri()), '/')
                );
                $action = array_pop($arr);
                $callable = ['App\Controller\\'.implode('\\', $arr).'Controller', $action];
            } else {
                $callable = false;
            }
        }

        if (is_callable($callable)) {
            call_user_func_array($callable, $routeInfo[2]);
        } else {
            header('HTTP/1.1 404 Not Found');
            echo View('errors.404');
        }
    });
}


// 根据运行模式设置dispatch
if (Config('app.run-mode')=='api') {

    Route::setDispatcher(function ($routeInfo) {
        
        dd($routeInfo);

        if ($routeInfo[0] == Route::FOUND) {
            
        } elseif ($routeInfo[0] == Route::NOT_FOUND) {
            $callable = false;
        }
    });
}