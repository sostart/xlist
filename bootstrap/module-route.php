<?php

App::registerTools(['Module'=>[__DIR__.'/../MyKits', function () {
    $module = Module::getInstance();
    $module->register(Config('modules'))->resolve(); Event::fire('kit.module.loaded');
    return $module;
}]])->loadTools(['Module']);

(Config('app.active-module')===true) && Config('app.active-module', Module()->active);
(Config('app.run-mode')===true) && Config('app.run-mode', Module()->mode);

App::registerTools(['API' => function () {
    $api = API::getInstance();
    if (is_string(Config('app.active-module'))) {
        $config = Config('modules')[Config('app.active-module')];
        $api->setConfig(['domain' => $config['api-protocol'].'://'.$config['api-server-name'].$config['api-path-prefix'], 
            'params' => ['token'=>Session('token')],
            'callback' => function ($rs, $callback) {
                $rs = json_decode($rs, true);
                if ($rs && $rs['status']==200) {
                    return $callback?call_user_func($callback, $rs):$rs['data'];
                } else {
                    if ($rs['status']=='401' && $rs['data']=='notlogin') {
                        Session::destroy();
                        return false;
                    }
                    return null;
                }
            }
        ]);
    }
    return $api;
}]);

if (Config('app.run-mode')=='mvc') {

    Route::setDispatcher(function ($routeInfo) {
        
        $callable = false;

        if ($routeInfo[0] == Route::FOUND) {
            if (is_callable($routeInfo[1][1])) {
                $callable = $routeInfo[1][1];
            } elseif (is_string($routeInfo[1][1]) && strpos($routeInfo[1][1], 'Controller::')) {
                $callable = 'App\Controller\\'.$routeInfo[1][1];
            } elseif (is_string($routeInfo[1][1]) && (substr($routeInfo[1][1], -4)=='View')) {
                $callable = function () use ($routeInfo) {
                    return View::render(substr($routeInfo[1][1], 0, -4));
                };
            }
        } elseif ($routeInfo[0] == Route::NOT_FOUND) {
            // 未找到路由, 并开启了默认路由
            if (Config('app.mvc-defaut-route')) {
                $arr = explode(
                    '/',
                    trim(path(), '/')
                );
                $action = array_pop($arr);
                $callable = ['App\Controller\\'.implode('\\', $arr).'Controller', $action];
            }
        }

        if (is_callable($callable)) {

            $middlewares = Config('middleware');
            
            $routeInfo[1][0][] = $callable;

            foreach ($routeInfo[1][0] as $middleware) {
                if (is_callable($middleware)) {
                    $callables[] = $middleware;
                } else {
                    if (is_string($middleware) && isset($middlewares[$middleware])) {
                        $callables = is_array($middlewares[$middleware]) ? $middlewares[$middleware] : [$middlewares[$middleware]];
                    } else {
                        throw new Exception('中间件未定义 '.$middleware);
                    }
                }
            }

            foreach ($callables as $callable) {
                if (is_callable($callable)) {
                    if (!is_null($response = call_user_func_array($callable, $routeInfo[2]))) {
                        break;
                    }
                } elseif (class_exists($callable)) {
                    if (!is_null($response = call_user_func_array(new $callable, $routeInfo[2]))) {
                        break;
                    }
                } else {
                    throw new Exception('中间件不能运行 或 最终回调返回了NULL(或未返回任何内容) '.$callable);
                }
            }

            if (!is_object($response) || get_class($response)!=='PHPKit\Response') {
                echo Response()->content($response);
            } else {
                echo Response();
            }
        } else {
            echo Response()->status(404)->message('Not Found')->content(View('errors.404'));
        }
    });
}

if (Config('app.run-mode')=='api') {
    
    Response::json();

    Route::setDispatcher(function ($routeInfo) {
        if ($routeInfo[0] == Route::FOUND) {
            if (is_callable($routeInfo[1][1])) {
                $callable = $routeInfo[1][1];
            } elseif (is_string($routeInfo[1][1]) && strpos($routeInfo[1][1], 'API::')) {
                $callable = 'App\API\\'.$routeInfo[1][1];
            }
        } elseif ($routeInfo[0] == Route::NOT_FOUND) {
            $callable = false;
        }

        if (is_callable($callable)) {

            $middlewares = Config('middleware');
            $routeInfo[1][0][] = $callable;
            $params = array_merge($_REQUEST, $routeInfo[2]);

            if (isset($params['callback'])) {
                Response()->json($params['callback']);
            }

            foreach ($routeInfo[1][0] as $middleware) {
                if (is_callable($middleware)) {
                    $callables[] = $middleware;
                } else {
                    if (is_string($middleware) && isset($middlewares[$middleware])) {
                        $callables = is_array($middlewares[$middleware]) ? $middlewares[$middleware] : [$middlewares[$middleware]];
                    } else {
                        throw new Exception('中间件未定义 '.$middleware);
                    }
                }
            }

            foreach ($callables as $callable) {
                if (is_callable($callable)) {
                    if (!is_null($data = call_user_func_array($callable, [&$params]))) {
                        break;
                    }
                } elseif (class_exists($callable)) {
                    if (!is_null($data = call_user_func_array(new $callable, [&$params]))) {
                        break;
                    }
                } else {
                    throw new Exception('中间件不能运行 或 最终回调返回了NULL(或未返回任何内容) '.$callable);
                }
            }

            if (!is_object($data) || get_class($data)!=='PHPKit\Response') {
                echo Response()->content($data);
            } else {
                echo Response();
            }
        } else {
            echo Response()->status(404)->message('未找到接口');
        }
    });
}

if (Config('app.run-mode')=='cli') {

    Route::setDispatcher(function ($routeInfo) {
        if ($routeInfo[0] == Route::FOUND) {
            if (is_callable($routeInfo[1][1])) {
                $callable = $routeInfo[1][1];
            } elseif (is_string($routeInfo[1][1]) && strpos($routeInfo[1][1], 'Command::')) {
                $callable = 'App\Command\\'.$routeInfo[1][1];
            }
        } elseif ($routeInfo[0] == Route::NOT_FOUND) {
            $callable = false;
        }

        if (is_callable($callable)) {
            call_user_func_array($callable, $routeInfo[2]);
        }
    });
}
