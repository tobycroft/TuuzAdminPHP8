<?php
declare (strict_types = 1);

namespace app\admin\controller;

use think\facade\View;
use think\facade\Session;
use think\facade\Db;
class Login extends \app\admin\controller\Base
{
  use \think\admin\traits\admin\Login;

  public function logout()
  {
    return $this->doSuccess('ok');
  }
}
