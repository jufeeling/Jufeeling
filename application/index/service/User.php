<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/10
 * Time: 18:52
 */

namespace app\index\service;

use app\index\model\DeliveryAddress as DeliveryAddressModel;
use app\index\model\UserCoupon as UserCouponModel;
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
        $uid = TokenService::getCurrentUid();
        $data = PartyModel::withCount('participants')
            ->withCount('message')
            ->where('user_id',$uid)
            ->where('status',0)
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
            ->where('status',0)
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

    /**
     * @return mixed
     * 获取用户的购物券
     */
    public function getUserCoupon(){
        $uid = TokenService::getCurrentUid();
        //获取未使用且未过期的购物券
        $data['not_use'] = UserCouponModel::with('coupon')
            ->where('end_time','>',time())
            ->where('user_id',$uid)
            ->where('state',0)
            ->select();
        //获取使用过的购物券
        $data['used']    = UserCouponModel::with('coupon')
            ->where('user_id',$uid)
            ->where('state',1)
            ->select();
        //获取过期且未使用过的购物券
        $data['overdue'] = UserCouponModel::with('coupon')
            ->where('end_time','<',time())
            ->where('user_id',$uid)
            ->where('state',0)
            ->select();
        $result['not_use'] = getCouponCategory($data['not_use'],1);
        $result['used']    = getCouponCategory($data['used'],1);
        $result['overdue'] = getCouponCategory($data['overdue'],1);
        return $result;
    }

}