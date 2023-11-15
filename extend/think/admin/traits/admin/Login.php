<?php

namespace think\admin\traits\admin;
use think\Exception;

use think\facade\Db;
use think\admin\Time;

trait Login
{
  public function doLogin()
  {
    $account = input('account');
    $password = input('password');
    if (!$account || !$password) {
      return $this->doError('请输入登录信息');
    }
    $Admin = new \think\admin\model\Admin();
    
    $user = $Admin
          ->where('account',$account)
          ->where('password',md5($password))
          ->findOrEmpty();
    if ($user) {
      if ($user->status <= 0) {
        return $this->doError('该账户被禁用');
      }
      $log = array();
      $dutime = Time::daysAgo(7);
      if ($user['token_time'] < $dutime) {
        $log['token'] = md5(time().rand(1111,99999999));
      }else{
        $log['token'] = $user['token'];
      }
      $log['last_time'] = $user['login_time'];
      $log['last_ip'] = $user['login_ip'];
      $log['logins'] = $user['logins']+1;
      $log['utime'] = time();
      $log['login_time'] = time();
      $log['token_time'] = time();
      $log['login_ip'] =  $_SERVER["REMOTE_ADDR"];
      $Admin->where('id',$user['id'])->update($log);
      $user['token_time'] = $log['token_time'];
      // $user['token'] = $log['token'];
      $r = $Admin
                  // ->field('id,head,token,token_time,type,account,name,mobile')
                  ->where('account',$account)
                  ->where('password',md5($password))
                  ->find();
      $r['token_time'] = Time::toDatetime($user['token_time']+604800);
      return $this->doSuccess('登陆成功',$r);
    }
    return $this->doError('登陆失败');
  }
}