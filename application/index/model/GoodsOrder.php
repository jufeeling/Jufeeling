<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 15:41
 */

namespace app\index\model;

use app\lib\enum\OrderStatusEnum;
use think\Model;

class GoodsOrder extends Model
{
    public static function getOrderById($id)
    {
        $order = self::find($id);
        return $order;
    }

    public function goods()
    {
        return $this->hasMany('OrderId', 'order_id', 'id');
    }

    /**
     * @param $status
     * @param $uid
     * @return array|\PDOStatement|string|\think\Collection
     * 获取用户的物品
     */
    public static function getUserGoods($status, $uid)
    {
        switch ($status) {
            //未支付
            case OrderStatusEnum::UNPAID:
                $data = [
                    ['status' , '=' , OrderStatusEnum::UNPAID],
                    ['create_time' , '>'  , time() - 86400]
                ];
                break;
            //已支付
            case OrderStatusEnum::PAID:
                $data = [
                    'status' => OrderStatusEnum::PAID,
                ];
                break;
            //已过期
            case OrderStatusEnum::Overdue:
                $data = [
                    ['status' , '=' , OrderStatusEnum::UNPAID],
                    ['create_time' , '<'  , time() - 86400]
                ];
                break;
            //已取消
            case OrderStatusEnum::Cancel:
                $data = [
                    'status' => 2 //已取消的状态
                ];
                break;
        }

        //获取不同状态下的订单(已支付,未支付(已过期和未过期),已取消)
        //已支付:Status = 1
        //未支付:1、已过期(time() - create_time > 86400)并且Status = 0
        //未支付:2、未过期(time() - create_time < 86400)并且Status = 0
        //已取消:用户主动点击取消订单按钮 Status = 2
        //其中已取消以及已过期合并为已取消

        //关联OrderId获取单个商品订单的信息
        //OrderId关联Goods获取商品信息
        //根据create_time排序
        //获取商品的个数(共有多少款)
        //state=0标记用户未删除该订单

        $result = self::field('id,order_id,price,status')
            ->with(['goods' => function ($query) {
                $query->field('id,order_id,goods_id')
                    ->with(['goods' => function ($query) {
                        $query->field('id,thu_url,name');
                    }]);
            }])
            ->order('create_time desc')
            ->withCount('goods')
            ->where('state',0)
            ->where('isDeleteAdmin',0)
            ->where('user_id',$uid)
            ->where($data)
            ->select()
            ->toArray();
        return $result;
    }

}