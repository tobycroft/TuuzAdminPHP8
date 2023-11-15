<?php

namespace think\admin\model;

use think\admin\Model;
use think\model\concern\SoftDelete;

class DevHelper extends Model
{
  use SoftDelete;
  protected $deleteTime = 'delete_time';
  protected $defaultSoftDelete = 0;

}