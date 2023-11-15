<?php

namespace think\admin;

use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

class ExceptionHandle extends Handle
{
  public function render($request, Throwable $e): Response
  {
    // 参数验证错误
    if ($e instanceof ValidateException) {
      return json($e->getError(), 422);
    }


    if ($e instanceof HttpException && $request->param('_ajax')) {

      $resp['status'] = 400;
      $resp['message'] = $e->getMessage();
      $json = json($resp);
      return $json;
      // return response($e->getMessage(), $e->getStatusCode());
      return json($e->getMessage(), $e->getStatusCode());
    }else{
      $resp['status'] = 400;
      $resp['message'] = $e->getMessage();
      $json = json($resp);
      return $json;
    }
    // 其他错误交给系统处理
    return parent::render($request, $e);
  }

}