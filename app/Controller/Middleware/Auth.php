<?php

namespace App\Controller\Middleware;

class Auth
{
    public static function mustLogin()
    {
        if (!Session::get('token') || !API()->get('heartbeat')) {
            return redirect('login');
        }
    }

    public static function mustNotLogin()
    {
        if (Session::get('token')) {
            return redirect('/');
        }
    }
}
