<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 11:30
 */

namespace app\index\model;


use think\Model;

class DeliveryAddress extends Model
{
    /**
     * @param $id
     * @return array|null|\PDOStatement|string|Model
     * 通过id获取收货地址
     */
    public static function getDeliveryAddress($id)
    {
        $address = self::find($id);
        return $address;
    }

    /**
     * @param $uid
     * @return array|null|\PDOStatement|string|Model
     * 获取用户默认地址
     */
    public static function getDefaultAddress($uid){
        $address = self::where('user_id', $uid)
            ->where('state', 0)
            ->find();
        return $address;
    }
}