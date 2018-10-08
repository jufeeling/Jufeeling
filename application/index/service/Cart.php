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
use app\lib\exception\UserException;

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
        $data['goods'] = ShoppingCart::with(['goods'=>function($query){
            $query->field('id,name,thu_url,stock,price');
        }])
            ->field('id,goods_id,count,select')
            ->order('update_time desc')
            ->where('user_id',TokenService::getCurrentUid())
            ->select();
        //计算购物车总价
        $data = $this->getShoppingCartTotalPrice($data['goods']);
        //转换购物车商品勾选状态 0=>false 1=>true
        $data['goods'] = $this->changeSelect($data['goods']);
        return $data;
    }

    private function changeSelect($data){
        foreach ($data as $d){
            if($d['select'] == 0){
                $d['select'] = false;
            }
            else{
                $d['select'] = true;
            }
        }
        return $data;
    }

    /**
     * @param $goods
     * @return int
     * 计算购物车总价格
     */
    private function getShoppingCartTotalPrice($goods){
        $data['totalPrice'] = 0;
        $data['count'] = 0;
        foreach ($goods as $g){
            if($g['select'] === 1){
                $data['totalPrice'] += $g['goods']['price'] * $g['count'];
                $data['count'] += $g['count'];
            }
        }
        $data['goods'] = $goods;
        return $data;
    }

    /**
     * @param $data
     * @throws UserException
     * 修改购物车数量
     */
    public function changeCartCount($data){
        $cart = ShoppingCart::find($data['id']);
        if($cart['user_id'] !== TokenService::getCurrentUid()){
            throw new UserException([
                'msg' => '你无权修改购物车数量'
            ]);
        }
        $cart['count'] = $data['count'];
        $cart->save();
    }

    /**
     * @param $data
     * @throws UserException
     * 删除购物车
     */
    public function deleteCart($data){
        $cart = ShoppingCart::find($data['id']);
        if($cart['user_id'] !== TokenService::getCurrentUid()){
            throw new UserException([
                'msg' => '你无权删除购物车'
            ]);
        }
        $cart->delete();
    }

    /**
     * @param $data
     * @throws UserException
     * 选择购物车数量
     */
    public function selectCart($data){
        $cart = ShoppingCart::find($data['id']);
        if($cart['user_id'] !== TokenService::getCurrentUid()){
            throw new UserException([
                'msg' => '你无权修改购物车状态'
            ]);
        }
        $cart['select'] = $data['select'];
        $cart->save();
    }

    /**
     * @param $data
     * 全选修改
     */
    public function selectAllCart($data){
        $uid = TokenService::getCurrentUid();
        ShoppingCart::where('user_id',$uid)->setField('select',$data['select']);
    }
}