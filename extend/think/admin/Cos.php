<?php

namespace think\admin;

class Cos
{
  private $secretId = "AKIDdIBpCB1YsvjGUoJtahZAWUBevye3c46a"; //"云 API 密钥 SecretId";
  private $secretKey = "LPWlOnsCjV0ElUx80pjNmtySivxkHYM7"; //"云 API 密钥 SecretKey";
  private $region = "ap-guangzhou"; //设置一个默认的存储桶地域
  private $bucket = 'cos-tieninc-1257984526';
  public function upload($file,$key='',$ext='')
  {
//    $base = getSettingGroup('腾讯云');
//    $this->secretId = $base['tencent_secret_id'];
//    $this->secretKey = $base['tencent_secret_key'];
//    $this->region = $base['tencent_region'];
//    $this->bucket = $base['tencent_bucket'];


    $cosClient = new \Qcloud\Cos\Client(
      array(
        'region' => $this->region,
        // 'schema' => 'https', //协议头部，默认为http
        'credentials'=> array(
          'secretId'  => $this->secretId ,
          'secretKey' => $this->secretKey
        )
      )
    );
    $tmp = $file['tmp_name'];
    $mimeType = '';

    $name = $file['name'];
    if ($name){
      $name_arr = explode(".",$name);
      if (count($name_arr)>1){
        $ext = $name_arr[count($name_arr)-1];
      }
    }


    if (!$ext) {
      $mimeType = mime_content_type($tmp);
      $extension = explode("/",$mimeType);
      $ext = $extension[1];
    }

    try {
      $key = 'amis/'.date('Ymd').$tmp.'.'.$ext;
      if ($name){
        $key = 'amis/'.date('Ymd').'/'.rand(10,9999).'/'.$name.'.'.$ext;
      }


      $result = $cosClient->putObject(array(
        'Bucket' => $this->bucket,
        'Key' => $key,
        'Body' => fopen($tmp, 'rb'),
        'ContentType' => $mimeType
      ));
      $url = 'https://cos.tieninc.com/'.$key;
      $vo = array();
      $vo['url'] = $url;
      $vo['key'] = $key;
      $vo['ext'] = $ext;
      $vo['name'] = $file['name'];
      $vo['mime'] = $mimeType;
      return $vo;
    } catch (\Exception $e) {
      return $e;
    }
  }

  public function getTags($Key)
  {
    $cosClient = new \Qcloud\Cos\Client(
      array(
        'region' => $this->region,
        // 'schema' => 'https', //协议头部，默认为http
        'credentials'=> array(
          'secretId'  => $this->secretId ,
          'secretKey' => $this->secretKey
        )
      )
    );
    try {
      $result = $cosClient->DetectLabel(array(
        'Bucket' => $this->bucket,
        'Key' => $Key,
      ));
      $tags_arr = [];
      foreach ($result['Labels'] as $k) {
        $tags_arr[] = $k['Name'];
      }
      $tags = implode(",",$tags_arr);
      return $tags;
    } catch (\Exception $e) {
      return $e;
    }
  }

  public function remove($Key)
  {
    $cosClient = new \Qcloud\Cos\Client(
      array(
        'region' => $this->region,
        // 'schema' => 'https', //协议头部，默认为http
        'credentials'=> array(
          'secretId'  => $this->secretId ,
          'secretKey' => $this->secretKey
        )
      )
    );
    try {
      $result = $cosClient->deleteObject(array(
        'Bucket' => $this->bucket, //格式：BucketName-APPID
        'Key' => $Key,
        // 'VersionId' => 'exampleVersionId' //存储桶未开启版本控制时请勿携带此参数
      ));
      return true;
      print_r($result);
    } catch (\Exception $e) {
      // echo($e);
    }
  }
}