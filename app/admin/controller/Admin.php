<?php
declare (strict_types=1);

namespace app\admin\controller;

use think\facade\Db;

class Admin extends Base
{
    use \think\admin\traits\admin\Admin;

    public function append()
    {
        return ['code_txt'];
    }

    public function getModel()
    {
        return new \think\admin\model\Admin();
    }

}
