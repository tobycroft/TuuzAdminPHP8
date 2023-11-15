<?php
declare (strict_types = 1);

namespace app\admin\controller;

class Syslog extends Base
{
  public function getModel()
  {
    return new \think\admin\model\Syslog();
  }

}
