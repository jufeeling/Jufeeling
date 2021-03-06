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

    /**
     * @return \think\response\Json
     * 支付订单
     */
    public function payOrder()
    {
        (new OrderValidate())->scene('pay')->goCheck(Request::param());
        $result = $this->pay->payOrder(Request::param());
      //  return result('','',404);
        return result($result);
    }

    /**
     * @return \think\response\Json
     * 支付成功
     */
    public function paySuccess(){
        (new OrderValidate())->scene('pay')->goCheck(Request::param());
        $this->pay->paySuccess(Request::param());
        return result();
    }

    /**
     * @return \think\response\Json
     * 支付失败
     */
    public function payFail(){
        (new OrderValidate())->scene('pay')->goCheck(Request::param());
        $this->pay->payFail(Request::param());
        return result();
    }

    public function redirectNotify()
    {
        $notify = new \WxPayNotify();
        $notify->Handle();
    }

    public function receiveNotify()
    {
        $xmlData = file_get_contents('php://input');
        $result = curl_post('http:/z.cn/api/v1/pay/re_notify?XDEBUG_SESSION_START=13322',
            $xmlData);
    }

    /**
     * @return \think\response\Json
     * 重新支付
     */
    public function rePay(){
        (new OrderValidate())->scene('pay')->goCheck(Request::param());
        $data = $this->pay->rePay(Request::param());
        return result($data);
    }
}