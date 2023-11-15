<?php

namespace think\admin;

use think\Response;

class CrossDomain
{
  public function handle($request, \Closure $next)
  {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 1800');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');
    header('Access-Control-Allow-Headers: token,uid,utoken,accesstoken, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With');
    // header('Access-Control-Allow-Headers: token,utoken, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With,Authorization,token,uid,access_token');
    if (strtoupper($request->method()) == "OPTIONS") {
      return Response::create()->send();
    }
    return $next($request);
  }
}