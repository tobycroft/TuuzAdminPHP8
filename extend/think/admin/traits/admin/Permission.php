<?php

namespace think\admin\traits\admin;
use think\Exception;

use think\Model;
use think\Request;
use think\facade\Db;
use think\admin\Time;
use \think\facade\Route;
trait Permission
{
  public function savePermission()
  {
    // return $this->doSuccess('ok');

    $role_type = input('type');
    $psotList = input('code');

    $Permission = new \think\admin\model\Permission();
    $Permission->where('role_type',$role_type)->delete();

    //先删除多余的
    foreach ($psotList as $k) {
      if ($k['type'] == 'menu') {
        $vo = [];
        $vo['type'] = 'menu';
        $vo['role_type'] = $role_type;
        $vo['menu_id'] = $k['menu_id'];
        $ck = $Permission
                ->where('menu_id',$k['menu_id'])
                ->where('type','menu')
                ->where('role_type',$role_type)
                ->count('id');
        if (!$ck) {
          $Permission->insert($vo);
        }
      }elseif ($k['type'] == 'rule') {
        $vo = [];
        $vo['type'] = 'rule';
        $vo['role_type'] = $role_type;
        $vo['menu_id'] = $k['menu_id'];
        $vo['menu_rule_id'] = $k['rule_id'];
        $ck = $Permission
                ->where('menu_rule_id',$k['rule_id'])
                ->where('type','rule')
                ->where('menu_id',$k['menu_id'])
                ->where('role_type',$role_type)
                ->count('id');
        if (!$ck) {
          $Permission->insert($vo);
        }
      }
    }


    return $this->doSuccess('ok');
  }

  public function getAllMenu()
  {
    $PHPTree = new \think\admin\PHPTree();
    $Menu = new \think\admin\model\Menu();
    $Permission = new \think\admin\model\Permission();
    $MenuRule = new \think\admin\model\MenuRule();

    $role_type = input('type');
    $Permission = new \think\admin\model\Permission();
    $plist = $Permission->where('role_type',$role_type)->select();
    $value_list = [];
    foreach ($plist as $k) {
      if ($k['type'] == 'menu') {
        $value_list[] = $k['menu_id'].'menu';
      }elseif ($k['type'] == 'rule') {
        $value_list[] = $k['menu_rule_id'].'rule';
      }
    }


    $list = $Menu
              ->field('id as value,name as label,pid,id, id as menu_id')
              ->where('status',1)
              // ->where('pid',0)
              ->order('idx asc')
              ->select()->toArray();
    foreach ($list as &$k) {
      $k['value'] .= 'menu' ;
      $k['type'] = 'menu' ;
      $k['rule_id'] = 0 ;
    }


    $rules = $MenuRule
              ->field('id as value,name as label,menu_id as pid,id,menu_id,rule,id as rule_id')
              ->select()->toArray();
    foreach ($rules as &$k) {
      $k['value'] .= 'rule' ;
      $k['type'] = 'rule' ;
      $k['label'] .= $k['rule'] ;
    }
    $list = array_merge($list,$rules);
    $list = $PHPTree->makeTree($list);

    
    $data['items'] = $list;
    $data['value'] = implode(",",$value_list);
    // $data['value'] = [235];
    return $this->doSuccess('ok',$data);
  }
}