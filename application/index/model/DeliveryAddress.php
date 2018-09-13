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
}