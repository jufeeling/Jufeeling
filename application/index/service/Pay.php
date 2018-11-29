<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/17
 * Time: 14:40
 */

namespace app\index\service;

use app\index\model\GoodsOrder as GoodsOrderModel;
use app\index\model\GoodsOrder;
use app\index\model\OrderId as OrderIdModel;
use app\index\model\Goods as GoodsModel;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use app\index\service\Order as OrderService;
use app\index\service\Token as TokenService;
use think\facade\Log;

require '../extend/pay/wxpay/lib/WxPay.Api.php';

class Pay
{
    private $id;
    //订单表中的主键id
    private $order_id;
    //自己定义的order_id

    public function payOrder($data)
    {
        $this->id = $data['id'];
        $this->checkOrderValid();
        $status = (new OrderService())->checkOrderStock($this->id);
        if (!$status['pass']) {
            return $status;
        }
        $order = GoodsOrderModel::find($this->id);
        return $this->makeWxPreOrder($order['sale_price'] + $order['carriage']);
    }

    /**
     * @param $totalPrice
     * @return array
     * @throws TokenException
     * 开始微信支付
     */
    private function makeWxPreOrder($totalPrice)
    {
        $openid = TokenService::getCurrentTokenVar('openid');
        if (!$openid) {
            throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->order_id);
        $wxOrderData->SetTrade_type('JSAPI');
        //$wxOrderData->SetTotal_fee($totalPrice * 100);
        $wxOrderData->SetTotal_fee(1);
        $wxOrderData->SetBody('Jufeel');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('jufeel_config.redirect_notify'));
        return $this->getPaySignature($wxOrderData);
    }

    private function getPaySignature($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS') {
            Log::record($wxOrder, 'error');
            Log::record('获取预支付订单失败', 'error');
        }
        //prepay_id
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('MD5');
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        $rawValues['appId'] = config('wx.app_id');
        $rawValues['order_id'] = $this->id;
        unset($rawValues['appId']);
        return $rawValues;
    }

    private function recordPreOrder($wxOrder)
    {
        //将prepay_id存进数据库
        $order = GoodsOrderModel::getOrderById($this->id);
        $order['prepay_id'] = $wxOrder['prepay_id'];
        $order->save();
    }

    /**
     * @return bool
     * @throws OrderException
     * @throws TokenException
     * 检查订单是否合法
     */
    public function checkOrderValid()
    {
        //判断是否存在该订单
        //判断该订单是否属于该用户
        //判断该订单状态是否属于未支付
        //判断该订单是否已被建立超过24小时
        $order = GoodsOrderModel::getOrderById($this->id);
        if ($order &&
            Token::isValidOperate($order['user_id']) &&
            $order['status'] == OrderStatusEnum::UNPAID &&
            time() - $order['create_time'] < 86400
        )
        {
            $this->order_id = $order['order_id'];
            return true;
        }
        throw new OrderException(['msg' => '支付错误,请重试']);
    }

    /**
     * @param $data
     * @throws OrderException
     * 支付成功后的处理
     */
    public function paySuccess($data)
    {
        //找到该订单
        //将该订单下属的orderId找到并修改状态
        //将该订单的状态改为已支付
        $order = GoodsOrderModel::getOrderById($data['id']);
        $orderIds = OrderIdModel::where('order_id', $data['id'])
            ->setField(['status' => OrderStatusEnum::PAID]);
        if ($orderIds == 0) {
            throw new OrderException(['msg' => '修改失败']);
        }
        $order['status'] = OrderStatusEnum::PAID;
        $order->save();
    }

    /**
     * @param $data
     * 支付失败
     */
    public function payFail($data)
    {
        //将该订单下属的orderId找到
        //遍历找到orderId下的真实商品
        //返回库存
        $orderRecord = OrderIdModel::where('order_id', $data['id'])->select();
        foreach ($orderRecord as $o) {
            $goods = GoodsModel::where('id', $o['goods_id'])->find();
            $goods['stock'] = $goods['stock'] + $o['count'];
            $goods->save();
        }
    }

    /**
     * @param $id
     * @return array
     * 重新支付
     */
    public function rePay($id){
        $order = GoodsOrder::with(['goods'=>function($query){
            $query->field('goods_id,count,order_id');
        }])
            ->find($id);
        $goods = [];
        foreach ($order['goods'] as $key => $item){
            array_push($goods,$item);
        }
        $data['id'] = (new OrderService())->generateOrder(
            $goods,
            $order['coupon_id'],
            $order['receipt_id'],
            $order['carriage']
        );
        $result = (new Pay())->payOrder($data);
        $order['isDeleteAdmin'] = 1;
        $order->save();
        return $result;
    }
}