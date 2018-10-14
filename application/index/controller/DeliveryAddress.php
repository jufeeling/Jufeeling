<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 11:34
 */

namespace app\index\controller;

use app\lib\exception\DeliveryAddressException;
use think\App;
use think\facade\Request;
use app\index\validate\DeliveryAddressValidate;
use app\index\service\DeliveryAddress as DeliveryAddressService;

class DeliveryAddress extends BaseController
{
    private $deliveryAddress;

    public function __construct(App $app = null, DeliveryAddressService $deliveryAddress)
    {
        $this->deliveryAddress = $deliveryAddress;
        parent::__construct($app);
    }

    /**
     * @return \think\response\Json
     * 用户添加收货地址
     */
    public function addDeliveryAddress()
    {
        (new DeliveryAddressValidate())->scene('add')->goCheck(Request::param());
        try {
            $data = $this->deliveryAddress->addDeliveryAddress(Request::param());
        } catch (DeliveryAddressException $e) {
            return result('', $e->msg, $e->code);
        }
        return result($data, '添加成功');
    }

    /**
     * @return \think\response\Json
     * 删除收货地址
     */
    public function deleteDeliveryAddress()
    {
        (new DeliveryAddressValidate())->scene('id')->goCheck(Request::param());
        try {
            $this->deliveryAddress->deleteDeliveryAddress(Request::param());
        } catch (DeliveryAddressException $e) {
            return result('', $e->msg, $e->code);
        }
        return result('', '删除成功');
    }

    /**
     * @return \think\response\Json
     * 更新收获地址
     */
    public function updateDeliveryAddress()
    {
        (new DeliveryAddressValidate())->scene('update')->goCheck(Request::param());
        try {
            $this->deliveryAddress->updateDeliveryAddress(Request::param());
        } catch (DeliveryAddressException $e) {
            return result('', $e->msg, $e->code);
        }
        return result('', '修改成功');
    }

    /**
     * @return \think\response\Json
     * 获取收货地址
     */
    public function getDeliveryAddress()
    {
        (new DeliveryAddressValidate())->scene('id')->goCheck(Request::param());
        try {
            $data = $this->deliveryAddress->getDeliveryAddress(Request::param());
        } catch (DeliveryAddressException $e) {
            return result('', $e->msg, $e->code);
        }
        return result($data, '获取成功');
    }
}