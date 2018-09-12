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
        $result = $this->getPartyWay($data,1);
        return $result;
    }

    public function getUserJoinParty(){
        $uid = TokenService::getCurrentUid();
        $data = PartyOrderModel::with(['party'=>function($query){
            $query->withCount('participants')
                ->withCount('message');
        }])
            ->where('user_id',1)
            ->order('create_time desc')
            ->select();
        $result = $this->getPartyWay($data,2);
        return $result;
    }
    /**
     * @param $data
     * @return mixed
     * 得到派对方式
     */
    private function getPartyWay($data,$type){
        $ways = config('way.way');
        if($type==1){
            foreach ($data as $d){
                $d['way'] = $ways[$d['way']];
            }
            return $data;
        }
        foreach ($data as $d){
            $d['party']['way'] = $ways[$d['party']['way']];
        }
        return $data;
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取用户的收货地址
     */
    public function getUserDeliveryAddress(){
        //$uid = Token::getCurrentUid();
        $result = DeliveryAddressModel::where('user_id',1)
            ->select();
        return $result;
    }

    public function test(){
        
    }
}