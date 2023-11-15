<?php
namespace think\admin;

use think\facade\View;
use think\facade\Db;
class Crud
{
  public function initSchema()
  {
    $id = input('id');
    $controllerPath = input('controllerPath');
    $modelPath = input('modelPath');
    $M = new \app\admin\model\DevHelper();
    $model = $M::find($id);

    $cfg = Db::getConfig();
    $connections = $cfg['connections'][$cfg['default']];
    $dbname = $connections['database'];
    $prefix = $connections['prefix'];


    $tableName = str_replace($prefix,"",$model['table']);
    $tableName = parse_name($tableName,1);

    $res = View::fetch('../extend/think/admin/command/crud/crud.json',[
      "name"=>$model['name'],
      "table"=>$model['table'],
      "tableName"=>$tableName,
      "controllerPath"=>$controllerPath,
      "modelPath"=>$modelPath,
    ]);
    return $res;
  }
}