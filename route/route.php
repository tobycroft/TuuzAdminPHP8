<?php

use think\facade\App;

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

// 完整路由：支持 /admin/module/controller/function/param 格式
\think\facade\Route::any('admin/:realModule/:controller/:function/[:param]', function ($realModule, $controller, $function, $param = '') {
    // 静态资源文件扩展名
    $staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot'];

    // 检查是否是静态资源请求
    $pathInfo = \think\facade\Request::instance()->pathinfo();
    $extension = strtolower(pathinfo($pathInfo, PATHINFO_EXTENSION));
    if (in_array($extension, $staticExtensions)) {
        // 静态资源直接返回404（由Web服务器处理）
        return abort(404);
    }

    // CORS处理
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    if (\think\facade\Request::isOptions()) {
        return false;
    }

    // 获取实际模块名（通过映射表）
    $moduleMap = [
        'admin' => [
            'index' => 'admin',
            'user' => 'user',
            'admin' => 'admin',
        ]
    ];
    $module = isset($moduleMap['admin'][$realModule]) ? $moduleMap['admin'][$realModule] : $realModule;

    // 查找控制器（支持多种命名方式）
    $class = findController($module, $controller);
    if (!$class) {
        return abort(404, 'Controller not found: '.ucfirst($controller).' in module: '.$module);
    }

    // 使用容器创建控制器实例（支持依赖注入）
    $instance = App::make($class);

    // 调用方法（支持带参数）
    if (!method_exists($instance, $function)) {
        return abort(404, 'Method not found: '.$function);
    }

    // 根据参数数量调用方法
    if ($param !== '') {
        return call_user_func([$instance, $function], $param);
    } else {
        return call_user_func([$instance, $function]);
    }
});

// 简化路由：/admin/module/controller
\think\facade\Route::any('admin/:realModule/:controller', function ($realModule, $controller) {
    // 静态资源文件扩展名
    $staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot'];

    // 检查是否是静态资源请求
    $pathInfo = \think\facade\Request::instance()->pathinfo();
    $extension = strtolower(pathinfo($pathInfo, PATHINFO_EXTENSION));
    if (in_array($extension, $staticExtensions)) {
        return abort(404);
    }

    // CORS处理
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    if (\think\facade\Request::isOptions()) {
        return false;
    }

    // 获取实际模块名（通过映射表）
    $moduleMap = [
        'admin' => [
            'index' => 'admin',
            'user' => 'user',
            'admin' => 'admin',
        ]
    ];
    $module = isset($moduleMap['admin'][$realModule]) ? $moduleMap['admin'][$realModule] : $realModule;

    // 查找控制器（支持多种命名方式）
    $class = findController($module, $controller);
    if (!$class) {
        return abort(404, 'Controller not found: '.ucfirst($controller).' in module: '.$module);
    }

    // 使用容器创建控制器实例（支持依赖注入）
    $instance = App::make($class);

    // 调用index方法
    if (!method_exists($instance, 'index')) {
        return abort(404, 'Method index not found');
    }

    return call_user_func([$instance, 'index']);
});

// 默认路由：/admin
\think\facade\Route::any('admin', function () {
    // CORS处理
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    if (\think\facade\Request::isOptions()) {
        return false;
    }

    // 默认访问 admin 模块的 Index 控制器
    $module = 'admin';

    // 查找Index控制器
    $class = findController($module, 'Index');
    if (!$class) {
        return abort(404, 'Controller Index not found in module: '.$module);
    }

    // 使用容器创建控制器实例（支持依赖注入）
    $instance = App::make($class);

    // 调用index方法
    if (!method_exists($instance, 'index')) {
        return abort(404, 'Method index not found');
    }

    return call_user_func([$instance, 'index']);
});

// 前台路由：/:controller/:function/[:param]
\think\facade\Route::any(':controller/:function/[:param]', function ($controller, $function, $param = '') {
    // 静态资源文件扩展名
    $staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot'];

    // 检查是否是静态资源请求
    $pathInfo = \think\facade\Request::instance()->pathinfo();
    $extension = strtolower(pathinfo($pathInfo, PATHINFO_EXTENSION));
    if (in_array($extension, $staticExtensions)) {
        return abort(404);
    }

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
    $class = findController('index', $controller);
    if (!$class) {
        return abort(404, 'Controller not found: '.ucfirst($controller));
    }

    // 使用容器创建控制器实例（支持依赖注入）
    $instance = App::make($class);

    // 调用方法（支持带参数）
    if (!method_exists($instance, $function)) {
        return abort(404, 'Method not found: '.$function);
    }

    // 根据参数数量调用方法
    if ($param !== '') {
        return call_user_func([$instance, $function], $param);
    } else {
        return call_user_func([$instance, $function]);
    }
});

// 前台默认路由
\think\facade\Route::any('/', function () {
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
    $class = findController('index', 'Index');
    if (!$class) {
        return abort(404, 'Controller Index not found');
    }

    // 使用容器创建控制器实例（支持依赖注入）
    $instance = App::make($class);

    // 调用index方法
    if (!method_exists($instance, 'index')) {
        return abort(404, 'Method index not found');
    }

    return call_user_func([$instance, 'index']);
});