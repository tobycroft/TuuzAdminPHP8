<?php

namespace think\admin\traits\admin;
use think\Exception;

use think\Model;
use think\Request;
use think\facade\Db;
use think\admin\Time;

trait AmisPage
{
  public function _filter($request)
  {
    $searchKey = input('keywords');
    $query = [];
    if ($searchKey) {
      $query[] = ['name|code|table','like',"%{$searchKey}%"];
    }
    return $query;
  }

  public function update()
  {
    $data = input('post.');
    if (!$data['code']) {
      unset($data['code']);
    }
    if ($data['id']) {
      Db::name('amis_page')->where('id',$data['id'])->update($data);
    }else{
      Db::name('amis_page')->insert($data);
    }
    return $this->doSuccess('ok');
  }

  public function view()
  {
    $amis_page_id = input('amis_page_id');
    $code = input('code');
    $vo = Db::name('amis_page')
              ->where('id|code',$amis_page_id)
//              ->whereOr('code',$code)
              ->find();
    $vo['amis_page_id'] = $amis_page_id;
    $vo['amis_page_code'] = $code;
    return $this->doSuccess('ok',$vo);
  }

  public function page()
  {
    $amis_page_id = input('amis_page_id');
    $amis_page_code = input('amis_page_code');
    $vo = Db::name('amis_page')
              ->where('id',$amis_page_id)
              ->whereOr('code',$amis_page_code)
              ->find();
//    print_r($vo['json']);
//    return;
    $amisJson = json_decode($vo['json'],true);
    return $this->doSuccess('ok',$amisJson);
  }
}