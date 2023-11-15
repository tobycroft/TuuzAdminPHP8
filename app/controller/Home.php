<?php


namespace app\controller;

use app\common\controller\Common;

/**
 * 前台公共控制器
 * @package app\index\controller
 */
class Home extends Common
{
    /**
     * 初始化方法
     */
    protected function initialize()
    {
        // 系统开关
        if (!config('web_site_status')) {
            var_dump(config());
            exit();
        }
        echo "123";
        exit();
    }
}
