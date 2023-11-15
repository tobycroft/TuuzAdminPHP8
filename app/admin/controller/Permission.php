<?php
declare (strict_types = 1);

namespace app\admin\controller;

class Permission extends Base
{
  use \think\admin\traits\admin\Permission;
  public function getModel()
  {
    return new \think\admin\model\Permission();
  }

}
