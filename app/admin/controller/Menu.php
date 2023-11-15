<?php
declare (strict_types = 1);

namespace app\admin\controller;

use think\admin\PHPTree;
use think\Request;
use think\facade\Db;
class Menu extends Base
{
  use \think\admin\traits\admin\Menu;
  // public function getTable()
  // {
  //   return 'db_menu';
  // }

  public function getModel()
  {
    return new \think\admin\model\Menu();
  }

  public function getPermissionMenu()
  {
    
  }

}
