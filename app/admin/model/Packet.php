<?php


namespace app\admin\model;

use think\Db;
use think\Model;
use util\Sql;

/**
 * 数据包模型
 * @package app\admin\model
 */
class Packet extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'admin_packet';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    /**
     * 安装数据包
     * @param string $name 数据包名
     * @return bool
     */
    public static function install($name = '')
    {
        $info = self::getInfoFromFile($name);

        foreach ($info['tables'] as $table) {
            $sql_file = realpath(config_old('packet_path') . $name . "/{$table}.sql");
            if (file_exists($sql_file)) {
                if (isset($info['database_prefix']) && $info['database_prefix'] != '') {
                    $sql_statement = Sql::getSqlFromFile($sql_file, false, [$info['database_prefix'] => config_old('database.prefix')]);
                } else {
                    $sql_statement = Sql::getSqlFromFile($sql_file);
                }

                if (!empty($sql_statement)) {
                    foreach ($sql_statement as $value) {
                        Db::execute($value);
                    }
                }
            } else {
                return "【{$table}.sql】文件不存在";
            }
        }

        return true;
    }

    /**
     * 卸载数据包
     * @param string $name 数据包名
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public static function uninstall($name = '')
    {
        $info = self::getInfoFromFile($name);
        foreach ($info['tables'] as $table) {
            $sql = "DROP TABLE IF EXISTS `" . config_old('database.prefix') . "{$table}`;";
            Db::execute($sql);
            self::where('name', $name)
                ->delete();
        }
        return true;
    }

    /**
     * 获取所有数据包列表
     * @return array|bool
     */
    public function getAll()
    {
        // 获取数据包目录下的所有插件目录
        $dirs = array_map('basename', glob(config_old('packet_path') . '*', GLOB_ONLYDIR));
        if ($dirs === false || !file_exists(config_old('packet_path'))) {
            $this->error = '插件目录不可读或者不存在';
            return false;
        }

        // 读取数据库数据包表
        $packets = $this->column(true, 'name');

        // 读取未安装的数据包
        foreach ($dirs as $packet) {
            if (!isset($packets[$packet])) {
                $info = $this->getInfoFromFile($packet);
                $info['status'] = 0;
                $packets[] = $info;
            }
        }

        return $packets;
    }

    /**
     * 从文件获取数据包信息
     * @param string $name 数据包名称
     * @return array|mixed
     */
    public static function getInfoFromFile($name = '')
    {
        $info = [];
        if ($name != '') {
            // 从配置文件获取
            if (is_file(config_old('packet_path') . $name . '/info.php')) {
                $info = include config_old('packet_path') . $name . '/info.php';
            }
        }
        return $info;
    }
}
