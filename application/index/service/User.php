<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/10
 * Time: 18:52
 */

namespace app\index\service;

use app\index\model\DeliveryAddress as DeliveryAddressModel;
use app\index\service\Token as TokenService;
use app\index\model\Party as PartyModel;
use app\index\model\PartyOrder as PartyOrderModel;

class User
{
    /**
     * @return mixed
     * 获取用户举办的派对
     */
    public function getUserHostParty(){
        //$uid = TokenService::getCurrentUid();
        $data = PartyModel::withCount('participants')
            ->withCount('message')
            ->where('user_id',1)
            ->order('create_time desc')
            ->select();
        $result = getPartyWay($data,1);
        return $result;
    }

    /**
     * @return mixed
     * 获取用户参加的派对
     */
    public function getUserJoinParty(){
        $uid = TokenService::getCurrentUid();
        $data = PartyOrderModel::with(['party'=>function($query){
            $query->withCount('participants')
                ->withCount('message');
        }])
            ->where('user_id',$uid)
            ->order('create_time desc')
            ->select();
        $result = getPartyWay($data,2);
        return $result;
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取用户收货地址
     */
    public function getUserDeliveryAddress(){
        $uid = TokenService::getCurrentUid();
        $data = DeliveryAddressModel::where('user_id',$uid)
            ->select();
        $result = getAddressLabel($data);
        return $result;
    }

}