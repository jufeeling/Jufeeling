<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 11:47
 */

namespace app\index\service;

use app\index\model\DeliveryAddress as DeliveryAddressModel;
use app\index\service\Token as TokenService;
use app\lib\exception\DeliveryAddressException;

class DeliveryAddress
{
    /**
     * @param $data
     * @return \think\response\Json
     * @throws DeliveryAddressException
     * 新增收货地址
     */
    public function addDeliveryAddress($data)
    {
        //判断用户是否设置为用户地址
        if ($data['state'] == 0) {
            //判断用户是否已有默认地址
            $address = DeliveryAddressModel::where('user_id', TokenService::getCurrentUid())
                ->where('state', 0)
                ->find();
            if ($address) {
                $address['state'] = 1;
                $address->save();
            }
        }
        $address = new DeliveryAddressModel();
        $address['user_id'] = TokenService::getCurrentUid();
        $address['receipt_name'] = $data['name'];
        $address['receipt_phone'] = $data['phone'];
        $address['receipt_area'] = $data['area'];
        $address['receipt_address'] = $data['address'];
        $address['label'] = $data['label'];
        $address['state'] = $data['state'];
        $address->save();
        return $address['id'];
    }

    /**
     * @param $data
     * @return \think\response\Json
     * @throws DeliveryAddressException
     * 删除收货地址
     */
    public function deleteDeliveryAddress($data)
    {
        $address = DeliveryAddressModel::getDeliveryAddress($data['id']);
        if ($address) {
            //判断此默认地址是否为该用户的
            if ($address['user_id'] == TokenService::getCurrentUid()) {
                if ($address->delete()) ;
                else {
                    throw new DeliveryAddressException(
                        [
                            'code' => 100,
                            'msg' => '服务器内部错误',
                            'errorCode' => 10000
                        ]
                    );
                }
            }
            throw new DeliveryAddressException(
                [
                    'code' => 103,
                    'msg' => '你无权删除此收货地址',
                    'errorCode' => 10003
                ]
            );
        }
        throw new DeliveryAddressException(
            [
                'code' => 102,
                'msg' => '未找到该收货地址',
                'errorCode' => 10002
            ]
        );
    }

    /**
     * @param $data
     * @return \think\response\Json
     * @throws DeliveryAddressException
     * 更新收货地址
     */
    public function updateDeliveryAddress($data)
    {
        $address = DeliveryAddressModel::getDeliveryAddress($data['id']);
        if ($address) {
            //判断此收货地址是否为该用户的
            if ($address['user_id'] == TokenService::getCurrentUid()) {
                $address['id'] = $data['id'];
                $address['receipt_name'] = $data['name'];
                $address['receipt_phone'] = $data['phone'];
                $address['receipt_area'] = $data['area'];
                $address['receipt_address'] = $data['address'];
                $address['label'] = $data['label'];
                $address['state'] = $data['state'];
                $address->save();
            } else {
                throw new DeliveryAddressException(
                    [
                        'code' => 103,
                        'msg' => '你无权修改此收货地址',
                        'errorCode' => 10003
                    ]
                );
            }
        } else {
            throw new DeliveryAddressException(
                [
                    'code' => 102,
                    'msg' => '未找到该收货地址',
                    'errorCode' => 10002
                ]
            );
        }

    }

    /**
     * @param $data
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws DeliveryAddressException
     * 收货地址详情
     */
    public function getDeliveryAddress($data)
    {
        $address = DeliveryAddressModel::getDeliveryAddress($data['id']);
        if ($address) {
            if ($address['user_id'] == TokenService::getCurrentUid()) {
                $labels = config('jufeel_config.label');
                $address['label'] = $labels[$address['label']];
                return $address;
            }
            throw new DeliveryAddressException(
                [
                    'code' => 104,
                    'msg' => '你无权获得此收货地址',
                    'errorCode' => 10004
                ]
            );
        }
        throw new DeliveryAddressException(
            [
                'code' => 102,
                'msg' => '未找到该收货地址',
                'errorCode' => 10002
            ]
        );
    }
}