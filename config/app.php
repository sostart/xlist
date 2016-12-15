<?php

return [
    
    'debug' => true,
    
    // 是否启用默认路由 默认路由规则: Admin/Index/login => App\Controller\Admin\IndexController::login
    'mvc-defaut-route' => true,

    // 设置为 true 在module-route.php中会根据模块配置自动进行设定
    'active-module' => true,
    'run-mode' => true, // cli mvc api
];
