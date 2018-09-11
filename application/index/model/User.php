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
    public static function getAll(){

        $data =
            [
                'id' => 1,
                'openid' => 1
            ];
        $order =
            [
                'list' => 'list',
                'id'   => 'id'
            ];
        $result = self::where($data)->order($order['id'])->select();
        return $result;
    }
}