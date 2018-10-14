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
use think\Exception;
use think\facade\Cache;

class Cart
{

    /**
     * Cart constructor.
     * 判断是否有Cart的缓存    */
    public function __construct()
    {
        $uid = TokenService::getCurrentUid();
        $token = Cache::get($uid);
        if($token);
        else{
            $data = $this->getShoppingCart();
            $this->saveCartToCache($data);
        }
    }

    /**
     * @param $data
     * 存进缓存
     */
    private function saveCartToCache($data){
        $uid = TokenService::getCurrentUid();
        $token = Cache::get($uid);
        if($token){
            Cache::set($token,$data,3600);
        }else{
            $token = TokenService::generateToken();
            //先将token缓存(唯一区识)
            Cache::set(TokenService::getCurrentUid(),$token,3600);
            Cache::set($token,$data,3600);
        }

    }

    /**
     * @return mixed
     * 得到缓存内容
     */
    private function getCartCacheInfo(){
        $uid = TokenService::getCurrentUid();
        $token = Cache::get($uid);
        $cartInfo = Cache::get($token);
        return $cartInfo;
    }

    /**
     * @return int
     * 从数据库中获取购物车信息
     */
    public function getShoppingCart(){
        $data['goods'] = ShoppingCart::with(['goods' => function ($query) {
            $query->field('id,name,thu_url,stock,price');
        }])
            ->field('id,goods_id,count,select')
            ->order('update_time desc')
            ->where('user_id', TokenService::getCurrentUid())
            ->select();
        //转换购物车商品勾选状态 0=>false 1=>true
        $data['goods'] = $this->changeSelect($data['goods']);
        //计算购物车总价
        $data = $this->getShoppingCartTotalPrice($data['goods']);
        return $data;
    }

    /**
     * @param $data
     * 新增购物车商品
     */
    public function addShoppingCart($data)
    {
        $status = [
            'isExist' => false
        ];
        $cartInfo = $this->getCartCacheInfo();
        foreach ($cartInfo['goods'] as $c){
            if($c['goods_id'] == $data['goods_id']){
                $c['count'] += $data['count'];
                $status['isExist'] = true;
            }
        }
        if($status['isExist'] == false){
            ShoppingCart::create(
                [
                    'user_id' => TokenService::getCurrentUid(),
                    'goods_id' => $data['goods_id'],
                    'count' => $data['count']
                ]
            );
            $this->saveCacheToDb();
        }
        else{
            $cartInfo = $this->getShoppingCartTotalPrice($cartInfo['goods']);
            $this->saveCartToCache($cartInfo);
        }
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取购物车商品信息
     */
    public function getShoppingCartInfo()
    {
       // 从缓存中取出购物车信息
        $data = $this->getCartCacheInfo();
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * 改变select字段的值
     */
    private function changeSelect($data)
    {
        foreach ($data as $d) {
            if ($d['select'] == 0) {
                $d['select'] = false;
            } else {
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
    private function getShoppingCartTotalPrice($goods)
    {
        $data['totalPrice'] = 0;
        $data['count'] = 0;
        foreach ($goods as $g) {
            if ($g['select'] === true) {
                $data['totalPrice'] += $g['goods']['price'] * $g['count'];
                $data['count'] += $g['count'];
            }
        }
        $data['goods'] = $goods;
        return $data;
    }

    /**
     * @param $data
     * @throws Exception
     * 修改购物车数量
     */
    public function changeCartCount($data)
    {
        $status = [
            'isExist' => false
        ];
        $cartInfo = $this->getCartCacheInfo();
        foreach ($cartInfo['goods'] as $c){
            if($c['id'] == $data['id']){
                $c['count'] = $data['count'];
                $status['isExist'] = true;
            }
        }
        if($status['isExist'] == true){
            $cartInfo = $this->getShoppingCartTotalPrice($cartInfo['goods']);
            $this->saveCartToCache($cartInfo);
        }
        else{
            throw new UserException([
               'msg' => '购物车中没有该商品'
            ]);
        }

    }

    /**
     * @param $data
     * @throws UserException
     * 删除购物车
     */
    public function deleteCart($data)
    {
        $this->saveCacheToDb();
        $cart = ShoppingCart::find($data['id']);
        if ($cart['user_id'] !== TokenService::getCurrentUid()) {
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
    public function selectCart($data)
    {
        $status = [
            'isExist' => false
        ];
        $cartInfo = $this->getCartCacheInfo();
        foreach ($cartInfo['goods'] as $c){
            if($c['id'] == $data['id']){
                if($data['select'] == 1){
                    $c['select'] = true;
                }
                else{
                    $c['select'] = false;
                }
            }
            $status['isExist'] = true;
        }
        if($status['isExist'] == true){
            $this->saveCartToCache($cartInfo);
        }
        else{
            throw new UserException([
                'msg' => '购物车中没有该商品'
            ]);
        }
    }

    /**
     * @param $data
     * 全选修改
     */
    public function selectAllCart($data)
    {
        $cartInfo = $this->getCartCacheInfo();
        foreach ($cartInfo['goods'] as $c) {
            if ($data['select'] == 1) {
                $c['select'] = true;
            } else {
                $c['select'] = false;
            }
        }
        $this->saveCartToCache($cartInfo);
    }

    /**
     *将缓存中的数据添加到数据库
     */
    public function saveCacheToDb(){
        $data = $this->getCartCacheInfo();
        if($data){
            foreach ($data['goods'] as $d){
                $cart = ShoppingCart::where('id',$d['id'])->find();
                $cart['count'] = $d['count'];
                if($d['select'] == true){
                    $cart['select'] = 1;
                }else{
                    $cart['select'] = 0;
                }
                $cart->save();
            }
            $this->deleteCache();
        }
    }

    /**
     * 删除缓存
     */
    private function deleteCache(){
        $token = Cache::get(TokenService::getCurrentUid());
        Cache::rm($token);
        Cache::rm(TokenService::getCurrentUid());
    }
}