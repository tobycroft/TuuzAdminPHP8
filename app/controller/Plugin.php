<?php


namespace app\controller;

/**
 * 插件控制器
 * @package app\index\home
 */
class Plugin extends Home
{
    /**
     * 执行插件内部方法
     * @return mixed
     */
    public function execute()
    {
        $plugin     = input('param._plugin');
        $controller = input('param._controller');
        $action     = input('param._action');
        $params     = $this->request->except(['_plugin', '_controller', '_action'], 'param');

        if (empty($plugin) || empty($controller) || empty($action)) {
            $this->error('没有指定插件名称、控制器名称或操作名称');
        }

        if (!plugin_action_exists($plugin, $controller, $action)) {
            $this->error("找不到方法：{$plugin}/{$controller}/{$action}");
        }
        return plugin_action($plugin, $controller, $action, $params);
    }
}
