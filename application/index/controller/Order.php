<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 14:34
 */

namespace app\index\controller;

use app\index\service\Order as OrderService;
use app\index\validate\OrderValidate;
use app\lib\exception\GoodsException;
use app\lib\exception\OrderException;
use think\App;
use think\Controller;
use think\facade\Request;

class Order extends Controller
{
    private $order;

    public function __construct(App $app = null, OrderService $order)
    {
        $this->order = $order;
        parent::__construct($app);
    }

    /**
     * 生成订单
     */
    public function generateOrder()
    {
        (new OrderValidate())->scene('generate')->goCheck(Request::param());
        try{
            $status = $this->order->generateOrder(
                Request::param('goods'),
                Request::param('coupon_id'),
                Request::param('receipt_id'),
                Request::param('carriage')
            );
        }catch (GoodsException $exception)
        {
            return result('',$exception->msg,$exception->code);
        }
        return result($status);
    }

    /**
     * 得到预订单
     */
    public function generatePreOrder(){
        (new OrderValidate())->scene('pre')->goCheck(Request::param());
        $status = $this->order->generatePreOrder(Request::param());
        return result($status);
    }

}