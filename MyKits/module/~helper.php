<?php

if (!function_exists('Module')) {
    function Module()
    {
        return call_user_func_array(PHPKit\PHPKit::get(strtolower(__FUNCTION__)), func_get_args());
    }
}

// Module模块载入后重写uri函数, 区分cli和web模式, web模式下自动 去除/添加 模块前缀
Event::listen('kit.module.loaded', function () {
    $uri = clone Helper()->uri;
    Helper()->register('uri', function () use ($uri) {
        if (php_sapi_name() == 'cli') {
            return isset($_SERVER['argv'][2])?$_SERVER['argv'][2]:'/';
        } else {
            if (func_num_args()) {
                return rtrim(Config('modules')[Config('app.active-module')][Config('app.run-mode').'-uri-prefix']?:'/', '/') . call_user_func_array($uri, func_get_args());
            } else {
                return substr(call_user_func_array($uri, func_get_args()), strlen(Config('modules')[Config('app.active-module')][Config('app.run-mode').'-uri-prefix'])) ? : '/';
            }            
        }
    });
});
