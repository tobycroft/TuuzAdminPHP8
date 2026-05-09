<?php

use think\facade\Route;

// 后台管理路由 - admin 应用
Route::rule('admin', 'admin/index/index');
Route::rule('admin/:controller', 'admin/:controller/index');
Route::rule('admin/:controller/:action', 'admin/:controller/:action');
Route::rule('admin/:controller/:action/:id', 'admin/:controller/:action');

// 前台应用路由 - index 应用
Route::rule('/', 'index/index/index');
Route::rule(':controller', 'index/:controller/index');
Route::rule(':controller/:action', 'index/:controller/:action');
Route::rule(':controller/:action/:id', 'index/:controller/:action');
