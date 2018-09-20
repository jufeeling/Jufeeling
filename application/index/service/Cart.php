<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/18
 * Time: 15:37
 */

namespace app\index\service;

use app\index\model\ShoppingCart;
use app\index\service\Token as TokenService;

class Cart
{
    /**
     * @param $data
     * 新增购物车商品
     */
    public function addShoppingCart($data)
    {
        $cart = ShoppingCart::where('goods_id', $data['goods_id'])
            ->where('user_id', TokenService::getCurrentUid())
            ->find();
        if ($cart) {
            $cart['count'] += $data['count'];
            $cart->save();
        } else {
            ShoppingCart::create(
                [
                    'user_id' => TokenService::getCurrentUid(),
                    'goods_id' => $data['goods_id'],
                    'count' => $data['count']
                ]
            );
        }
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取购物车商品信息
     */
    public function getShoppingCartInfo()
    {
        $data = ShoppingCart::with(['goods'=>function($query){
            $query->field('id,name,thu_url,stock');
        }])
            ->field('id,goods_id,count')
            ->order('update_time desc')
            ->where('user_id',TokenService::getCurrentUid())
            ->select();
        return $data;
    }
}