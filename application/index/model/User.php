<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/7/29
 * Time: 22:21
 */

namespace app\index\model;


use think\Model;

class User extends Model
{
    public static function getByOpenID($openid){
        $user = self::where('openid',$openid)->find();
        return $user;
    }
}