<?php

namespace think\admin\traits\admin;
use think\Exception;

use think\Model;
use think\Request;
use think\facade\Db;
use think\admin\Time;
use \think\facade\Route;
trait MenuRule
{
  public function _filter($request)
  {
    $searchKey = input('searchKey');
    $query = [];
    if ($searchKey) {
      $query[] = ['name|menu_id|id|model|rule','like',"%{$searchKey}%"];
    }
    $menu_id = input('menu_id');
    if ($menu_id) {
      $query[] = ['menu_id','=',$menu_id];
    }
    
    return $query;
  }
  public function createMenuRule()
  {
    $menu_id = input('menu_id');
    $AdminMenu = new \think\admin\model\Menu();
    $vo = $AdminMenu->where('id',$menu_id)->find();
    $AdminMenu->createMenuRule($vo);
    return $this->doSuccess('ok');
  }

  public function createBatchMenuRule()
  {
    $items = input('items');
    $AdminMenu = new \think\admin\model\Menu();
    foreach ($items as $k) {
      $vo = $AdminMenu->where('id',$k['id'])->find();
      $AdminMenu->createMenuRule($vo);
    }
    return $this->doSuccess('ok');
  }
}