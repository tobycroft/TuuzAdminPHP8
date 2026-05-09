<?php


namespace app\admin\model;

use think\Model;

/**
 * 后台配置模型
 * @package app\admin\model
 */
class Config extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'admin_config';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    /**
     * 获取配置信息
     * @param string $name 配置名
     * @return mixed
     */
    public function getConfig($name = '')
    {
        $configs = self::column('value,type', 'name');

        $result = [];
        foreach ($configs as $config_name => $config) {
            switch ($config['type']) {
                case 'array':
                    $result[$config_name] = parse_attr($config['value']);
                    break;
                case 'checkbox':
                    if ($config['value'] != '') {
                        $result[$config_name] = explode(',', $config['value']);
                    } else {
                        $result[$config_name] = [];
                    }
                    break;
                default:
                    $result[$config_name] = $config['value'];
                    break;
            }
        }

        return $name != '' ? ($result[$name] ?? null) : $result;
    }
}