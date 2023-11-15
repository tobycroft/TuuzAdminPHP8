<?php

namespace think\admin;

class Time
{
  /**
   * 返回今日开始和结束的时间戳
   *
   * @return array
   */
  public static function today()
  {
    list($y, $m, $d) = explode('-', date('Y-m-d'));
    return [
      mktime(0, 0, 0, $m, $d, $y),
      mktime(23, 59, 59, $m, $d, $y)
    ];
  }

  /**
   * 返回昨日开始和结束的时间戳
   *
   * @return array
   */
  public static function yesterday()
  {
    $yesterday = date('d') - 1;
    return [
      mktime(0, 0, 0, date('m'), $yesterday, date('Y')),
      mktime(23, 59, 59, date('m'), $yesterday, date('Y'))
    ];
  }

  /**
   * 返回本周开始和结束的时间戳
   *
   * @return array
   */
  public static function week()
  {
    list($y, $m, $d, $w) = explode('-', date('Y-m-d-w'));
    if ($w == 0) $w = 7; //修正周日的问题
    return [
      mktime(0, 0, 0, $m, $d - $w + 1, $y), mktime(23, 59, 59, $m, $d - $w + 7, $y)
    ];
  }

  /**
   * 返回上周开始和结束的时间戳
   *
   * @return array
   */
  public static function lastWeek()
  {
    $timestamp = time();
    return [
      strtotime(date('Y-m-d', strtotime("last week Monday", $timestamp))),
      strtotime(date('Y-m-d', strtotime("last week Sunday", $timestamp))) + 24 * 3600 - 1
    ];
  }

  /**
   * 返回本月开始和结束的时间戳
   *
   * @return array
   */
  public static function month($everyDay = false)
  {
    list($y, $m, $t) = explode('-', date('Y-m-t'));
    return [
      mktime(0, 0, 0, $m, 1, $y),
      mktime(23, 59, 59, $m, $t, $y)
    ];
  }

  /**
   * 返回上个月开始和结束的时间戳
   *
   * @return array
   */
  public static function lastMonth()
  {
    $y = date('Y');
    $m = date('m');
    $begin = mktime(0, 0, 0, $m - 1, 1, $y);
    $end = mktime(23, 59, 59, $m - 1, date('t', $begin), $y);

    return [$begin, $end];
  }

  /**
   * 返回今年开始和结束的时间戳
   *
   * @return array
   */
  public static function year()
  {
    $y = date('Y');
    return [
      mktime(0, 0, 0, 1, 1, $y),
      mktime(23, 59, 59, 12, 31, $y)
    ];
  }

  /**
   * 返回去年开始和结束的时间戳
   *
   * @return array
   */
  public static function lastYear()
  {
    $year = date('Y') - 1;
    return [
      mktime(0, 0, 0, 1, 1, $year),
      mktime(23, 59, 59, 12, 31, $year)
    ];
  }

  public static function dayOf()
  {
  }

  /**
   * 获取几天前零点到现在/昨日结束的时间戳
   *
   * @param int $day 天数
   * @param bool $now 返回现在或者昨天结束时间戳
   * @return array
   */
  public static function dayToNow($day = 1, $now = true)
  {
    $end = time();
    if (!$now) {
      list($foo, $end) = self::yesterday();
    }

    return [
      mktime(0, 0, 0, date('m'), date('d') - $day, date('Y')),
      $end
    ];
  }

  /**
   * 返回几天前的时间戳
   *
   * @param int $day
   * @return int
   */
  public static function daysAgo($day = 1)
  {
    $nowTime = time();
    return $nowTime - self::daysToSecond($day);
  }

  /**
   * 返回几天后的时间戳
   *
   * @param int $day
   * @return int
   */
  public static function daysAfter($day = 1)
  {
    $nowTime = time();
    return $nowTime + self::daysToSecond($day);
  }

  /**
   * 天数转换成秒数
   *
   * @param int $day
   * @return int
   */
  public static function daysToSecond($day = 1)
  {
    return $day * 86400;
  }

  /**
   * 周数转换成秒数
   *
   * @param int $week
   * @return int
   */
  public static function weekToSecond($week = 1)
  {
    return self::daysToSecond() * 7 * $week;
  }

  private static function startTimeToEndTime()
  {
  }

  /**
   * @param $begin_time
   * @param $end_time
   * @return array
   * @author: hhygyl
   * @name: timediff
   * @describe:计算两个时间戳之间相差的日时分秒
   */
  public static function timediff($begin_time, $end_time)
  {
    if ($begin_time < $end_time) {
      $starttime = $begin_time;
      $endtime = $end_time;
    } else {
      $starttime = $end_time;
      $endtime = $begin_time;
    }

    //计算天数
    $timediff = $endtime - $starttime;
    $days = intval($timediff / 86400);
    //计算小时数
    $remain = $timediff % 86400;
    $hours = intval($remain / 3600);
    //计算分钟数
    $remain = $remain % 3600;
    $mins = intval($remain / 60);
    //计算秒数
    $secs = $remain % 60;
    $res = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
    return $res;
  }
  public static function toDatetime( $time, $format = 'Y-m-d H:i:s' ) {
  if ( empty ( $time ) ) {
    return "";
  }
  if ( is_numeric( $time ) ) {
    return date( $format, $time );
  }
  $format = str_replace( '#', ':', $format );
  return date( $format, strtotime( $time ) );
}

public static function toDate( $time, $format = 'Y-m-d' ) {
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

  /**
   * @param $date_time
   * @param int $type
   * @param bool $format
   * @return false|string
   * @author: hhygyl <hhygyl520@qq.com>
   * @name: format_datetime
   * @describe:友好的时间显示
   */
  public static function friendlyDate($sTime, $type = 'normal', $alt = 'false')
  {
    if (!$sTime) {
      return '';
    }
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime = time();
    $dTime = $cTime - $sTime;
    $dDay  = intval(date("z", $cTime)) - intval(date("z", $sTime));
    //$dDay     =   intval($dTime/3600/24);
    $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));
    //normal：n秒前，n分钟前，n小时前，日期
    if ($type == 'normal') {
      if ($dTime < 60) {
        if ($dTime < 10) {
          return '刚刚'; //by yangjs
        } else {
          return intval(floor($dTime / 10) * 10) . "秒前";
        }
      } elseif ($dTime < 3600) {
        return intval($dTime / 60) . "分钟前";
        //今天的数据.年份相同.日期相同.
      } elseif ($dYear == 0 && $dDay == 0) {
        //return intval($dTime/3600)."小时前";
        return '今天' . date('H:i', $sTime);
      } elseif ($dYear == 0) {
        return date("m月d日 H:i", $sTime);
      } else {
        return date("Y-m-d", $sTime);
      }
    } elseif ($type == 'mohu') {
      if ($dTime < 60) {
        return $dTime . "秒前";
      } elseif ($dTime < 3600) {
        return intval($dTime / 60) . "分钟前";
      } elseif ($dTime >= 3600 && $dDay == 0) {
        return intval($dTime / 3600) . "小时前";
      } elseif ($dDay > 0 && $dDay <= 7) {
        return intval($dDay) . "天前";
      } elseif ($dDay > 7 && $dDay <= 30) {
        return intval($dDay / 7) . '周前';
      } elseif ($dDay > 30) {
        return intval($dDay / 30) . '个月前';
      }
      //full: Y-m-d , H:i:s
    } elseif ($type == 'full') {
      return date("Y-m-d H:i:s", $sTime);
    } elseif ($type == 'ymd') {
      return date("Y-m-d", $sTime);
    } else {
      if ($dTime < 60) {
        return $dTime . "秒前";
      } elseif ($dTime < 3600) {
        return intval($dTime / 60) . "分钟前";
      } elseif ($dTime >= 3600 && $dDay == 0) {
        return intval($dTime / 3600) . "小时前";
      } elseif ($dYear == 0) {
        return date("Y-m-d H:i:s", $sTime);
      } else {
        return date("Y-m-d H:i:s", $sTime);
      }
    }
  }



  /**
   * @param StringService $timestamp
   * @return bool
   * @author: hhygyl
   * @name: is_timestamp
   * @describe:判断是否是时间戳
   */
  public static function is_timestamp($timestamp = '')
  {
    if (!$timestamp) return false;
    return $is_unixtime = ctype_digit($timestamp) && $timestamp <= 2147483647;
  }



  /**
   * @param null $time
   * @param StringService $format
   * @return false|StringService
   * @author: hhygyl <hhygyl520@qq.com>
   * @name: time_format
   * @describe:时间戳格式化ibe:
   */
  public static function time_format($time = NULL, $format = 'Y-m-d H:i')
  {
    $time = $time === NULL ? time() : intval($time);
    return date($format, $time);
  }



  /**
   * @param int $time
   * @return bool|StringService
   * @author: hhygyl <hhygyl520@qq.com>
   * @name: get_surplus_time
   * @describe:转换剩余时间格式
   */
  public static function get_surplus_time($time = 0)
  {
    if (!$time) {
      return false;
    }
    if ($time < 0) {
      return '已结束';
    } else {
      if ($time < 60) {
        return $time . '秒';
      } else {
        if ($time < 3600) {
          return floor($time / 60) . '分钟';
        } else {
          if ($time < 86400) {
            return floor($time / 3600) . '小时';
          } else {
            if ($time < 259200) { //3天内
              return floor($time / 86400) . '天';
            } else {
              return floor($time / 86400) . '天';
            }
          }
        }
      }
    }
  }

  /**
   * @param $str_time
   * @param StringService $format
   * @return bool
   * @author: hhygyl <hhygyl520@qq.com>
   * @name: check_date_time
   * @describe:判断是否日期时间
   */
  public static function check_date_time($str_time, $format = "Y-m-d H:i:s")
  {
    $unix_time = strtotime($str_time);
    $check_date = date($format, $unix_time);
    if ($check_date == $str_time) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @param int $some
   * @param null $day
   * @return false|float|int|null
   * @author: hhygyl <hhygyl520@qq.com>
   * @name: get_some_day
   * @describe:获取n天前0点的时间戳
   */
  public static function get_some_day($some = 30, $day = null)
  {
    $time = $day ? $day : time();
    $some_day = $time - 60 * 60 * 24 * $some;
    $btime = date('Y-m-d' . ' 00:00:00', $some_day);
    $some_day = strtotime($btime);
    return $some_day;
  }
}
