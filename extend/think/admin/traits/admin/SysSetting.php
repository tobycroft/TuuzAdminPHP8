<?php

namespace think\admin\traits\admin;
use think\Exception;

use think\Model;
use think\Request;
use think\facade\Db;
use think\admin\Time;
use \think\facade\Route;
trait SysSetting
{

  public function read()
  {
    $SysSetting = $this->getModel();
    $list = $SysSetting->where('id','>',0)->select();
    $rows = [];
    foreach ($list as $k) {
      # code...
      $rows[$k['field']] = $k['value'];
    }
    return $this->doSuccess('ok',$rows);
  }
  public function update()
  {
    $SysSetting = $this->getModel();
    $post = input('post.');
    $rows = [];
    foreach ($post as $key => $value) {
      $v = [];
      $v['field'] = $key;
      $v['name'] = $key;
      $v['value'] = $value;
      $ck = $SysSetting->where('field',$key)->count('id');
      if ($ck) {
        $SysSetting->where('field',$key)->update($v);
      }else{
        $SysSetting->insert($v);
      }
    }
    return $this->doSuccess('ok');
  }
}