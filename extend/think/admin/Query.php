<?php

namespace think\admin;

use think\Collection;
use think\db\Query as TpQuery;
class Query extends  TpQuery
{
  public function select($data = null): Collection
  {
    return parent::select($data);
  }

  public function find($data = null)
  {
    return parent::find($data);
  }


}