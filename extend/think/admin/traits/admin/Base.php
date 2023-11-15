<?php

namespace think\admin\traits\admin;
use think\Exception;

use think\Model;
use think\Request;
use think\facade\Db;
use think\admin\Time;

trait Base
{

  protected function getModel(){}

  protected function withMap()
  {
    return [];
  }
  protected function append()
  {
    return [];
  }
  protected function _filter($request)
  {
    return [];
  }

  public function search(\think\Request $request)
  {

    $query = [];

    $perPage = input('perPage/d',20);
    $page = input('page/d',1);
    $orderDir = input('orderDir','desc');
    $orderBy   = input('orderBy','id');

    $query = $this->_filter($request);
    $model = $this->getModel();

    if ($this->withMap()){
      $model->with($this->withMap());
    }


    $rows = $model
      ->with($this->withMap())
      ->append($this->append())
      ->where($query)
      ->order($orderBy.' '.$orderDir)
      ->page($page,$perPage)
      ->select();
    $sql = $model->getLastSql();
    $total = $model
      ->where($query)
      ->count('id');
    // $data['page'] =1;
    $data['items'] = $rows;
    $data['total'] = $total;
    $data['sql'] = $sql;

    return $this->doSuccess('ok',$data);
  }

  public function bacthUpdate()
  {
    $post = input('post.');
    $ids = input('ids');
    $model = $this->getModel();
    // $model = $model::find($id);
    $pk = $model->whereIn('id',$ids)->save($post);
    if ($pk > 0) {
      return $this->doSuccess('ok');
    }
    return $this->doSuccess('ok');
    # code...
  }

  public function update()
  {

    $post = input('post.');
    $id = input('id');
    $model = $this->getModel();
    $model = $model::find($id);
    $pk = $model->save($post);
    if ($pk > 0) {
      return $this->doSuccess('ok');
    }
    return $this->doSuccess('ok');

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
    $vo['create_time'] = time();
    $model->insert($vo);
    return $this->doSuccess('ok');
  }

  public function info()
  {
    $model = $this->getModel();
    $id = input('id');
    $vo = $model->with($this->withMap())->where('id',$id)->append($this->append())->find();
    return $this->doSuccess('ok',$vo);
  }

  public function amisUpdate()
  {
    $model = $this->getModel();
    $rowsDiff = input('rowsDiff');
    foreach ($rowsDiff as $row) {
      $id = $row['id'];
      $pk = $model
              ->where('id',$id)
              ->update($row);
    }
    return $this->doSuccess('ok');
    # code...
  }

  public function amisUploadImg()
  {
    $file = $_FILES['file'];
    $Oss = new \app\common\Oss();
    $key = "comments/{$this->getTable()}/".date('Ym/d')."/".rand(111,999);
    $r = $Oss->upload($file,$key);
    $res['value'] = $r['url'];
    return $this->doSuccess('ok',$res);
  }

  public function bacthRemove()
  {
    // $post = input('post.');
    $ids = input('ids');
    $model = $this->getModel();
    // // $model = $model::find($id);
    $pk = $model->whereIn('id',$ids)->destroy();
    // if ($pk > 0) {
    //   return $this->doSuccess('ok');
    // }
    return $this->doSuccess('ok');
    # code...
  }

  public function insert()
  {

    $model = $this->getModel();
    $form = input('post.');

    $model->save($form);
    return $this->doSuccess('ok');
  }

  public function remove()
  {
    $model = $this->getModel();
    $id = input('id');
    $ret = $model::destroy($id);
    if ($ret) {
      # code...
    }
    return $this->doSuccess('ok');
  }

  public function upimage()
  {
    $Cos = new \think\admin\Cos();
    $file = $_FILES['file'];
    $r = $Cos->upload($file);
//    print_r($r);
    return $this->doSuccess('ok',$r);
  }

  public function upfile()
  {
    $Cos = new \think\admin\Cos();
    $file = $_FILES['file'];
    $r = $Cos->upload($file);
    return $this->doSuccess('ok',$r);
  }

  public function doError($msg = '')
  {
    $resp['status'] = -1;
    $resp['msg'] = $msg;
    $json = json($resp, 200);
    return $json;
  }

  public function doSuccess($msg = '', $data = '')
  {
    $resp['status'] = 0;
    $resp['msg'] = $msg;
    $resp['data'] = $data;
    $json = json($resp, 200);
    return $json;
  }
}