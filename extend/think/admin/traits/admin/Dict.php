<?php

namespace think\admin\traits\admin;
use think\Exception;

use think\Model;
use think\Request;
use think\facade\Db;
use think\admin\Time;

trait Dict
{
  public function getModel()
  {
    return new \think\admin\model\Dict();
  }
  public function _filter($request)
  {
    $searchKey = input('searchKey');
    $query = [];
    if ($searchKey) {
      $query[] = ['name|id|pid|url','like',"%{$searchKey}%"];
    }


    $group = input('group');
    if ($group) {
      $query[] = ['group','=',$group];
    }

    $type = input('type');
    if ($type) {
      $query[] = ['type','=',$type];
    }
    
    return $query;
  }
  public function getGroups()
  {
    $list = Db::table($this->getTable())
              ->field('`group`,`group` as `label`,`group` as `value`')
              ->group('group')
              ->select()->toArray();
    return $this->doSuccess('ok',$list);
  }


  public function getDict()
  {
    $Dict = $this->getModel();
    $name = input('name');
    $query = [];
    if ($name){
      $query[] = ['name','=',trim($name)];
    }
    $parent = $Dict
//      ->field('id as value,name as label,field,group')
      ->where($query)
      ->where('pid',0)
      ->order('idx asc')
      ->findOrEmpty();

    if ($parent->isEmpty()){
      return $this->doSuccess('ok',[]);
    }
    $list = $Dict
      ->field('`value`,name as label,field,group')
      ->where('pid',$parent->id)
      ->order('idx asc')
      ->select();
    return $this->doSuccess('ok',$list);
  }


  public function getParents()
  {
    $list = Db::name('dict')
      ->field('id as value,name as label,field,group')
      ->where('pid',0)
      ->order('idx asc')
      ->select()->toArray();
    return $this->doSuccess('ok',$list);
  }

  public function getAll()
  {
    $list = Db::name('dict')
      ->where('pid',0)
      ->order('idx asc')
      ->select()->toArray();
    foreach ($list as &$k) {
      $v = array();

      $children = Db::name('dict')
        ->where('pid',$k['id'])
        ->order('idx asc')
        ->select()->toArray();
      foreach ($children as &$key) {

        $subs = Db::name('dict')
          ->where('pid',$key['id'])
          ->order('idx asc')
          ->select()->toArray();
        if ($subs) {
          $key['children'] = $subs;
        }

      }
      $k['children'] = $children;
    }
    $data['total'] = 100;
    $data['items'] = $list;
    return $this->doSuccess('ok',$data);
  }

  public function getTypes()
  {
    $list = Db::table($this->getTable())
              ->field('`type`,`type` as `label`,`type` as `value`')
              ->group('type')
              ->select()->toArray();
    return $this->doSuccess('ok',$list);
  }
  
}