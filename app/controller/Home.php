<?php


namespace app\controller;

use app\common\controller\Common;
use think\facade\Config;

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
//        if (!config_old('web_site_status')) {
//            $this->error('站点已经关闭，请稍后访问~');
//        }
        echo json_encode(Config::get('tp.'),320);
        exit();
    }
}
