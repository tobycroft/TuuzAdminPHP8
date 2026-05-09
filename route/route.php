<?php

\think\facade\Route::any(':module/:controller/:function',
    '\app\:version\:module\controller\:controller@:function')->before(function () {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    if (\think\facade\Request::isOptions()) {
        return false;
    }
    return true;
});