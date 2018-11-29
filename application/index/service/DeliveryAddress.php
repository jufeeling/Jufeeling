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
        //如果是则判断用户是否已有默认地址
        //如果有则修改默认地址
        //入库并返回id

        if ($data['state'] == 0) {
            $address = DeliveryAddressModel::where('user_id', TokenService::getCurrentUid())
                ->where('state', 0)
                ->find();
            if ($address) {
                $address['state'] = 1;
                $address->save();
            }
        }
        $value = [
            'user_id' => TokenService::getCurrentUid(),
            'receipt_name' => $data['name'],
            'receipt_phone' => $data['phone'],
            'receipt_area' => $data['area'],
            'receipt_address' => $data['address'],
            'label' => $data['label'],
            'state' => $data['state']
        ];
        return DeliveryAddressModel::insertGetId($value);
    }

    /**
     * @param $data
     * @return \think\response\Json
     * @throws DeliveryAddressException
     * 删除收货地址
     */
    public function deleteDeliveryAddress($data)
    {
        $result = DeliveryAddressModel::where('id', $data['id'])
            ->where('user_id', TokenService::getCurrentUid())
            ->delete();
        if ($result == false) {
            throw new DeliveryAddressException(['msg' => '删除失败...请重试']);
        }
    }

    /**
     * @param $data
     * @return \think\response\Json
     * @throws DeliveryAddressException
     * 更新收货地址
     */
    public function updateDeliveryAddress($data)
    {
        if ($data['state'] == 0) {
            $address = DeliveryAddressModel::where('user_id', TokenService::getCurrentUid())
                ->where('state', 0)
                ->find();
            if ($address) {
                $address['state'] = 1;
                $address->save();
            }
        }
        DeliveryAddressModel::where('id', $data['id'])
            ->where('user_id', TokenService::getCurrentUid())
            ->setField([
                    'label' => $data['label'],
                    'state' => $data['state'],
                    'receipt_name' => $data['name'],
                    'receipt_area' => $data['area'],
                    'receipt_phone' => $data['phone'],
                    'receipt_address' => $data['address']]
            );
    }

    /**
     * @param $data
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws DeliveryAddressException
     * 收货地址详情
     */
    public function getDeliveryAddress($data)
    {
        if ($data['id'] != 0) {
            $address = DeliveryAddressModel::getDeliveryAddress($data['id']);
        }
        else
        {
            $address = DeliveryAddressModel::getDefaultAddress(TokenService::getCurrentUid());
            if(empty($address))
            {
                $address = DeliveryAddressModel::where('user_id',TokenService::getCurrentUid())
                    ->find();
            }
        }
        return $address;
    }
}