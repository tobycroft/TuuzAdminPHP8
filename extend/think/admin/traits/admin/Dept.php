<?php

namespace think\admin\traits\admin;

trait Dept
{
  public function getAll()
  {

    $Dept = new \think\admin\model\Dept();
    $rows = $Dept->field('name as label,id as value ,pid,id')->order('idx asc')->select();
    $PHPTree =new \think\admin\PHPTree();
    $list = [];
    if (count($rows) > 0) {
      $list = $PHPTree->makeTree($rows->toArray());
    }

    return $this->doSuccess('ok',$list);
  }
}