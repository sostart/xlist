<?php

if (!function_exists('Module')) {
    function Module()
    {
        return call_user_func_array(PHPKit\PHPKit::get(strtolower(__FUNCTION__)), func_get_args());
    }
}

// Module模块载入后重写path函数, 区分cli和web模式, web模式下自动 去除/添加 模块前缀
Event::listen('kit.module.loaded', function () {
    $path = clone Helper()->path;
    Helper()->register('path', function () use ($path) {
        if (php_sapi_name() == 'cli') {
            return isset($_SERVER['argv'][2])?$_SERVER['argv'][2]:'/';
        } else {
            if (func_num_args()) {
                return '/'.trim(trim(Config('modules')[Config('app.active-module')][Config('app.run-mode').'-path-prefix']?:'/', '/').call_user_func_array($path, func_get_args()),'/');
            } else {
                return '/'.trim(substr(call_user_func_array($path, func_get_args()), strlen(Config('modules')[Config('app.active-module')][Config('app.run-mode').'-path-prefix']))?:'/','/');
            }
        }
    });
});
