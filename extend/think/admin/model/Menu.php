<?php
namespace think\admin\model;

use JetBrains\PhpStorm\NoReturn;
use think\facade\Db;
use think\facade\View;
use think\Model;
class Menu extends Model
{

  public function createClass($vo)
  {
    // print_r($vo);
    // die();
    $schema = $vo['schema'];
    if (!$schema) $schema = $vo->schema;
    if (!$schema) return;
    $schema = json_decode($schema,true);
    // print_r($vo->schema);

    foreach ($schema as $key) {
      if ($key['type'] == 'controller') {
        //控制器
        $controller = View::fetch(root_path().'/extend/think/admin/builder/controller.class',[
          "schema"=>$key,
        ]);
        $controller = "<?php ".$controller;

        $filePath = root_path().strtolower($key['path']).$key['class'].'.php';
        if (!file_exists($filePath)) {
          file_put_contents($filePath,$controller);
        }

      }elseif ($key['type'] == 'model') {
        //模型
        $model = View::fetch(root_path().'/extend/think/admin/builder/model.class',[
          "schema"=>$key,
        ]);
        $model = "<?php ".$model;
        $filePath = root_path().strtolower($key['path']).$key['class'].'.php';
        if (!file_exists($filePath)) {
          file_put_contents($filePath,$model);
        }
      }
      elseif ($key['type'] == 'validate') {
        $validate = View::fetch(root_path().'/extend/think/admin/builder/validate.class',[
          "schema"=>$key,
        ]);
        $validate = "<?php ".$validate;
        $filePath = root_path().strtolower($key['path']).$key['class'].'.php';
        // print_r($filePath);
        // return;
        if (!file_exists($filePath)) {
          file_put_contents($filePath,$validate);
        }
      }
    }
  }

  public function createSchema($vo)
  {
    $crudFile = root_path().'extend/think/admin/builder/crud.json';
    $res = View::fetch($crudFile,[
      "model"=>$vo->model,
    ]);
    $this->where('id',$vo->id)->save([
      'schema'=>$res
    ]);
    $vo['schema'] = $res;
    // $vo->schema = $res;
    $vo->save();
    return $vo;
  }
  public function createAmisSchema($vo)
  {
    if (!$vo->fields) {
      $fields = json_encode(Db::getFields($vo->full_table),true);
      $vo->fields = $fields;
    }else{
      $fields = json_decode($vo->fields,true);
    }

    

    $crudFile = root_path().'extend/think/admin/builder/amis_schema.json';
    $res = View::fetch($crudFile,[
      "title"=>$vo['name'],
      "model"=>$vo->model,
      "fields"=>$fields,
    ]);
    $vo->amis_schema = $res;
    $vo->save();
    return $vo;
  }

  //创建字段
  public function createFields($vo)
  {
    $fields = Db::getFields($vo->full_table);
    foreach ($fields as &$key) {
      $key['type'] = Db::table($vo->full_table)->getFieldType($key['name']);
      if (!$key['comment']) {
        $key['comment'] = $key['name'];
      }
    }
    // $this->where('id',$vo->id)->save([
    //   'fields'=>json_encode($fields)
    // ]);
    $vo->fields = json_encode($fields);
    $vo->save();
    return $vo;
  }

  //生成权限节点 
  public function createMenuRule($vo)
  {
    
    $actions = [
      ["action"=>"search","name"=>"搜索"],
      ["action"=>"remove","name"=>"删除"],
      ["action"=>"info","name"=>"详情"],
      ["action"=>"update","name"=>"更新"],
      ["action"=>"insert","name"=>"新增"],
    ];
    
    $MenuRule = new \think\admin\model\MenuRule();

    $rows = [];
    foreach ($actions as $k) {
      // $url = (string) \think\facade\Route::buildUrl();
      $rule = (string)url("/admin/{$vo->model}/{$k['action']}")
                  ->suffix('')
                  ->root('/');
      $rule = str_replace("index.php/",'',$rule);
      $ck = $MenuRule->where('rule',$rule)->count('id');
      if (!$ck) {
        $rows[] = [
          'rule'=>$rule,
          'menu_id'=>$vo->id,
          'model'=>$vo->model,
          'name'=>$vo['name'].'-'.$k['name'],
        ];
      }
    }
    $MenuRule->insertAll($rows);
  }


  public function miniNav($user_type)
  {
    $PHPTree = new \think\admin\PHPTree();
//    $Menu = new \app\admin\model\Menu();
    $navs = array();
    $navs['homeInfo']['href'] = '/amis/views/page.html?amis_page_code=desktop';
    $navs['homeInfo']['title'] = '首页';

    $navs['logoInfo']['href'] = '';
    $navs['logoInfo']['title'] = '管理系统';

    $query = [];
    if ($user_type != '超级管理员'){
      $mids = Db::name('permission')
                ->where('type','menu')
                ->where('role_type',$user_type)
                ->column('menu_id');
      $query[] = ['id','in',$mids];
    }

    $list = $this
      ->field('id,name as title,url as href,`table`, `table` as tb,pid')
      ->where($query)
      ->where('status',1)
      // ->where('table','<>','')
      ->order('idx asc')
      ->select();
  
    foreach ($list as &$menu) {
      $menu['target'] = "_self";
      $menu['icon'] = 'fa fa-address-book';
      if ($menu->tb) {
        $menu['href'] = $menu->url?$menu->url:"/amis/views/page.html?amis_page_code=".$menu->tb;
      }else{
        // $menu['href'] = '';
      }

    }
    $PHPTree->leaf_key = 'child';
    $PHPTree->children_key = 'child';
    // print_r($PHPTree->leaf_key);
    // return;
    $list = $PHPTree->makeTree($list->toArray());

    $navs['menuInfo'] = $list;
    return $navs;


    $list = $this
      ->field('id,name as title,url as href')
      ->where($query)
      ->where('pid',0)
      ->where('status',1)
      ->where('table','<>','')
      //      ->where('amis_url','<>','')
      ->order('idx asc')
      ->select();
    foreach ($list as &$menu) {
      $menu['target'] = "_self";
      $menu['icon'] = 'fa fa-address-book';
      $child = $this
        ->field('id,name as title,url as href,amis_url,`table`, `table` as tb')
        ->where('pid',$menu->id)
        ->where('status',1)
        ->where('table','<>','')
//        ->where('amis_url','<>','')
        ->order('idx asc')
        ->select();
      foreach ($child as &$item) {
        $item['href'] = $menu->amis_url?$menu->amis_url:"/amis/views/page.html?amis_page_code=".$item->tb;
        $item['target'] = "_self";
      }
      $menu['child'] = $child;
    }
    $navs['menuInfo'] = $list;
    return $navs;
  }

  public function amisNav($type = '')
  {
    $PHPTree = new \think\admin\PHPTree();
    $adminQuery = [];
    if ($type != '超级管理员') {
      $menuids = Db::name('Permission')->where('type',$type)->column('menu_id');
      if ($menuids) {
        $adminQuery[] = ['id','in',$menuids];
      }
    }
    $list = $this
      ->field('id,name as label,`table` as tb,pid,amis_schema as `schema`,name,full_table,model')
      ->where($adminQuery)
      ->where('status',1)
      // ->where('table','<>','')
      ->order('idx asc')
      ->select()->toArray();
    $root[] = [
      "label"=>"首页",
      "url"=>"/",
      "redirect"=>"/index/index",
    ];
    $rows = [];
    foreach ($list as $menu) {
      $vo = [];
      $vo['id'] = $menu['id'];
      $vo['pid'] = $menu['pid'];
      $vo['label'] = $menu['label'];
      // print_r($menu);
      // return;
      
      if ($menu['schema'] && $menu['schema'] !='null' && $menu['pid'] > 0) {
        
        $vo['url'] = '/'.$menu['tb'].$menu['id'];
        // $menu['schemaApi'] = "/amis/views/page.html?amis_page_code=".$menu['tb'];
        // $vo['schemaApi'] = "/admin/AmisPage/page?amis_page_code=".$menu['tb'];
        // $vo['schema'] = [
        //   "type"=>"iframe",
        //   "src"=>"/amis/views/page.html?amis_page_code=".$menu['tb'],
        // ];
        $vo['schema'] = json_decode($menu['schema'],true);
      }elseif ($menu['schema'] == 'null' && $menu['full_table'] && $menu['model']) {
        // print_r($menu);
        // return;
        // $vo['schemaApi'] = "/admin/AmisPage/page?amis_page_code=".$menu['tb'];
        $vo['schema'] = [
          "type"=>"iframe",
          "src"=>"/amis/views/page.html?amis_page_code=".$menu['tb'],
        ];
        
      }
      else{
        // $vo['url'] = '/'.$menu['tb'].$menu['id'];
        // $menu['href'] = '';
      }
      $rows[] = $vo;
    }
    $PHPTree->leaf_key = '';
    $list = $PHPTree->makeTree($rows);
    $root['children'] = $list;
    // array_unshift($list,$root);
    $data['pages'] = $root;

    return $data;
  }


  public function win10Nav($user_type)
  {

    $query = [];
    if ($user_type != '超级管理员'){
      $mids = Db::name('permission')->where('type',$user_type)->column('menu_id');
      $query[] = ['id','in',$mids];
    }
    $appRows = $this
      ->where($query)
      ->where('pid','<>',0)
      ->where('status',1)
      ->where('table','<>','')
//      ->where('amis_url','<>','')
      ->order('idx asc')
      ->select()->toArray();
    $apps = [];
    $Faker = new \think\admin\Faker();
    foreach ($appRows as $menu) {
      // $color = sprintf("#%06X\n", mt_rand( 0, 0xFFFFFF ));
      $color = $Faker->color();
      $apps[$menu['table']] = [
        "addressBar"=> true,
        "autoRun"=> 0,
        "background"=> false,
        "badge"=> 0,
        "desc"=> $menu['name'],
        "icon"=> [
          "type"=> "str",
          "content"=> $menu['name'],
          "bg"=> $color
        ],
        "openMode"=> "normal",
        "plugin"=> false,
        "position"=> [
          "autoOffset"=> true,
          "left"=> true,
          "top"=> true,
          "x"=> "x*0.1",
          "y"=> "y*0.1"
        ],
        "version"=> "1.0.0",
        "poweredBy"=> "Yuri2",
        "resizable"=> true,
        "single"=> false,
        "size"=> [
          "height"=> "y*0.8-80",
          "width"=> "x*0.8"
        ],
        "title"=> $menu['name'],
        "url"=> "/amis/views/page.html",
        "customTile"=> "",
        "urlRandomToken"=> true
      ];

    }

    $shortcuts = [];
    $list = $Menu
      ->where('pid',0)
      ->order('idx asc')
      ->select();
    foreach ($list as $menu) {

      $parent = [
        "app"=> "amis-page",
        "title"=> $menu['name'],
        "params"=> [
          "amis_page_code"=>$menu['table']
        ],
        "hash"=> ""
      ];
      $children = [];
      foreach ($appRows as $appRow) {
        if ($menu['id'] != $appRow['pid']) continue;
        $pars = [
          'amis_page_code'=>$appRow['table']
        ];

        $children[] = [
          "app"=> $appRow['table'],
          "title"=> $appRow['name'],
          "params"=> $pars,
          "hash"=> ""
        ];
      }
      if ($children){
        $parent = [
          "title"=> $menu['name'],
          "children"=>$children,
        ];
        $shortcuts[] = $parent;
      }else{
        $shortcuts[] = $parent;
      }
    }

    $r['shortcuts'] = $shortcuts;
    $r['apps'] = $apps;
    return $r;
    // return $this->doSuccess('ok',$r);
  }
}