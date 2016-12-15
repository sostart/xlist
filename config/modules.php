<?php
return call_user_func(function () {
    $domain = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'';

    return [
        'App' => [
            'dir'=>__DIR__.'/../App',
            'mvc-server-name' => $domain,
            'mvc-uri-prefix' => '/',
            'api-server-name'=> $domain,
            'api-uri-prefix'=>'/api'
        ],

        'Admin' => [
            'dir'=>__DIR__.'/../modules/Admin',
            'mvc-server-name' => $domain,
            'mvc-uri-prefix'=>'/admin',
            'api-server-name'=> $domain,
            'api-uri-prefix'=>'/admin/api'
        ],
    ];
});

// 首页模块 用户模块 好友模块 聊天模块 搜索模块
// 资源模块 缓存模块