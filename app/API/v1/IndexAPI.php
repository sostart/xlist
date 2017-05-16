<?php

namespace App\API\v1;

use App\API\Middleware\Auth;

class IndexAPI
{
    public static function login($params)
    {
        // 验证
        if ( $info = find('users', 'id, password', ['username'=>$params['username']])) {
             if (md5($params['password']) == $info['password']) {                    
                // 生成记录并返回token
                return Auth::token($info['id']);
             }
        }

        return false;
    }

    public static function logout($params)
    {
        Auth::clean($params['token']);
        return true;
    }
}
