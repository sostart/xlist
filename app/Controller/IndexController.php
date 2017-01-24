<?php

namespace App\Controller;

class IndexController
{
    public static function index()
    {
        echo View::render('index', ['title'=>'hello, world']);
    }
}
