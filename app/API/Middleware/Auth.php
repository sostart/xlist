<?php

namespace App\API\Middleware;

class Auth
{
    public static function token($uid)
    {
        $token = md5(uniqid().rand());
        
        Cache::set($token, $uid, 3600);
        
        $tokens = Cache::get('user_token_'.$uid, []);
        $tokens[$token] = time();
        Cache::set('user_token_'.$uid, $tokens, 3600*24);

        return $token;
    }

    public static function refreshToken($token)
    {
        if ($uid = Cache($token)) {
            Cache::set($token, $uid, 3600);

            $tokens = Cache::get('user_token_'.$uid, []);
            $tokens[$token] = time();
            Cache::set('user_token_'.$uid, $tokens, 3600*24);

            return $token;
        }

        return false;
    }

    public static function clean($token)
    {
        if ($uid = Cache($token)) {
            Cache::delete($token);

            $tokens = Cache::get('user_token_'.$uid, []);
            if (isset($tokens[$token])) {
                unset($tokens[$token]);
            }
            Cache::set('user_token_'.$uid, $tokens, 3600*24);
        }

        return true;
    }

    public static function mustLogin($params)
    {
        if (!isset($params['token']) || !Cache($params['token'])) {
            return Response()->status(401)->content('notlogin')->message('未登录, 无访问权限');
        } else {
            static::refreshToken($params['token']);
        }
    }

    public static function accessCheck($params)
    {
        //return static::mustLogin($params)?:(
            //// 根据token获得用户权限 判断用户是否有访问当前path的权限
        //);
    }

    public static function mustNotLogin($params)
    {
        if (isset($params['token']) && Cache($params['token'])) {
            return Response()->status(403)->message('已登录, 无访问权限');
        }
    }
}
