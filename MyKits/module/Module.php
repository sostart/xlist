<?php

namespace PHPKit;

use Exception;

class Module
{
    use LazySingletonTrait, LazyLinkTrait;

    protected static $modules = [];

    protected static $active = false;

    protected static $mode = false; // cli api mvc

    public function __get($name)
    {
        return static::$name();
    }

    protected static function API_active($module=false)
    {
        return (static::$active = $module?:static::$active);
    }

    protected static function API_mode($mode=false)
    {
        return (static::$mode = $mode?:static::$mode);
    }

    protected static function API_register($modules)
    {
        static::$modules = $modules;
    }
    
    // 根据模块配置确定当前请求的 模块&模式
    protected static function API_resolve()
    {
        if (php_sapi_name() == 'cli') {
            static::cliReslove();
        } else {
            static::webReslove();
        }
    }

    protected static function cliReslove()
    {
        // getopt
        if (isset($_SERVER['argv'][1]) && isset(static::$modules[$_SERVER['argv'][1]])) {
            static::$active = $_SERVER['argv'][1];
            static::$mode = 'cli';
        } else {
            throw new Exception('未找到模块');
        }
    }

    protected static function webReslove()
    {
        $container = []; $bingo = [];
        foreach (static::$modules as $module=>$config) {
            foreach (['mvc', 'api'] as $mode) {
                $sn = $config[$mode.'-server-name']?true:false;
                $path = rtrim($config[$mode.'-path-prefix'], '/');
                $trait = ($sn?$config[$mode.'-server-name']:'').$path;
                
                if (isset($container[$trait])) {
                    throw new Exception('模块配置有问题 请检查');
                }

                $container[$trait] = true;
                if (preg_match('~^'.$trait.'(/.*)?$~i', ($sn?$_SERVER['HTTP_HOST']:'').path())) {
                    // 域名匹配优先  uri更匹配优先
                    if (!isset($bingo[2]) || (!$bingo[2] && $sn) || (($bingo[2] == $sn) && (strlen($path)>$bingo[3]))) {
                        $bingo = [$module, $mode, $sn, strlen($path)];
                    }
                }
            }
        }
        
        if ($bingo) {
            static::$active = $bingo[0];
            static::$mode = $bingo[1];
        } else {
            throw new Exception('未找到模块');
        }
    }
}
