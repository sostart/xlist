<?php

namespace App\Controller;

class IndexController
{
    public static function login()
    {
        @list($username, $password) = explode('@', Input('password'), 2);
        if ($username && $password) {
            if ($token = API()->post('login', ['username'=>$username, 'password'=>$password])) {
                Session::set('token', $token);
                return Response()->content($token)->json();
            }
        }
        return Response()->content(false)->message('用户名或密码错误')->json();
    }

    public static function logout()
    {
        API()->get('logout');
        Session::destroy();
        return redirect('/');
    }
}
