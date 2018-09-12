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
     * 获取用户举办的派对
     * @return \think\response\Json
     */
    public function getUserHostParty(){
        $data = $this->user->getUserHostParty();
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 获取用户参加的派对
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

    /**
     * @return \think\response\Json
     * 获取用户的购物券
     */
    public function getUserCoupon(){
        $data = $this->user->getUserCoupon();
        return result($data);
    }

}