<?php
declare(strict_types=1);
namespace think\admin\middleware;

use think\admin\model\Syslog;
use think\facade\Request;

class Auth
{
  protected $notCheckController = [
    'login',
    'amispage',
    'common',
  ];

  /***
   * @param $request
   * @param \Closure $next
   * @return mixed|\think\response\Json|\think\response\Redirect
   */
  public function handle($request, \Closure $next)
  {

    


    //操作日志
    $Syslog = new Syslog();
    $Syslog->url = $request->url();
    $Syslog->status = 1;

    $action = $request->action(true);
    $controller = $request->controller(true);
    //排除不检查的控制器
    if (in_array($controller,$this->notCheckController)){
      return $next($request);
    }
    //登录验证
    $token = $request->header('token');
    if (!$token) {
      $Syslog->status = 0;
      $Syslog->save();
      return show(401,'token不能为空');
    }
    //权限验证
    $Admin = new \think\admin\model\Admin();
    $admin = $Admin->checkAuth($token);
    if (!$admin){
      $Syslog->status = 0;
      $Syslog->save();
      return show(402,'权限不足');
    }
    $Syslog->admin_id = $admin->id;
    $Syslog->save();


    $request->token = $token;
    $request->admin = $admin;
    $request->admin_id = $admin['id'];


    return $next($request);
//    return $response;

  }

  /**
   * 中间件结束调度
   * @param \think\Response $response
   */
  public function end(\think\Response $response)
  {

  }

}