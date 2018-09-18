<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/18
 * Time: 15:34
 */

namespace app\index\controller;

use app\index\service\Cart as CartService;
use app\index\validate\CartValidate;
use think\App;
use think\Controller;
use think\facade\Request;

class Cart extends Controller
{
    private $cart;
    public function __construct(App $app = null,CartService $cart)
    {
        $this->cart = $cart;
        parent::__construct($app);
    }

    /**
     * @return \think\response\Json
     * 新增购物车商品
     */
    public function addShoppingCart(){
        (new CartValidate())->scene('add')->goCheck(Request::param());
        $this->cart->addShoppingCart(Request::param());
        return result();
    }

    /**
     * @return \think\response\Json
     * 获取购物车商品
     */
    public function getShoppingCartInfo(){
        $data = $this->cart->getShoppingCartInfo(Request::param());
        return result($data);
    }
}