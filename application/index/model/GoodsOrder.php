<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 15:41
 */

namespace app\index\model;


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

    public static function getUserGoods($status, $uid)
    {
        if ($status == 1) {
            $data = self::field('id,order_id,price,status')
                ->with(['goods' => function ($query) {
                    $query->field('id,order_id,goods_id')
                        ->with(['goods' => function ($query) {
                            $query->field('id,thu_url');
                        }]);
                }])
                ->where('status', 1)
                ->order('create_time desc')
                ->withCount('goods')
                ->where('user_id', $uid)
                ->select();
        } else if ($status == 0) {
            $data = self::field('id,order_id,price,status')
                ->with(['goods' => function ($query) {
                    $query->field('id,order_id,goods_id')
                        ->with(['goods' => function ($query) {
                            $query->field('id,thu_url');
                        }]);
                }])
                ->where('create_time', '>', time() - 86400)
                ->where('status', 0)
                ->order('create_time desc')
                ->withCount('goods')
                ->where('user_id', $uid)
                ->select();

        } else {
            $data = self::field('id,order_id,price,status')
                ->with(['goods' => function ($query) {
                    $query->field('id,order_id,goods_id')
                        ->with(['goods' => function ($query) {
                            $query->field('id,thu_url');
                        }]);
                }])
                ->where('create_time', '<', time() - 86400)
                ->where('status', 0)
                ->withCount('goods')
                ->order('create_time desc')
                ->where('user_id', $uid)
                ->select();
        }

        return $data;
    }

}