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
use app\lib\exception\UserException;
use think\App;
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
    public function addShoppingCart()
    {
        (new CartValidate())->scene('add')->goCheck(Request::param());
        $this->cart->addShoppingCart(Request::param());
        return result();
    }

    /**
     * @return \think\response\Json
     * 获取购物车商品
     */
    public function getShoppingCartInfo()
    {
        $data = $this->cart->getShoppingCartInfo();
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 修改购物车商品个数
     */
    public function changeCartCount()
    {
        (new CartValidate())->scene('count')->goCheck(Request::param());
        try{
            $this->cart->changeCartCount(Request::param());
        }catch (UserException $e){
            return result('',$e->msg,$e->code);
        }
        return result();
    }

    /**
     * @return \think\response\Json
     * 删除购物车内容
     */
    public function deleteCart()
    {
        (new CartValidate())->scene('id')->goCheck(Request::param());
        $this->cart->deleteCart(Request::param());
        return result();
    }

    /**
     * @return \think\response\Json
     * 修改购物车选择状态
     */
    public function selectCart()
    {
        (new CartValidate())->scene('select')->goCheck(Request::param());
        try{
           $data =  $this->cart->selectCart(Request::param());
        }catch (UserException $e){
            return result('',$e->msg,$e->code);
        }
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 全选
     */
    public function selectAllCart()
    {
        (new CartValidate())->scene('all')->goCheck(Request::param());
        $data = $this->cart->selectAllCart(Request::param());
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 将缓存放入数据库中
     */
    public function saveCartToDb()
    {
        $this->cart->saveCacheToDb();
        $data = Request::header('token');
        return result($data);
    }
}