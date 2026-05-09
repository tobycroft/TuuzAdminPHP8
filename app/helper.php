<?php

// +----------------------------------------------------------------------
// | 自定义辅助函数
// +----------------------------------------------------------------------

if (!function_exists('url')) {
    /**
     * URL生成（ThinkPHP 8 方式）
     * @param string        $url        URL地址
     * @param array|string  $vars       变量（支持数组或字符串）
     * @param bool|string   $suffix     后缀
     * @param bool          $domain     是否显示域名
     * @return string
     */
    function url($url = '', $vars = '', $suffix = true, $domain = false)
    {
        // 如果URL已经包含模块/控制器信息，直接生成
        if (strpos($url, '/') !== false) {
            // 将字符串参数转换为数组
            if (is_string($vars) && !empty($vars)) {
                parse_str($vars, $vars);
            }
            if ($vars === '') {
                $vars = [];
            }

            return (string) \think\facade\Route::buildUrl($url, $vars)->suffix($suffix)->domain($domain);
        }

        // 如果只是方法名，需要补充模块和控制器信息
        $request = \think\facade\Request::instance();
        $pathInfo = $request->pathinfo();
        $parts = explode('/', trim($pathInfo, '/'));

        // 获取当前模块和控制器
        $module = '';
        $controller = '';

        if (count($parts) >= 2) {
            $module = $parts[0];
            $controller = $parts[1];
        }

        // 构建完整的URL路径
        $fullUrl = "{$module}/{$controller}/{$url}";

        // 将字符串参数转换为数组
        if (is_string($vars) && !empty($vars)) {
            parse_str($vars, $vars);
        }
        if ($vars === '') {
            $vars = [];
        }

        // 生成URL
        return (string) \think\facade\Route::buildUrl($fullUrl, $vars)->suffix($suffix)->domain($domain);
    }
}

if (!function_exists('captcha_src')) {
    /**
     * 获取验证码图片URL
     * @param string $id
     * @return string
     */
    function captcha_src($id = '')
    {
        return url('/captcha' . ($id ? "/{$id}" : ''));
    }
}

if (!function_exists('captcha_check')) {
    /**
     * 验证验证码是否正确
     * @param string $value
     * @param string $id
     * @return bool
     */
    function captcha_check($value, $id = '')
    {
        $captcha = new \think\captcha\Captcha();
        return $captcha->check($value, $id);
    }
}

if (!function_exists('action_log')) {
    /**
     * 记录行为日志
     * @param string $action     行为名称
     * @param string $model      模型名
     * @param int    $record_id  记录ID
     * @param int    $user_id    用户ID
     * @return bool
     */
    function action_log($action, $model = '', $record_id = 0, $user_id = 0)
    {
        try {
            $data = [
                'action'     => $action,
                'model'      => $model,
                'record_id'  => $record_id,
                'user_id'    => $user_id ?: session('user_auth.uid', 0),
                'ip'         => \think\facade\Request::instance()->ip(),
                'create_time' => time(),
            ];
            \think\facade\Db::name('action_log')->insert($data);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('is_signin')) {
    /**
     * 判断是否登录
     * @return bool
     */
    function is_signin()
    {
        return session('user_auth.uid') ? true : false;
    }
}