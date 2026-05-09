<?php

\think\facade\Route::any(':module/:controller/:function', function ($module, $controller, $function) {
    // 处理 CORS 跨域
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    if (\think\facade\Request::isOptions()) {
        return false;
    }

    // 将控制器名转换为大驼峰命名（首字母大写）
    $controller = ucfirst($controller);

    // 构建完整的控制器类名
    $class = "\\app\\{$module}\\controller\\{$controller}";

    // 检查类是否存在
    if (!class_exists($class)) {
        // 尝试小写类名（兼容原有文件）
        $classLower = "\\app\\{$module}\\controller\\{$controller}";
        if (!class_exists($classLower)) {
            return abort(404, 'Controller not found');
        }
        $class = $classLower;
    }

    // 调用控制器方法
    $instance = new $class();
    if (!method_exists($instance, $function)) {
        return abort(404, 'Method not found');
    }

    return call_user_func([$instance, $function]);
});

// 简化路由：只传模块和控制器，默认调用 index 方法
\think\facade\Route::any(':module/:controller', function ($module, $controller) {
    // 处理 CORS 跨域
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    if (\think\facade\Request::isOptions()) {
        return false;
    }

    // 将控制器名转换为大驼峰命名
    $controller = ucfirst($controller);

    // 构建完整的控制器类名
    $class = "\\app\\{$module}\\controller\\{$controller}";

    // 检查类是否存在
    if (!class_exists($class)) {
        $classLower = "\\app\\{$module}\\controller\\{$controller}";
        if (!class_exists($classLower)) {
            return abort(404, 'Controller not found');
        }
        $class = $classLower;
    }

    // 调用 index 方法
    $instance = new $class();
    if (!method_exists($instance, 'index')) {
        return abort(404, 'Method index not found');
    }

    return call_user_func([$instance, 'index']);
});

// 简化路由：只传模块，默认调用 Index 控制器的 index 方法
\think\facade\Route::any(':module', function ($module) {
    // 处理 CORS 跨域
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    if (\think\facade\Request::isOptions()) {
        return false;
    }

    // 构建完整的控制器类名
    $class = "\\app\\{$module}\\controller\\Index";

    // 检查类是否存在
    if (!class_exists($class)) {
        return abort(404, 'Controller not found');
    }

    // 调用 index 方法
    $instance = new $class();
    if (!method_exists($instance, 'index')) {
        return abort(404, 'Method index not found');
    }

    return call_user_func([$instance, 'index']);
});