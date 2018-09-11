<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/10
 * Time: 18:52
 */

namespace app\index\service;
use app\index\service\Token as TokenService;
use app\index\model\User as UserModel;
use app\index\model\Party as PartyModel;

class User
{
    public function getUserHostParty(){
        //$uid = TokenService::getCurrentUid();
        $data = PartyModel::withCount('participants')
            ->withCount('message')
            ->where('user_id',1)
            ->order('create_time desc')
            ->select();
        $result = $this->getPartyWay($data);
        return $result;
    }
    private function getPartyWay($data){
        $ways = config('way.way');
        for($i = 0;$i<sizeof($data);$i++){
            $data[$i]['way'] = $ways[$data[$i]['way']];
        }
        return $data;
    }
}