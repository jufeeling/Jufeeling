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
use think\App;
use think\Controller;

class Order extends BaseController
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
        $data = input('post.goods/a');
        (new OrderValidate())->scene('generate')->goCheck($data);
        $status = $this->order->generateOrder($data['goods'], $data['salePrice'], $data['receipt_id']);
        return result($status);
    }
}