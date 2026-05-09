<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

// [ PHP版本检查 ]
header("Content-type: text/html; charset=utf-8");
if (version_compare(PHP_VERSION, '8.0', '<')) {
    die('PHP版本过低，最少需要PHP8.0，请升级PHP版本！');
}

// 定义后台入口文件
define('ADMIN_FILE', 'admin.php');

// 加载基础文件
require __DIR__ . '/../vendor/autoload.php';

// 支持事先使用静态方法设置Request对象和Config对象

// 检查是否安装
if (!is_file('../data/install.lock')) {
    // 执行安装模块
    $http = (new App())->http;
    $response = $http->name('install')->run();
    $response->send();
    $http->end($response);
} else {
    // 执行应用并响应
    $http = (new App())->http;
    $response = $http->run();
    $response->send();
    $http->end($response);
}