<?php



// 登陆 && 退出
Route::get('login', 'mustnotlogin', 'loginView');
Route::post('login', 'mustnotlogin', 'IndexController::login');
Route::get('logout', 'auth', 'IndexController::logout');

Route::group('/', 'auth', function () {
    $config = Config('modules')[Config('app.active-module')];
    View::share('apiurl', $config['api-protocol'].'://'.$config['api-server-name'].$config['api-path-prefix'].'/');
    View::share('token', Session('token'));
    
    Route::get('/', 'indexView');

    Route::get('test', function () {

    });
});
