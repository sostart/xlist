<?php

return call_user_func(function () {
    
    $domain = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'';

    return [
        'App' => [
            'dir'=>__DIR__.'/../app',
            'mvc-protocol' => 'http',
            'mvc-server-name' => $domain,
            'mvc-path-prefix' => '/',
            'api-protocol' => 'http',
            'api-server-name'=> $domain,
            'api-path-prefix'=>'/api'
        ],

        'Admin' => [
            'dir'=>__DIR__.'/../modules/Admin',
            'mvc-protocol' => 'http',
            'mvc-server-name' => $domain,
            'mvc-path-prefix'=>'/admin',
            'api-protocol' => 'http',
            'api-server-name'=> $domain,
            'api-path-prefix'=>'/admin/api'
        ],
    ];
});

// 首页模块 用户模块 好友模块 聊天模块 搜索模块
// 资源模块 缓存模块
