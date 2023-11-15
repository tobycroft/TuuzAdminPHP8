<?php
declare (strict_types = 1);

namespace app\admin\controller;

class Dict extends Base
{
  use \think\admin\traits\admin\Dict;
  
  public function append()
  {
    return ['code_txt'];
  }
  

}
