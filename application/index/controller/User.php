<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/10
 * Time: 18:48
 */

namespace app\index\controller;

use app\index\validate\UserValidate;
use app\lib\exception\UserException;
use think\App;
use app\index\service\User as UserService;
use think\Controller;
use think\facade\Request;

class User extends Controller
{
    private $user;

    public function __construct(App $app = null, UserService $user)
    {
        $this->user = $user;
        parent::__construct($app);
    }

    /**
     * 获取用户举办的派对
     * @return \think\response\Json
     */
    public function getUserHostParty()
    {
        $data = $this->user->getUserHostParty();
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 用户删除派对
     */
    public function deleteUserParty(){
        (new UserValidate())->scene('id')->goCheck(Request::param());
        try{
            $this->user->deleteUserParty(Request::param());
        }catch (UserException $e){
            return result('',$e->msg,$e->code);
        }
        return result();
    }

    /**
     * @return \think\response\Json
     * 删除用户派对订单
     */
    public function deleteUserPartyOrder(){
        (new UserValidate())->scene('id')->goCheck(Request::param());
        try{
            $this->user->deleteUserPartyOrder(Request::param());
        }catch (UserException $e){
            return result('',$e->msg,$e->code);
        }
        return result();
    }

    /**
     * @return \think\response\Json
     * 获取用户参加的派对
     */
    public function getUserJoinParty()
    {
        $data = $this->user->getUserJoinParty();
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 获取用户的收货地址
     */
    public function getUserDeliveryAddress()
    {
        $data = $this->user->getUserDeliveryAddress();
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 获取用户的购物券
     */
    public function getUserCoupon()
    {
        $data = $this->user->getUserCoupon();
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 获取用户的商品
     */
    public function getUserGoods()
    {
        $data = $this->user->getUserGoods();
        return result($data);
    }


    /**
     * @return \think\response\Json
     * 删除来点feel界面的商品
     */
    public function deleteUserGoods(){
        (new UserValidate())->scene('delete')->goCheck(Request::param());
        try{
            $this->user->deleteUserGoods(Request::param());
        }catch (UserException $e){
            return result('',$e->msg,$e->code);
        }
        return result();
    }



    /**
     * @return \think\response\Json
     * 获取订单详情
     */
    public function getUserOrderInfo()
    {
        (new UserValidate())->scene('id')->goCheck(Request::param());
        $data = $this->user->getUserOrderInfo(Request::param());
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 获取用户的订单
     */
    public function getUserOrder(){
        $data = $this->user->getUserOrder();
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 用户删除订单
     */
    public function deleteUserOrder(){
        (new UserValidate())->scene('id')->goCheck(Request::param());
        try{
            $this->user->deleteUserOrder(Request::param());
        }catch (UserException $e){
            return result('',$e->msg,$e->code);
        }
        return result();
    }

    /**
     * @return \think\response\Json
     * 用户取消订单
     */
    public function cancelUserOrder(){
        (new UserValidate())->scene('id')->goCheck(Request::param());
        try{
            $this->user->cancelUserOrder(Request::param());
        }catch (UserException $e){
            return result('',$e->msg,$e->code);
        }
        return result();
    }

    /**
     *用户确认收获
     */
    public function deliveryUserOrder(){
        (new UserValidate())->scene('id')->goCheck(Request::param());
        try{
            $this->user->deliveryUserOrder(Request::param());
        }catch (UserException $e){
            return result('',$e->msg,$e->code);
        }
        return result();
    }

    /**
     * @return \think\response\Json
     * 保存用户信息
     */
    public function saveUserInfo(){
        (new UserValidate())->scene('info')->goCheck(Request::param());
        $this->user->saveUserInfo(Request::param());
        $data = Request::header('token');
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 得到用户的状态,用户标记用户是否为新用户
     */
    public function getUserState(){
        $user = $this->user->getUserState();
        return result($user);
    }

}