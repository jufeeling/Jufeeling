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

    public function orders()
    {
        return $this->belongsTo('GoodsOrder', 'order_id', 'id');
    }
    /**
     * @param $user_id
     * @return mixed
     * 获取用户的商品
     */
    public static function getUserGoods($user_id)
    {
        $data =
            [
                'user_id' => $user_id,
                'status' => OrderStatusEnum::PAID,
                'state' => OrderStatusEnum::Undelete
            ];
        $data = self::with(['goods' => function ($query) {
            $query->with('label')->field('id,name,thu_url,sale_price');
        }])
            ->where($data)
            ->order('update_time desc')
            ->field('price,goods_id,count')
            ->select()
            ->toArray();
        return $data;
    }
}