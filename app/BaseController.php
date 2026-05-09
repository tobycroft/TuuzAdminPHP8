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
     * 渲染模板输出（兼容 ThinkPHP 5）
     * @param string $template 模板文件名
     * @param array $vars 模板变量
     * @param array $config 模板配置
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $config = [])
    {
        // 如果指定了模板名，直接使用
        if (!empty($template)) {
            return View::fetch($template, $vars, $config);
        }

        // 如果没有指定模板名，根据URL路径自动解析
        $pathInfo = $this->request->pathinfo();
        $parts = explode('/', trim($pathInfo, '/'));

        // URL格式: admin/module/controller/action 或 admin/controller/action
        if (count($parts) >= 2) {
            $module = $parts[0];  // 取第一个部分作为模块
            $controller = $parts[1];  // 取第二个部分作为控制器
            $action = isset($parts[2]) ? $parts[2] : 'index';  // 取第三个部分作为方法

            // 构建模板路径
            $templatePath = $this->app->getAppPath() . "{$module}/view/{$controller}/{$action}.html";

            // 如果文件存在，使用完整路径
            if (file_exists($templatePath)) {
                return View::fetch($templatePath, $vars, $config);
            }
        }

        // 默认情况：调用原始的 View::fetch
        return View::fetch($template, $vars, $config);
    }

}