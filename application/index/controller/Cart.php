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
use think\facade\Request;

class Cart extends BaseController
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
        $data = $this->cart->getShoppingCartInfo();
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 修改购物车商品个数
     */
    public function changeCartCount(){
        (new CartValidate())->scene('count')->goCheck(Request::param());
        $this->cart->changeCartCount(Request::param());
        return result();
    }

    /**
     * @return \think\response\Json
     * 删除购物车内容
     */
    public function deleteCart(){
        (new CartValidate())->scene('id')->goCheck(Request::param());
        $this->cart->deleteCart(Request::param());
        return result();
    }

    /**
     * @return \think\response\Json
     * 修改购物车选择状态
     */
    public function selectCart(){
        (new CartValidate())->scene('select')->goCheck(Request::param());
        $this->cart->selectCart(Request::param());
        return result();
    }

    /**
     * @return \think\response\Json
     * 全选
     */
    public function selectAllCart(){
        (new CartValidate())->scene('all')->goCheck(Request::param());
        $this->cart->selectAllCart(Request::param());
        return result();
    }
}