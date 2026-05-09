<?php


namespace app\common\behavior;

use app\admin\model\Config as ConfigModel;
use app\admin\model\Module as ModuleModel;
use think\facade\Env;
use think\facade\Request;
use think\facade\App;

/**
 * 初始化配置信息行为
 * 将系统配置信息合并到本地配置
 * @package app\common\behavior
 * @author CaiWeiMing <314013107@qq.com>
 */
class Config
{
    /**
     * 执行行为 handle方法是事件监听器唯一的接口（ThinkPHP 8）
     * @access public
     * @return void
     */
    public function handle()
    {
        // 如果是安装操作，直接返回
        if (defined('BIND_MODULE') && BIND_MODULE === 'install') return;

        // 获取当前模块名称（ThinkPHP 8 方式）
        $module = Request::instance()->param('module', '');

        // 如果没有获取到模块，从路径中解析
        if (empty($module)) {
            $pathInfo = Request::instance()->pathinfo();
            $parts = explode('/', trim($pathInfo, '/'));
            if (!empty($parts)) {
                // 第一个部分通常是模块名
                $module = $parts[0];
            }
        }

        // 获取入口目录（使用绝对路径）
        $base_file = Request::baseFile();
        $base_dir = substr($base_file, 0, strripos($base_file, '/') + 1);

        // 确保 PUBLIC_PATH 以 / 开头（绝对路径）
        if (strpos($base_dir, '/') !== 0) {
            $base_dir = '/' . $base_dir;
        }
        define('PUBLIC_PATH', $base_dir);

        // 视图输出字符串内容替换（使用绝对路径）
        $view_replace_str = [
            // 静态资源目录（绝对路径）
            '__STATIC__' => '/static',
            // 文件上传目录（绝对路径）
            '__UPLOADS__' => '/uploads',
            // JS插件目录（绝对路径）
            '__LIBS__' => '/static/libs',
            // 后台CSS目录（绝对路径）
            '__ADMIN_CSS__' => '/static/admin/css',
            // 后台JS目录（绝对路径）
            '__ADMIN_JS__' => '/static/admin/js',
            // 后台IMG目录（绝对路径）
            '__ADMIN_IMG__' => '/static/admin/img',
            // 前台CSS目录（绝对路径）
            '__HOME_CSS__' => '/static/home/css',
            // 前台JS目录（绝对路径）
            '__HOME_JS__' => '/static/home/js',
            // 前台IMG目录（绝对路径）
            '__HOME_IMG__' => '/static/home/img',
            // 表单项扩展目录（绝对路径）
            '__EXTEND_FORM__' => '/extend/form'
        ];
        config('template.tpl_replace_string', $view_replace_str);

        // 如果定义了入口为admin，则修改默认的访问控制器层
        if (defined('ENTRANCE') && ENTRANCE == 'admin') {
            // 修复：检查 ADMIN_FILE 是否已定义
            if (!defined('ADMIN_FILE')) {
                define('ADMIN_FILE', substr($base_file, strripos($base_file, '/') + 1));
            }

            if ($module == '') {
                header('Location: ' . $base_file . '/admin', true, 302);
                exit();
            }

            if (!in_array($module, config('module.default_controller_layer', []))) {
                // 修改默认访问控制器层
                config('url_controller_layer', 'admin');
                // 修改视图模板路径
                config('template.view_path', Env::get('app_path') . $module . '/view/admin/');
            }

            // 插件静态资源目录
            config('template.tpl_replace_string.__PLUGINS__', '/plugins');
        } else {
            if ($module == 'admin') {
                header('Location: ' . $base_dir . (defined('ADMIN_FILE') ? ADMIN_FILE : 'admin.php') . '/admin', true, 302);
                exit();
            }

            if ($module != '' && !in_array($module, config('module.default_controller_layer', []))) {
                // 修改默认访问控制器层
                config('url_controller_layer', 'home');
            }
        }

        // 定义模块资源目录（使用绝对路径）
        config('template.tpl_replace_string.__MODULE_CSS__', '/static/' . $module . '/css');
        config('template.tpl_replace_string.__MODULE_JS__', '/static/' . $module . '/js');
        config('template.tpl_replace_string.__MODULE_IMG__', '/static/' . $module . '/img');
        config('template.tpl_replace_string.__MODULE_LIBS__', '/static/' . $module . '/libs');
        // 静态文件目录
        config('public_static_path', '/static/');

        // 读取系统配置
        $system_config = cache('system_config');
        if (!$system_config) {
            try {
                $ConfigModel = new ConfigModel ();
                $system_config = $ConfigModel->getConfig();
                // 所有模型配置
                $module_config = ModuleModel::where('config', 'neq', '')->column('config', 'name');
                foreach ($module_config as $module_name => $config) {
                    $system_config[strtolower($module_name) . '_config'] = json_decode($config, true);
                }
                // 非开发模式，缓存系统配置
                if (isset($system_config['develop_mode']) && $system_config['develop_mode'] == 0) {
                    cache('system_config', $system_config);
                }
            } catch (\Exception $e) {
                // 如果数据库连接失败，使用默认配置
                $system_config = [];
            }
        }

        // 设置配置信息
        if (!empty($system_config)) {
            // 定义不允许被数据库配置覆盖的核心配置项
            $protectedKeys = ['app', 'template', 'database', 'cache', 'route', 'log', 'session', 'cookie'];
            
            foreach ($system_config as $key => $value) {
                // 跳过核心配置项
                if (in_array($key, $protectedKeys)) {
                    continue;
                }
                
                // 获取现有的配置
                $existingConfig = config($key);
                
                // 如果现有配置是数组，且新值也是数组，则合并
                if (is_array($existingConfig) && is_array($value)) {
                    config($key, array_merge($existingConfig, $value));
                } else if ($existingConfig === null || !is_array($existingConfig)) {
                    // 如果现有配置不存在或不是数组，则设置新值
                    // 但如果新值是数组而旧值是字符串，不覆盖
                    if (!is_array($value)) {
                        config($key, $value);
                    }
                }
                // 如果现有配置是数组但新值不是，保留原有数组配置
            }
        }
        
        // 确保核心配置项都是数组类型
        $coreConfigs = ['app', 'template', 'database', 'cache', 'route', 'log', 'session', 'cookie', 'module'];
        foreach ($coreConfigs as $coreKey) {
            try {
                // 使用反射直接检查配置值类型
                $configInstance = \think\App::getInstance()->container->get('think\Config');
                $reflection = new \ReflectionClass($configInstance);
                $property = $reflection->getProperty('config');
                $property->setAccessible(true);
                $configArray = $property->getValue($configInstance);
                
                if (isset($configArray[$coreKey]) && !is_array($configArray[$coreKey])) {
                    // 如果核心配置不是数组，重置为空数组
                    \think\facade\Config::set([], $coreKey);
                }
            } catch (\Exception $e) {
                // 如果出现任何错误，重置为空数组
                try {
                    \think\facade\Config::set([], $coreKey);
                } catch (\Exception $e2) {
                    // 忽略进一步的错误
                }
            }
        }
    }
}