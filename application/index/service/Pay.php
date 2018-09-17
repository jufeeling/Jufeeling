<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/17
 * Time: 14:40
 */

namespace app\index\service;

use app\index\model\GoodsOrder as GoodsOrderModel;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use app\index\service\Order as OrderService;
use app\index\service\Token as TokenService;
use think\facade\Log;

require '../extend/pay/wxpay/lib/WxPay.Api.php';

class Pay
{
    private $id;       //订单表中的主键id

    private $order_id; //自己定义的order_id

    public function payOrder($data)
    {
        $this->id = $data['id'];
        $this->checkOrderValid();
        $status = (new OrderService())->checkOrderStock($this->order_id);
        if (!$status['pass']) {
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);
    }

    /**
     * @param $totalPrice
     * @return array
     * @throws TokenException
     * 开始微信支付
     */
    private function makeWxPreOrder($totalPrice)
    {
        //openid
        $openid = TokenService::getCurrentTokenVar('openid');
        if (!$openid) {
            throw new TokenException();
        }
        $order = GoodsOrderModel::with(['goods'])
            ->where('id', '=', $this->id)
            ->find();
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->order_id);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice * 100);
        $wxOrderData->SetBody($order['goods']['name']);
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url('http://sqq.coolcoder.io/index/.php/api/v1/pay/notify');
        return $this->getPaySignature($wxOrderData);
    }

    private function getPaySignature($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code'] != 'SUCCESS' ||
            $wxOrder['result_code'] != 'SUCCESS'
        ) {
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
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']);
        return $rawValues;
    }

    private function recordPreOrder($wxOrder)
    {
        $order = GoodsOrderModel::where('id', '=', $this->id)->find();
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
        $order = GoodsOrderModel::find($this->id);
        if (!$order) {
            throw new OrderException([
                'code' => 511,
                'msg' => '订单不存在'
            ]);
        }
        if (!Token::isValidOperate($order['user_id'])) {
            throw new TokenException(
                [
                    'msg' => '订单与用户不匹配',
                    'errorCode' => 10003
                ]);
        }
        if ($order['status'] != OrderStatusEnum::UNPAID) {
            throw new OrderException(
                [
                    'msg' => '订单已支付过啦',
                    'errorCode' => 80003,
                    'code' => 400
                ]);
        }
        $this->order_id = $order['order_id'];
        return true;
    }
}