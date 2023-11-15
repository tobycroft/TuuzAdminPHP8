<?php
declare (strict_types = 1);
namespace think\admin\model;

use think\Model;
class AmisPage extends Model
{
  public function view($id)
  {
    $vo = Db::name('amis_page')
      ->where('id|code',$id)
//              ->whereOr('code',$code)
      ->find();
    $vo['amis_page_id'] = $id;
    $vo['amis_page_code'] = $id;
    return $vo;
  }

  public function page($id)
  {
    $vo = Db::name('amis_page')
      ->where('id',$id)
      ->whereOr('code',$id)
      ->find();
    $amisJson = json_decode($vo['json'],true);
    return $amisJson;
  }
}

