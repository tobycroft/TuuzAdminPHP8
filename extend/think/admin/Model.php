<?php

namespace think\admin;

use think\Model as tpModel;
use think\facade\Db;
use think\admin\Faker;
class Model extends tpModel
{
  private function getRow()
  {
    $Faker = new \think\admin\Faker();
    $dbList = Db::query("show full columns from ".$this->getTable());
    $values = array_column($dbList,'Comment');
    $fields = array_column($dbList,'Field');
    $vo = array_combine($fields ,$values);
    foreach ($vo as $k=>$v) {
      if ($k == 'status') $vo[$k] = rand(0,1);
      if ($k == 'ctime') $vo[$k] = time();
      if ($k == 'mobile') $vo[$k] = $Faker->mobile();
      if ($k == 'login_ip') $vo[$k] = $Faker->ip();
      if ($k == 'last_ip') $vo[$k] = $Faker->ip();
      if ($k == 'account') $vo[$k] = $Faker->en(6);
    }
    return $vo;
  }
  public function faker()
  {
    $row = $this->getRow();
    return $row;
  }
  public function fakerList()
  {
    $rows = array();
    for ($i = 0;$i<10;$i++)
    {
      $row = $this->getRow();
      $rows[] = $row;
    }
    return $rows;
  }
}