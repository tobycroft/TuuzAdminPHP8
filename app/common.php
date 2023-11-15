<?php
error_reporting(E_ALL & ~E_NOTICE);
/**
 * 统一json返回格式
 * @param int $code
 * @param string $message
 * @param array $data
 * @param int $httpStatus
 * @return \think\response\Json
 */
function show($code = 0,$message = "",$data = [],$httpStatus = 200) {
  $result = [
    "status" => $code,
    "message" => $message,
    "data" => $data
  ];
  return json($result, $httpStatus);
}
function toDatetime( $time, $format = 'Y-m-d H:i:s' ) {
  if ( empty ( $time ) ) {
    return "";
  }
  if ( is_numeric( $time ) ) {
    return date( $format, $time );
  }
  $format = str_replace( '#', ':', $format );
  return date( $format, strtotime( $time ) );
}

function toDate( $time, $format = 'Y-m-d' ) {
  if ( empty ( $time ) ) {
    return $time;
  }
  $ckdate = explode("-", $time);
  if (count($ckdate) > 2) {
    return $time;
  }
  // $is_date=is_int($time);
  // if (!$is_date) return $time;
  $format = str_replace( '#', ':', $format );
  return date( $format, $time );
}


function is_mobile($number = '')
{
  return preg_match("/^1[3456789]{1}\d{9}$/",$number);
}



function cut( $Str, $Length,$sss = '' ) {
    //$Str为截取字符串，$Length为需要截取的长度
    if (mb_strlen($Str) < $Length) {
      return $Str;
      # code...
    }
    global $s;
    $i = 0;
    $l = 0;
    $ll = strlen( $Str );
    $s = $Str;
    $f = true;
    //if(isset($Str{$i}))
    while ( $i <= $ll ) {
    if ( ord( $Str{$i} ) < 0x80 ) {
    $l++; $i++;
    } else if ( ord( $Str{$i} ) < 0xe0 ) {
    $l++; $i += 2;
    } else if ( ord( $Str{$i} ) < 0xf0 ) {
    $l += 2; $i += 3;
    } else if ( ord( $Str{$i} ) < 0xf8 ) {
    $l += 1; $i += 4;
    } else if ( ord( $Str{$i} ) < 0xfc ) {
    $l += 1; $i += 5;
    } else if ( ord( $Str{$i} ) < 0xfe ) {
    $l += 1; $i += 6;
    }

    if ( ( $l >= $Length - 1 ) && $f ) {
    $s = substr( $Str, 0, $i );
    $f = false;
    }

    if ( ( $l > $Length ) && ( $i < $ll ) ) {
    $s = $s . '...'; break; //如果进行了截取，字符串末尾加省略符号“...”
    // $s = $s . $sss; break; //如果进行了截取，字符串末尾加省略符号“...”
    }
    }
  return $s;
}

function strFilter($str){
    $str = str_replace('`', '', $str);
    $str = str_replace('·', '', $str);
    $str = str_replace('~', '', $str);
    $str = str_replace('!', '', $str);
    $str = str_replace('！', '', $str);
    $str = str_replace('@', '', $str);
    $str = str_replace('#', '', $str);
    $str = str_replace('$', '', $str);
    $str = str_replace('￥', '', $str);
    $str = str_replace('%', '', $str);
    $str = str_replace('^', '', $str);
    $str = str_replace('……', '', $str);
    $str = str_replace('&', '', $str);
    $str = str_replace('*', '', $str);
    $str = str_replace('(', '', $str);
    $str = str_replace(')', '', $str);
    $str = str_replace('（', '', $str);
    $str = str_replace('）', '', $str);
    $str = str_replace('-', '', $str);
    $str = str_replace('_', '', $str);
    $str = str_replace('——', '', $str);
    $str = str_replace('+', '', $str);
    $str = str_replace('=', '', $str);
    $str = str_replace('|', '', $str);
    $str = str_replace('\\', '', $str);
    $str = str_replace('[', '', $str);
    $str = str_replace(']', '', $str);
    $str = str_replace('【', '', $str);
    $str = str_replace('】', '', $str);
    $str = str_replace('{', '', $str);
    $str = str_replace('}', '', $str);
    $str = str_replace(';', '', $str);
    $str = str_replace('；', '', $str);
    $str = str_replace(':', '', $str);
    $str = str_replace('：', '', $str);
    $str = str_replace('\'', '', $str);
    $str = str_replace('"', '', $str);
    $str = str_replace('“', '', $str);
    $str = str_replace('”', '', $str);
    $str = str_replace(',', '', $str);
    $str = str_replace('，', '', $str);
    $str = str_replace('<', '', $str);
    $str = str_replace('>', '', $str);
    $str = str_replace('《', '', $str);
    $str = str_replace('》', '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace('。', '', $str);
    $str = str_replace('/', '', $str);
    $str = str_replace('、', '', $str);
    $str = str_replace('?', '', $str);
    $str = str_replace('？', '', $str);
    return trim($str);
}

function isDate($str,$format='Y-m-d'){
  $unixTime_1=strtotime($str);
  if(!is_numeric($unixTime_1)) return false; //如果不是数字格式，则直接返回
  $checkDate=date($format,$unixTime_1);
  $unixTime_2=strtotime($checkDate);
  if($unixTime_1==$unixTime_2){
      return true;
  }else{
      return false;
  }
}

//获取系统字典
function sysDict($dict_type='',$dict_value = '') {
  $arr = think\facade\Db::table('mscode_sys_dict')
                  ->where('dict_type',$dict_type)
                  ->where('dict_value',$dict_value)
                  ->find();
  if ($arr) return $arr;
  return '';
}


function getSettingCode($code = '')
{
  $arr = think\facade\Db::name('Sysdict')
                  ->where('code',$code)
                  ->find();
  return $arr['value'];
}

function getSettingType($type = '')
{
  $list = think\facade\Db::name('Sysdict')
                  ->where('type',$type)
                  ->select()->toArray();
  $arr = array();
  foreach ($list as $k) {
    $arr[$k['field']] = $k['value'];
  }
  return $arr;
}
function getSettingGroup($group = 'base')
{
  $list = think\facade\Db::name('Sysdict')
                  ->where('group',$group)
                  ->select()->toArray();
  $arr = array();
  foreach ($list as $k) {
    $arr[$k['field']] = $k['value'];
  }
  return $arr;
}



/**
  * PHP计算两个时间段是否有交集（边界重叠不算）
  *
  * @param string $beginTime1 开始时间1
  * @param string $endTime1 结束时间1
  * @param string $beginTime2 开始时间2
  * @param string $endTime2 结束时间2
  * @return bool
  */
function is_time_cross($beginTime1 = '', $endTime1 = '', $beginTime2 = '', $endTime2 = '') {
  $beginTime1 = str_replace(":",'',$beginTime1);
  $endTime1 = str_replace(":",'',$endTime1);
  $beginTime2 = str_replace(":",'',$beginTime2);
  $endTime2 = str_replace(":",'',$endTime2);
  $status = $beginTime2 - $beginTime1;
  if ($status > 0) {
      $status2 = $beginTime2 - $endTime1;
      if ($status2 >= 0) {
          return false;
      } else {
          return true;
      }
  } else {
      $status2 = $endTime2 - $beginTime1;
      if ($status2 > 0) {
          return true;
      } else {
          return false;
      }
  }
}
function filterEmoji($str)
{
  $str = preg_replace_callback( '/./u',
      function (array $match) {
        return strlen($match[0]) >= 4 ? '' : $match[0];
      },
      $str);
  return $str;
}

