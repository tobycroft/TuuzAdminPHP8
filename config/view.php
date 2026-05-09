<?php
// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------

return [
    // 模板引擎类型
    'type'          => 'Think',
    // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写
    'auto_rule'     => 1,
    // 模板目录名
    'view_dir_name' => 'view',
    // 模板后缀
    'view_suffix'   => 'html',
    // 模板文件名分隔符
    'view_depr'     => DIRECTORY_SEPARATOR,
    // 模板引擎普通标签开始标记
    'tpl_begin'     => '{',
    // 模板引擎普通标签结束标记
    'tpl_end'       => '}',
    // 标签库标签开始标记
    'taglib_begin'  => '{',
    // 标签库标签结束标记
    'taglib_end'    => '}',
    // 是否开启模板编译缓存
    'tpl_cache'     => false,
    // 模板替换字符串
    'tpl_replace_string' => [
        '__STATIC__'     => '/static',
        '__UPLOADS__'    => '/uploads',
        '__LIBS__'       => '/static/libs',
        '__ADMIN_CSS__'  => '/static/admin/css',
        '__ADMIN_JS__'   => '/static/admin/js',
        '__ADMIN_IMG__'  => '/static/admin/img',
        '__HOME_CSS__'   => '/static/home/css',
        '__HOME_JS__'    => '/static/home/js',
        '__HOME_IMG__'   => '/static/home/img',
    ],
];