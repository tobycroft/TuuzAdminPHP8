<?php

namespace think\admin\traits\admin;
use think\Exception;

use think\Request;
use think\facade\Db;
trait Menu
{

  public function _filter($request)
  {
    $searchKey = input('searchKey');
    $query = [];
    if (is_numeric($searchKey)) {
      $query[] = ['id|pid','=',$searchKey];
    }else if ($searchKey) {
      $query[] = ['name|id|pid|url','like',"%{$searchKey}%"];
    }
    $pid = input('pid');
    if (is_numeric($pid)) {
      $query[] = ['pid','=',$pid];
    }

    $id = input('id');
    if (is_numeric($id)) {
      $query[] = ['id','=',$id];
    }

    $name = input('name');
    if ($name) {
      $query[] = ['name','like',"%{$name}%"];
    }
    return $query;
  }
  public function copy()
  {
    $id = input('id');
    $model = $this->getModel();
    $vo = $model::find($id)->toArray();
    unset($vo['id']);
    if (isset($vo['name'])) {
      $vo['name'] = '复制_' . $vo['name'];
    } elseif (isset($vo['title'])) {
      $vo['namtitlee'] = '复制_' . $vo['title'];
    }
    $vo['fields'] = null;
    $vo['amis_schema'] = null;
    $vo['schema'] = null;
    $vo['create_time'] = time();
    $model->insert($vo);
    return $this->doSuccess('ok');
  }

  public function getAll()
  {

    $Menu = new \think\admin\model\Menu();
    $rows = $Menu->where('status',1)->order('idx asc')->select();
    $PHPTree =new \think\admin\PHPTree();
    $list = $PHPTree->makeTree($rows->toArray());

    return $this->doSuccess('ok',$list);
  }

  public function getAllTree()
  {

    $Menu = new \think\admin\model\Menu();
    $rows = $Menu
              ->field('name as label,pid,id,id as value')
              ->where('status',1)
              ->order('idx asc')->select();
    $PHPTree =new \think\admin\PHPTree();
    $list = $PHPTree->makeTree($rows->toArray());

    $root['label'] = '所有';
    $root['value'] = '0';
    $root['type'] = 'root';
    $root['children'] = $list;
    $r['options'][] = $root;

    return $this->doSuccess('ok',$r);

  }
  public function saveOrder()
  {
    $ids = input('ids');
    $idxs = explode(",",$ids);

    $idx = 1;
    foreach ($idxs as $id) {
      Db::name('menu')
              ->where('id',$id)
              ->update([
                'idx'=>$idx
              ]);
      $idx++;
    }
    return $this->doSuccess('ok',$idxs);
  }
  
  public function miniNav(Request $request)
  {
    $AdminMenu = new \think\admin\model\Menu();
    $res = $AdminMenu->miniNav($request->admin->type);
    return $this->doSuccess('ok',$res);
  }

  public function win10Nav(Request $request)
  {
    $AdminMenu = new \think\admin\model\Menu();
    $res = $AdminMenu->win10Nav($request->admin->type);
    // print_r($res);
    // return;
    return $this->doSuccess('ok',$res);
  }

  public function amisNav(Request $request)
  {
    $AdminMenu = new \think\admin\model\Menu();
    $res = $AdminMenu->amisNav($request->admin->type);
    return $this->doSuccess('ok',$res);
  }

  public function amisPage()
  {
    $amis_page_id = input('amis_page_id');
    $amis_page_code = input('amis_page_code');
    $AdminMenu = new \think\admin\model\Menu();
    $vo = $AdminMenu
              ->where('id',$amis_page_id)
              ->whereOr('model|table',$amis_page_code)
              ->find();
    //判断字段信息
    if ($vo->fields == 'null') {
      $vo = $AdminMenu->createFields($vo);
    }
    if ($vo->amis_schema == 'null') {
      $vo = $AdminMenu->createAmisSchema($vo);
    }
    if ($vo->schema == 'null') {
      $vo = $AdminMenu->createSchema($vo);
      // print_r($vo->schema);
      // print_r($vo['schema']);

      $AdminMenu->createClass($vo);
      $AdminMenu->createMenuRule($vo);
    }
    $amisJson = json_decode($vo->amis_schema,true);
    return $this->doSuccess('ok',$amisJson);
  }

  public function restFields()
  {
    $id = input('id');
    $AdminMenu = new \think\admin\model\Menu();
    $vo = $AdminMenu->find($id);
    $table = $vo->full_table;

    $post = [];
    $fields = Db::getFields($table);
    foreach ($fields as &$key) {
      $key['type'] = Db::table($table)->getFieldType($key['name']);
    }

    $vo->fields = json_encode($fields);
    $vo->save();
    return $this->doSuccess('ok',[
      'fields'=>json_encode($fields)
    ]);
  }

  public function restAmisPage()
  {
    $id = input('id');
    $AdminMenu = new \think\admin\model\Menu();
    $vo = $AdminMenu
              ->where('id',$id)
              ->find();
    $vo = $AdminMenu->createAmisSchema($vo);
    return $this->doSuccess('ok',[
      'amis_schema'=>$vo->amis_schema
    ]);
  }

  public function createClass()
  {
    $id = input('id');
    $AdminMenu = new \think\admin\model\Menu();
    $vo = $AdminMenu
              ->where('id',$id)
              ->find();
    $AdminMenu->createClass($vo);
    $AdminMenu->createMenuRule($vo);
    return $this->doSuccess('ok');
    # code...
  }

  public function restSchema()
  {
    $id = input('id');
    $AdminMenu = new \think\admin\model\Menu();
    $vo = $AdminMenu
              ->where('id',$id)
              ->find();
    $vo = $AdminMenu->createSchema($vo);
    return $this->doSuccess('ok',[
      'schema'=>$vo->schema
    ]);
  }

  public function createCrud()
  {
    $AdminMenu = new \think\admin\model\Menu();
    $list = $AdminMenu
            ->where('table','<>','')
            ->select();

    $cfg = Db::getConfig();
    $connections = $cfg['connections'][$cfg['default']];
    $prefix = $connections['prefix'];

    foreach ($list as $k) {
      $full_table = parse_name($k['table'],1);
      $AdminMenu->where('id',$k['id'])->update([
        'table'=>$full_table,
      ]);
      print_r($full_table);
    }
  }
}