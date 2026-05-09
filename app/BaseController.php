<?php
declare (strict_types=1);

namespace app;

use liliuwei\think\Jump;
use think\App;
use think\exception\ValidateException;
use think\facade\Config;
use think\facade\Db;
use think\facade\View;
use think\Validate;

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
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {

        $this->app = $app;
        $this->request = $this->app->request;
        $config = Db::name('dp_admin_config')->column('value', 'name');
        Config::set($config, 'tp');
        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {
    }

    /**
     * 验证数据
     * @access protected
     * @param array $data 数据
     * @param string|array $validate 验证器名或者验证规则数组
     * @param array $message 提示信息
     * @param bool $batch 是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, string|array $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        $v->message($message);

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        return $v->failException(true)->check($data);
    }

    /**
     * 模板变量赋值（兼容 ThinkPHP 5）
     * @param mixed $name 变量名或数组
     * @param mixed $value 变量值
     * @return $this
     */
    protected function assign($name, $value = '')
    {
        View::assign($name, $value);
        return $this;
    }

    /**
     * 渲染模板输出（兼容 ThinkPHP 5，支持自定义路由）
     * @param string $template 模板文件名
     * @param array $vars 模板变量
     * @param array $config 模板配置
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $config = [])
    {
        // 如果没有指定模板名，自动根据URL解析
        if (empty($template)) {
            // 获取当前请求路径
            $pathInfo = $this->request->pathinfo();

            // 解析 URL 路径
            // 格式: admin/module/controller/action 或 admin/controller/action
            $parts = explode('/', trim($pathInfo, '/'));

            // 模块映射表
            $moduleMap = [
                'admin' => 'admin',
                'user' => 'user',
            ];

            // 确定实际模块、控制器和方法
            $module = 'admin';  // 默认模块
            $controller = 'Index';
            $action = 'index';

            if (count($parts) >= 2) {
                // URL 格式: admin/controller/action
                if (isset($moduleMap[$parts[1]])) {
                    // URL 格式: admin/module/controller/action
                    $module = $moduleMap[$parts[1]];
                    $controller = isset($parts[2]) ? $parts[2] : 'Index';
                    $action = isset($parts[3]) ? $parts[3] : 'index';
                } else {
                    // URL 格式: admin/controller/action
                    $controller = $parts[1];
                    $action = isset($parts[2]) ? $parts[2] : 'index';
                }
            }

            // 将控制器名转换为大驼峰命名
            $controller = ucfirst($controller);

            // 构建模板路径
            $templatePath = $this->app->getAppPath() . "{$module}/view/{$controller}/{$action}.html";

            // 检查模板文件是否存在
            if (file_exists($templatePath)) {
                $template = $templatePath;
            } else {
                // 尝试小写控制器名
                $controllerLower = strtolower($controller);
                $templatePathLower = $this->app->getAppPath() . "{$module}/view/{$controllerLower}/{$action}.html";
                if (file_exists($templatePathLower)) {
                    $template = $templatePathLower;
                } else {
                    // 如果都不存在，返回错误信息
                    return "<pre>模板文件不存在！\n\n尝试的路径：\n- {$templatePath}\n- {$templatePathLower}</pre>";
                }
            }
        }

        // 调用视图引擎渲染模板
        return View::fetch($template, $vars, $config);
    }

}