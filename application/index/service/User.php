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
    public function getUserLaunchParty(){
        $uid = TokenService::getCurrentUid();
    }
}