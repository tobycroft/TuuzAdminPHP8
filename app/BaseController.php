<?php

namespace app;

use think\exception\ValidateException;
use think\facade\Config;
use think\facade\Db;
use think\facade\View;
use think\Validate;

// 使用 Jump trait
use traits\controller\Jump;

/**
 * 控制器基础类
 */
abstract class BaseController
{
    use Jump;

    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 构造函数
     * @param \think\App $app
     */
    public function __construct(\think\App $app)
    {
        $this->app = $app;
        $this->request = $app->request;

        // 初始化
        $this->initialize();
    }

    /**
     * 初始化
     */
    protected function initialize()
    {
    }

    /**
     * 模板变量赋值
     * @param string|array $name 变量名
     * @param mixed        $value 变量值
     */
    protected function assign($name, $value = '')
    {
        View::assign($name, $value);
    }

    /**
     * 渲染模板
     * @param string $template 模板文件
     * @param array  $vars     模板变量
     * @param array  $config   模板配置
     */
    protected function fetch($template = '', $vars = [], $config = [])
    {
        if (!empty($template)) {
            return View::fetch($template, $vars, $config);
        }

        // 自动解析模板路径
        $pathInfo = $this->request->pathinfo();
        $parts = explode('/', trim($pathInfo, '/'));

        if (count($parts) >= 2) {
            $module = $parts[0];
            $controller = isset($parts[1]) ? $parts[1] : '';
            $action = isset($parts[2]) ? $parts[2] : 'index';

            $templatePath = $this->app->getAppPath() . "{$module}/view/{$controller}/{$action}.html";
            if (file_exists($templatePath)) {
                return View::fetch($templatePath, $vars, $config);
            }
        }

        return View::fetch($template, $vars, $config);
    }
}