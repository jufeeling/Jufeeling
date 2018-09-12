<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/10
 * Time: 18:48
 */

namespace app\index\controller;


use think\App;
use app\index\service\User as UserService;
use think\Controller;

class User extends Controller
{
    private $user;
    public function __construct(App $app = null,UserService $user)
    {
        $this->user = $user;
        parent::__construct($app);
    }

    /**
     * @return \think\response\Json
     * 获取用户举办的聚会
     */
    public function getUserHostParty(){
        $data = $this->user->getUserHostParty();
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 获取用户参加的聚会
     */
    public function getUserJoinParty(){
        $data = $this->user->getUserJoinParty();
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 获取用户的收货地址
     */
    public function getUserDeliveryAddress(){
        $data = $this->user->getUserDeliveryAddress();
        return result($data);
    }
}