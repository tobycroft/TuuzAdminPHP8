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
        if (!config_old('web_site_status')) {
            $this->error('站点已经关闭，请稍后访问~');
        }
    }

    public function index()
    {
        // 默认跳转模块
        if (config_old('home_default_module') != '' && config_old('home_default_module') != 'index') {
            $this->redirect(config_old('home_default_module') . '/index/index');
        }
        return 'home';
    }
}
