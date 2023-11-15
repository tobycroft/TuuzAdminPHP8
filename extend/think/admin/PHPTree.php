<?php

namespace think\admin;
/**
 * @name PHPTree
 * @author crazymus < QQ:291445576 >
 * @des PHP生成树形结构,无限多级分类
 * @version 1.2.0
 * @Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * @updated 2015-08-26

 *
 * https://my.oschina.net/crazymus/blog/491174
 */
class PHPTree
{
  public $primary_key = 'id';
  public $parent_key = 'pid';
  public $expanded_key = 'expanded';
  public $leaf_key = 'children';
  public $children_key = 'children';
  public $expanded = false;

  /* 结果集 */
  protected $result = array();

  /* 层次暂存 */
  protected $level = array();
  /**
   * @name 生成树形结构
   * @param array 二维数组
   * @return mixed 多维数组
   */
  public function makeTree($data,$options=array() ){
    $dataset = $this->buildData($data,$options);
    $r = $this->makeTreeCore(0,$dataset,'normal');
    return $r;
  }

  /* 生成线性结构, 便于HTML输出, 参数同上 */
  public function makeTreeForHtml($data,$options=array()){

    $dataset = $this->buildData($data,$options);
    $r = $this->makeTreeCore(0,$dataset,'linear');
    return $r;
  }

  /* 格式化数据, 私有方法 */
  private function buildData($data,$options){
//    $config = array_merge($this->$config,$options);
    $r = array();
    foreach($data as $item){
      $id = $item[$this->primary_key];
      $parent_id = $item[$this->parent_key];
      $r[$parent_id][$id] = $item;
    }

    return $r;
  }

  /* 生成树核心, 私有方法  */
  private function makeTreeCore($index,$data,$type='linear')
  {
    foreach($data[$index] as $id=>$item)
    {
      if($type=='normal'){
        if(isset($data[$id]))
        {
          $item[$this->expanded_key]= $this->expanded;
          $item[$this->children_key]= $this->makeTreeCore($id,$data,$type);
        }
        else
        {
          $item[$this->leaf_key]= true;
        }
        $r[] = $item;
      }else if($type=='linear'){
        $parent_id = $item[$this->parent_key];
        $this->level[$id] = $index==0?0:$this->level[$parent_id]+1;
        $item['level'] = $this->$level[$id];
        $this->result[] = $item;
        if(isset($data[$id])){
          $this->makeTreeCore($id,$data,$type);
        }

        $r = $this->result;
      }
    }
    return $r;
  }
}