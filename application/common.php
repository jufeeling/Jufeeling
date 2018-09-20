<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function result($data = [],$msg = 'OK', $code = 0)
{
    return json([
        'msg' => $msg,
        'data' => $data,
        'code' => $code
    ]);
}

/**
 * @param string $url get请求地址
 * @param int $httpCode 返回状态码
 * @return mixed
 */
function curl_get($url, &$httpCode = 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //不做证书校验,部署在linux环境下请改为true
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $file_contents = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $file_contents;
}

/**
 * @param string $url post请求地址
 * @param array $params
 * @return mixed
 */
function curl_post($url, array $params = array())
{
    $data_string = json_encode($params);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt(
        $ch, CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json'
        )
    );
    $data = curl_exec($ch);
    curl_close($ch);
    return ($data);
}

function getRandChar($length)
{
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;

    for ($i = 0;
         $i < $length;
         $i++) {
        $str .= $strPol[rand(0, $max)];
    }

    return $str;
}

function curlDownFile($img_url, $filename = '') {
    // curl下载文件
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $img_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $img = curl_exec($ch);
    curl_close($ch);
    // 保存文件到制定路径
    file_put_contents($filename, $img);
    unset($img, $url);
    return true;
}

function timeTransformation($time){
    $nowTimeStamp = strtotime('now');
    $day = floor(($nowTimeStamp - $time) / 86400);
    if($day == 0){
        $hour = floor(($nowTimeStamp - $time) / 3600);
        if($hour == 0){
            $minute = floor(($nowTimeStamp - $time) / 60);
            if($minute == 0){
                return '刚刚';
            }
            return $minute . '分钟前';
        }
        return $hour . '小时前';
    }
    else if($day < 7){
        $weekday = date('w');
        if($weekday - $day < 0){
            $day = 7 + ($weekday - $day);
            return week($day);
        }
        return week($weekday - $day);
    }
    else if($day > 7 && $day < 30){
        $week = floor($day / 7);
        return $week . '周前';
    }
    else if($day > 30 && $day < 360){
        return date('m-s',$time);
    }
    return date('Y-m-s',$time);
}

function week($day){
    $week= array(
        "0"=>"星期日",
        "1"=>"星期一",
        "2"=>"星期二",
        "3"=>"星期三",
        "4"=>"星期四",
        "5"=>"星期五",
        "6"=>"星期六"
    );
    return $week[$day];
};


/**
 * @param $data
 * @return mixed
 * 获取收获地址标签
 */
function getAddressLabel($data){
    $label = config('jufeel_config.label');
    foreach ($data as $d){
        $d['label'] = $label[$d['label']];
    }
    return $data;
}

/**
 * @param $data
 * @return mixed
 * 获取购物券类别
 */
function getCouponCategory($data,$type){
    $goods_category = config('jufeel_config.goods_category');
    for($i=0;$i<sizeof($data);$i++){
        if($type == 1){
            $data[$i]['category'] = $goods_category[$data[$i]['coupon']['category']];
        }
        else{
            $data[$i]['category'] = $goods_category[$data[$i]['category']];
        }
    }
    return $data;
}