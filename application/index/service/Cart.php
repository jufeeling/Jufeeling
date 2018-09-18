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
        foreach ($data as $d) {
            $cart = ShoppingCart::where('goods_id', $d['goods_id'])
                ->where('user_id', 1)
                ->find();
            if ($cart) {
                $cart['count'] += $d['count'];
            } else {
                ShoppingCart::create(
                    [
                        'user_id' => 1,
                        'goods_id' => $d['goods_id'],
                        'count' => $d['count']
                    ]
                );
            }
        }
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取购物车商品信息
     */
    public function getShoppingCartInfo()
    {
        $data = ShoppingCart::with(['goods'=>function($query){
            $query->field('id,name,pic_url,stock');
        }])
            ->field('id,goods_id,count')
            ->order('update_time desc')
            ->where('user_id',1)
            ->select();
        return $data;
    }
}