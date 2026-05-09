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
        // 调试模式：输出实际的模板路径
        if (true) { // 强制开启调试，调试完成后改为 false
            // 获取当前请求信息
            $pathInfo = $this->request->pathinfo();
            $controller = $this->request->controller();
            $action = $this->request->action();

            // 构建可能的模板路径
            $possiblePaths = [
                // 方式1：基于当前请求的控制器和方法
                "app/view/{$controller}/{$action}.html",
                // 方式2：admin模块下的视图
                "app/admin/view/{$controller}/{$action}.html",
                // 方式3：user模块下的视图
                "app/user/view/{$controller}/{$action}.html",
                // 方式4：如果指定了模板名
                (!empty($template) ? "app/view/{$template}.html" : ''),
                (!empty($template) ? "app/admin/view/{$template}.html" : ''),
                (!empty($template) ? "app/user/view/{$template}.html" : ''),
            ];

            // 输出调试信息
            echo '<pre>';
            echo "当前请求路径: {$pathInfo}\n";
            echo "控制器: {$controller}\n";
            echo "方法: {$action}\n";
            echo "传入的模板名: '" . ($template ?: '空') . "'\n";
            echo "\n可能的模板路径:\n";
            foreach ($possiblePaths as $path) {
                if (!empty($path)) {
                    $fullPath = $this->app->getRootPath() . $path;
                    $exists = file_exists($fullPath) ? '✓ 存在' : '✗ 不存在';
                    echo "  {$path} [{$exists}]\n";
                }
            }
            echo '</pre>';
        }

        return View::fetch($template, $vars, $config);
    }

}
