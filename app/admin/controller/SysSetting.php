<?php
declare (strict_types = 1);

namespace app\admin\controller;

use think\facade\Db;
class SysSetting extends Base
{
  use \think\admin\traits\admin\SysSetting;
  
  public function append()
  {
    return ['code_txt'];
  }
  public function getModel()
  {
    return new \think\admin\model\SysSetting();
  }

}
