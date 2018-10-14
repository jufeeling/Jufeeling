<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 14:55
 */

namespace app\index\model;


use app\lib\enum\OrderStatusEnum;
use think\Model;

class OrderId extends Model
{
    public function goods()
    {
        return $this->belongsTo('Goods', 'goods_id', 'id');
    }

    /**
     * @param $type
     * 对应Select字段标记是否使用
     * @param $user_id
     * @return mixed
     * 获取用户的商品
     */
    public static function getUserGoods($type, $user_id)
    {
        $data = self::with(['goods' => function ($query) {
            $query->field('id,name,thu_url,sale_price');
        }])
            ->where('select', $type)
            ->where('status',OrderStatusEnum::PAID)
            ->where('user_id', $user_id)
            ->order('update_time desc')
            ->field('id,price,goods_id')
            ->select();
        return $data;
    }

}