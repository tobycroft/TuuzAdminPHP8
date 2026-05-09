<?php

namespace app;

use think\facade\View;
use think\Validate;
use traits\controller\Jump;

// 使用 Jump trait

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
     * 验证数据（适配ThinkPHP 8，兼容ThinkPHP 5验证器）
     * @param mixed $data 数据
     * @param mixed $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return mixed
     */
    protected function validate($data, $validate, $message = [], $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            // 解析验证器名称和场景
            $scene = '';
            $validateName = $validate;
            if (strpos($validate, '.')) {
                list($validateName, $scene) = explode('.', $validate);
            }

            // 尝试多个可能的命名空间路径
            $possiblePaths = [
                "app\\validate\\{$validateName}",           // app/validate/User.php
                "app\\admin\\validate\\{$validateName}",   // app/admin/validate/User.php
                "app\\user\\validate\\{$validateName}",   // app/user/validate/User.php
                "validate\\{$validateName}",              // validate/User.php
                $validateName                             // 完整类名
            ];

            $v = null;
            foreach ($possiblePaths as $classPath) {
                if (class_exists($classPath)) {
                    $v = new $classPath();
                    break;
                }
            }

            // 如果还是找不到，尝试使用容器（ThinkPHP 5 方式）
            if (empty($v)) {
                try {
                    $v = app("validate.{$validateName}");
                } catch (\Exception $e) {
                    // 如果都找不到，尝试直接使用类名
                    try {
                        $v = app($validateName);
                    } catch (\Exception $e2) {
                        // 返回验证失败的结果
                        return "验证器不存在: {$validate}";
                    }
                }
            }

            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        if ($message) {
            $v->message($message);
        }

        return $v->batch($batch)->check($data);
    }

    /**
     * 模板变量赋值
     * @param string|array $name 变量名
     * @param mixed $value 变量值
     */
    protected function assign($name, $value = '')
    {
        View::assign($name, $value);
    }

    /**
     * 渲染模板
     * @param string $template 模板文件
     * @param array $vars 模板变量
     * @param array $config 模板配置
     */
    protected function fetch($template = '', $vars = [], $config = [])
    {
        // 获取当前模块名
        $pathInfo = $this->request->pathinfo();
        $parts = explode('/', trim($pathInfo, '/'));
        $module = !empty($parts[0]) ? $parts[0] : 'admin';
        
        // 设置模块视图路径
        View::config(['view_path' => $this->app->getAppPath() . "{$module}/view/"]);
        
        if (!empty($template)) {
            return View::fetch($template, $vars, $config);
        }

        // 自动解析模板路径
        if (count($parts) >= 2) {
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