<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/17
 * Time: 14:17
 */

namespace app\index\controller;

use app\index\service\Pay as PayService;
use app\index\validate\OrderValidate;
use think\App;
use think\Controller;
use think\facade\Request;

class Pay extends Controller
{
    private $pay;

    public function __construct(App $app = null, PayService $pay)
    {
        $this->pay = $pay;
        parent::__construct($app);
    }

    public function payOrder()
    {
        (new OrderValidate())->scene('id')->goCheck(Request::param());
        $result = $this->pay->payOrder(Request::param());
        return result($result);
//        $oGoods = [
//            [
//                'goods_id' => 1,
//            ],
//            [
//                'goods_id' => 2
//            ]
//        ];
//        $oGIDs = [];
//        foreach ($oGoods as $item) {
//            array_push($oGIDs, $item['goods_id']);
//        }
//        $goods = \app\index\model\Goods::all($oGIDs)
//            ->visible(['id', 'price', 'stock'])
//            ->toArray();
//        return result($goods);
    }
}