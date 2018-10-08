<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/10
 * Time: 18:52
 */

namespace app\index\service;

use app\index\model\DeliveryAddress as DeliveryAddressModel;
use app\index\model\GoodsOrder as GoodsOrderModel;
use app\index\model\OrderId as OrderIdModel;
use app\index\model\UserCoupon as UserCouponModel;
use app\index\model\User as UserModel;
use app\index\service\Token as TokenService;
use app\index\model\Party as PartyModel;
use app\index\model\PartyOrder as PartyOrderModel;
use app\lib\exception\UserException;
use think\Exception;
use think\facade\Cache;

class User
{
    private $uid;

    public function __construct()
    {
        $this->uid = TokenService::getCurrentUid();
    }

    /**
     * @return mixed
     * 获取用户举办的派对
     */
    public function getUserHostParty()
    {
        $data = PartyModel::withCount('participants')
            ->withCount('message')
            ->where('user_id', $this->uid)
            ->where('status', 0)
            ->order('create_time desc')
            ->select();
        return $data;
    }

    /**
     * @return mixed
     * 获取用户参加的派对
     */
    public function getUserJoinParty()
    {
        $data = PartyOrderModel::with(['party' => function ($query) {
            $query->withCount('participants')
                ->withCount('message');
        }])
            ->where('status', 0)
            ->where('user_id', $this->uid)
            ->order('create_time desc')
            ->select();
        return $data;
    }

    /**
     * @param $data
     * @throws UserException
     * 用户删除派对
     */
    public function deleteUserParty($data){
        $party = PartyModel::where('id',$data['id'])
            ->find();
        if($party){
            if($party['user_id'] == $this->uid){
                $party['status'] = 1;
                $party->save();
            }
            else{
                throw new UserException([
                   'code' => 903,
                   'msg'  => '你无权执行此操作',
                ]);
            }
        }
        else{
            throw new UserException([
                'code' => 904,
                'msg'  => '该派对没有找到',
            ]);
        }
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取用户收货地址
     */
    public function getUserDeliveryAddress()
    {
        $data = DeliveryAddressModel::where('user_id', $this->uid)
            ->select();
        $result = getAddressLabel($data);
        return $result;
    }

    /**
     * @return mixed
     * 获取用户的购物券
     */
    public function getUserCoupon()
    {
        //获取未使用且未过期的购物券
        $data['not_use'] = UserCouponModel::with('coupon')
            ->where('end_time', '>', time())
            ->where('user_id', $this->uid)
            ->where('status',0)
            ->where('state', 0)
            ->select();
        $data['count']['not_use'] = sizeof($data['not_use']);

        //获取使用过的购物券
        $data['used'] = UserCouponModel::with('coupon')
            ->where('user_id', $this->uid)
            ->where('state', 1)
            ->select();
        $data['count']['used'] = sizeof($data['used']);

        //获取过期且未使用过的购物券
        $data['overdue'] = UserCouponModel::with('coupon')
            ->where('end_time', '<', time())
            ->where('user_id', $this->uid)
            ->where('state', 0)
            ->where('status',0)
            ->select();
        $data['count']['overdue'] = sizeof($data['overdue']);
        $data['not_use'] = getCouponCategory($data['not_use'], 1);
        $data['used']    = getCouponCategory($data['used'],    1);
        $data['overdue'] = getCouponCategory($data['overdue'], 1);
        return $data;
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取用户的商品
     */
    public function getUserGoods()
    {
        //获取用户使用过的商品
        $data['used'] = OrderIdModel::getUserGoods(1, $this->uid);
        //获取用户未使用过的商品
        $data['not_use'] = OrderIdModel::getUserGoods(0, $this->uid);
        return $data;
    }

    /**
     * @param $data
     * @throws Exception
     * 用户选择使用的商品
     */
    public function selectUserGoods($data)
    {
        for($i=0;$i<sizeof($data['check']);$i++){
            $orderId[$i] = OrderIdModel::find((int)$data['check'][$i]);
            if ($orderId[$i]['user_id'] != $this->uid) {
                throw new UserException([
                    'code' => 902,
                    'msg' => '您无权使用该商品'
                ]);
            }
            if ($orderId[$i]['select'] == 1) {
                throw new UserException([
                    'code' => 903,
                    'msg' => '该商品已使用'
                ]);
            }
        }
        Cache::set('select',$data['check']);
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取用户的订单
     */
    public function getUserOrder()
    {
        $order = GoodsOrderModel::field('id,order_id,price,status,overdue')
            ->with(['goods' => function ($query) {
                $query->with(['goods' => function ($query) {
                    $query->field('id,thu_url');
                }]);
            }])
            ->withCount('goods')
            ->where('user_id', $this->uid)
            ->select();
        return $order;
    }

    /**
     * @param $data
     * @throws UserException
     * 用户删除订单
     */
    public function deleteUserOrder($data)
    {
        $order = GoodsOrderModel::find($data['id']);
        if ($order) {
            if ($order['user_id'] == $this->uid) {
                $order->delete();
            } else {
                throw new UserException([
                    'code' => '904',
                    'msg' => '您无权删除此订单'
                ]);
            }
        } else {
            throw new UserException([
                'code' => '903',
                'msg' => '未找到该订单,不能删除'
            ]);
        }
    }

    /**
     * @param $data
     * @return array|null|\PDOStatement|string|\think\Model
     * 获取订单详情
     */
    public function getUserOrderInfo($data)
    {
        $order = GoodsOrderModel::with(['goods' => function ($query) {
            $query->field('order_id,goods_id,price')
                ->with(['goods' => function ($query) {
                    $query->field('id,name,thu_url');
                }]);
        }])
            ->where('id', $data['id'])
            ->find();
        return $order['goods'];
    }


    /**
     *检测是否为新用户
     */
    public function checkNewUser(){
        $user = UserModel::find($this->uid);
        if($user['state'] == 0){
            $user['state'] = 1;
            $user->save();
        }
    }

    /**
     * @param $data
     * 修改用户信息
     */
    public function saveUserInfo($data){
        UserModel::where('id',$this->uid)
            ->setField([
               'avatar'   => $data['avatar'],
               'nickname' => $data['nickname']
            ]);
    }
}