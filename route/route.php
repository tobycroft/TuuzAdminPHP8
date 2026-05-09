<?php

// 辅助函数：查找控制器类
function findController($module, $controller) {
    // 尝试1：大驼峰命名（PSR-4标准）
    $class = "\\app\\{$module}\\controller\\".ucfirst($controller);
    if (class_exists($class)) {
        return $class;
    }

    // 尝试2：全小写命名（兼容旧代码）
    $class = "\\app\\{$module}\\controller\\".strtolower($controller);
    if (class_exists($class)) {
        return $class;
    }

    // 尝试3：保持原大小写（完全匹配）
    $class = "\\app\\{$module}\\controller\\{$controller}";
    if (class_exists($class)) {
        return $class;
    }

    return false;
}

// 完整路由：/module/controller/function
\think\facade\Route::any(':module/:controller/:function', function ($module, $controller, $function) {
    // CORS处理
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    if (\think\facade\Request::isOptions()) {
        return false;
    }

    // 查找控制器（支持多种命名方式）
    $class = findController($module, $controller);
    if (!$class) {
        return abort(404, 'Controller not found: '.ucfirst($controller));
    }

    // 调用方法
    $instance = new $class();
    if (!method_exists($instance, $function)) {
        return abort(404, 'Method not found: '.$function);
    }

    return call_user_func([$instance, $function]);
});

// 简化路由：/module/controller
\think\facade\Route::any(':module/:controller', function ($module, $controller) {
    // CORS处理
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    if (\think\facade\Request::isOptions()) {
        return false;
    }

    // 查找控制器（支持多种命名方式）
    $class = findController($module, $controller);
    if (!$class) {
        return abort(404, 'Controller not found: '.ucfirst($controller));
    }

    // 调用index方法
    $instance = new $class();
    if (!method_exists($instance, 'index')) {
        return abort(404, 'Method index not found');
    }

    return call_user_func([$instance, 'index']);
});

// 默认路由：/module
\think\facade\Route::any(':module', function ($module) {
    // CORS处理
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    if (\think\facade\Request::isOptions()) {
        return false;
    }

    // 查找Index控制器
    $class = findController($module, 'Index');
    if (!$class) {
        return abort(404, 'Controller Index not found');
    }

    // 调用index方法
    $instance = new $class();
    if (!method_exists($instance, 'index')) {
        return abort(404, 'Method index not found');
    }

    return call_user_func([$instance, 'index']);
});
