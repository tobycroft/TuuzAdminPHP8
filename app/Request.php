<?php
namespace app;

// 应用请求对象类
class Request extends \think\Request
{
    /**
     * 生成表单令牌
     * @param string $name 令牌名称
     * @param string $type 令牌生成方法
     * @return string
     */
    public function token($name = '__token__', $type = 'md5')
    {
        return $this->buildToken($name, $type);
    }
}