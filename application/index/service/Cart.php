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
use think\facade\Cache;

class Cart
{
    private $openid;
    private $cache_name;

    /**
     * Cart constructor.
     * 判断是否有Cart的缓存
     */
    public function __construct()
    {
        $this->openid = TokenService::getCurrentTokenVar('openid');
        $this->cache_name = 'cart' . $this->openid;
        $val = Cache::get($this->cache_name);
        if (empty($val)) {
            $data = $this->getShoppingCart();
            $this->saveCartToCache($data);
        }
    }

    /**
     * @param $data
     * 存进缓存
     */
    private function saveCartToCache($data)
    {
        Cache::set($this->cache_name, $data);
    }

    /**
     * @return mixed
     * 得到缓存内容
     */
    private function getCartCacheInfo()
    {
        $cartInfo = Cache::get($this->cache_name);
        return $cartInfo;
    }

    /**
     * @return int
     * 从数据库中获取购物车信息
     */
    public function getShoppingCart()
    {
        $data['goods'] = ShoppingCart::with(['goods' => function ($query) {
            $query->field('id,name,thu_url,stock,sale_price');
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
        //先将缓存中的内容入库
        //遍历要加入购物车的商品
        //判断购物车中是否存在该商品
        //如果存在只需要改变数量
        //如果不存在则新增记录
        $this->saveCacheToDb();
        foreach ($data['goods'] as $g) {
            $cart = ShoppingCart::where('user_id', TokenService::getCurrentUid())
                ->where('goods_id', $g['goods_id'])
                ->find();
            if ($cart) {
                $cart['count'] += $g['count'];
                $cart['select'] = 1;
                $cart->save();
            } else {
                ShoppingCart::create([
                    'user_id' => TokenService::getCurrentUid(),
                    'goods_id' => $g['goods_id'],
                    'count' => $g['count']
                ]);
            }
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
        //遍历传进来的数据
        //如果该条记录为勾选状态
        //将该条记录的价格(单价*数量)加入总价
        //将数量加入总数量
        //返回数据

        $data['totalPrice'] = 0;
        $data['count'] = 0;
        foreach ($goods as $g) {
            if ($g['select'] == true) {
                $nowPrice = bcmul ($g['goods']['sale_price'],$g['count'] ,1);
                $data['totalPrice'] = bcadd($data['totalPrice'],$nowPrice,1);
                $data['count'] += $g['count'];
            }
        }
        $data['goods'] = $goods;
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * @throws UserException
     */
    public function changeCartCount($data)
    {
        //标记状态
        //取出购物车信息
        //遍历信息,如果存在该条记录,则改变状态
        //判断状态,如果存在该条记录,则重新计算购物车总价以及数量并将新的记录存于缓存
        //如果不存在,抛异常(执行此操作必定是在购物车界面,所以如果不存在该条记录必定出现异常)

        $status = ['isExist' => false];
        $cartInfo = $this->getCartCacheInfo();
        foreach ($cartInfo['goods'] as $c) {
            if ($c['id'] == $data['id']) {
                $c['count'] = (int)$data['count'];
                $status['isExist'] = true;
            }
        }
        if ($status['isExist'] == true) {
            $data = $this->getShoppingCartTotalPrice($cartInfo['goods']);
            $this->saveCartToCache($data);
            return $data;
        } else {
            throw new UserException(['msg' => '购物车中没有该商品']);
        }
    }

    /**
     * @param $data
     * @throws UserException
     * 删除购物车
     */
    public function deleteCart($data)
    {
        //先将购物车信息入库
        //在数据库中删除该条记录

        $this->saveCacheToDb();
        $cart = ShoppingCart::find($data['id']);
        if ($cart['user_id'] !== TokenService::getCurrentUid()) {
            throw new UserException(['msg' => '你无权删除购物车']);
        }
        $cart->delete();
    }

    /**
     * @param $data
     * @return int|mixed
     * @throws UserException
     * 选择购物车
     */
    public function selectCart($data)
    {
        //标记状态并取出购物车信息
        //遍历 如果存在该条记录,判断此时勾选状态
        //1=>true,2=>false
        //再将新的信息存于缓存

        $status = ['isExist' => false];
        $cartInfo = $this->getCartCacheInfo();
        foreach ($cartInfo['goods'] as $c) {
            if ($c['id'] == $data['id']) {
                if ($data['select'] == 1) {
                    $c['select'] = true;
                } else {
                    $c['select'] = false;
                }
            }
            $status['isExist'] = true;
        }
        if ($status['isExist'] == true) {
            $cartInfo = $this->getShoppingCartTotalPrice($cartInfo['goods']);
            $this->saveCartToCache($cartInfo);
            return $cartInfo;
        } else {
            throw new UserException(['msg' => '购物车中没有该商品']);
        }
    }

    /**
     * @param $data
     * @return array|\PDOStatement|string|\think\Collection
     * 全选
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
        $data = $this->getShoppingCartTotalPrice($cartInfo['goods']);
        $this->saveCartToCache($data);
        return $data;
    }

    /**
     *将缓存中的数据添加到数据库
     */
    public function saveCacheToDb()
    {
        //得到缓存中的数据
        //如果存在缓存
        //遍历缓存中的数据
        //在数据库中找到该条数据
        //将勾选状态以及数量更新
        //保存并删除缓存中的购物车

        $data = $this->getCartCacheInfo();
        if ($data) {
            foreach ($data['goods'] as $d) {
                $cart = ShoppingCart::where('id', $d['id'])->find();
                $cart['count'] = (int)$d['count'];
                if ($d['select'] == true) {
                    $cart['select'] = 1;
                } else {
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
    private function deleteCache()
    {
        Cache::rm($this->cache_name);
    }
}