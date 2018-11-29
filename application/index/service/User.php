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
use app\lib\enum\CouponEnum;
use app\lib\enum\OrderStatusEnum;
use app\lib\enum\PartyEnum;
use app\lib\exception\UserException;

class User
{
    /**
     * @return mixed
     * 获取用户举办的派对
     */
    public function getUserHostParty()
    {
        //关联参与者以及评论
        //状态必须为未删除
        //根据create_time排序
        //遍历查询出来的结果
        //得到此时聚会的状态
        //根据聚会state、start_time判断
        $data = PartyModel::withCount('participants')
            ->withCount('message')
            ->where('is_delete', PartyEnum::OPEN)
            ->where('user_id', TokenService::getCurrentUid())
            ->where('status', PartyEnum::OPEN)
            ->order('create_time desc')
            ->select();
        foreach ($data as $d) {
            $d['description'] = html_entity_decode(base64_decode($d['description']));
            $d['site'] = html_entity_decode(base64_decode($d['site']));
            if ($d['state'] == 0) {
                if ($d['start_time'] > time()) {
                    $d['pStatus'] = 1;
                } else {
                    $d['pStatus'] = 2;
                }
            } else {
                if ($d['state'] == 1) {
                    $d['pStatus'] = 3;
                } else {
                    $d['pStatus'] = 2;
                }
            }
        }
        return $data;
    }

    /**
     * @return mixed
     * 获取用户参加的派对
     */
    public function getUserJoinParty()
    {
        //关联派对以及派对的参与者和评论
        //状态必须为未删除(PartyOrder中的Status)
        //根据create_time排序
        //遍历查询出来的结果
        //得到此时聚会的状态
        //根据聚会state、start_time判断
        $data = PartyOrderModel::with(['party' => function ($query) {
            $query->where('is_delete', PartyEnum::OPEN)
                ->withCount('participants')
                ->withCount('message');
        }])
            ->where('status', PartyEnum::OPEN)
            ->where('user_id', TokenService::getCurrentUid())
            ->order('create_time desc')
            ->select();
        foreach ($data as $d) {
            $d['party']['description'] = html_entity_decode(base64_decode($d['party']['description']));
            $d['party']['site'] = html_entity_decode(base64_decode($d['party']['site']));
            if ($d['party']['state'] == 0) {
                if ($d['party']['start_time'] > time()) {
                    $d['party']['pStatus'] = 1;
                } else {
                    $d['party']['pStatus'] = 2;
                }
            } else {
                if ($d['party']['state'] == 1) {
                    $d['party']['pStatus'] = 3;
                } else {
                    $d['party']['pStatus'] = 2;
                }
            }
        }
        return $data;
    }

    /**
     * @param $data
     * @throws UserException
     * 发起者删除派对
     */
    public function deleteUserParty($data)
    {
        //删除派对即修改派对状态
        //将Party表的Status值修改
        $data = ['id' => $data['id'], 'user_id' => TokenService::getCurrentUid()];
        $result = PartyModel::where($data)->setField('status', PartyEnum::CLOSE);
        if ($result == 0) {
            throw new UserException(['msg' => '删除聚会失败...']);
        }
    }

    /**
     * @param $data
     * @throws UserException
     * 参与者删除派对订单
     */
    public function deleteUserPartyOrder($data)
    {
        //删除派对订单即修改派对订单状态
        //将PartyOrder表的Status值修改
        $data =
            [
                'id' => $data['id'],
                'user_id' => TokenService::getCurrentUid()
            ];
        $result = PartyOrderModel::where($data)
            ->setField('status', PartyEnum::CLOSE);
        if ($result == 0) {
            throw new UserException(['msg' => '删除聚会失败...']);
        }
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取用户收货地址
     */
    public function getUserDeliveryAddress()
    {
        $data = DeliveryAddressModel::where('user_id', TokenService::getCurrentUid())
            ->order('id desc')
            ->select()
            ->toArray();
        $status = [
            'hasDefault' => false,
            'pIndex' => -1
        ];
        //判断是否有默认地址 如果有改变标记值并记录该地址的位置
        foreach ($data as $key => $d) {
            if ($d['state'] == 0) {
                $status['hasDefault'] = true;
                $status['pIndex'] = $key;
            }
        }
        //有默认地址的情况
        //先将该地址复制到第一个
        //删除
        if ($status['hasDefault'] == true) {
            array_unshift($data, $data[$status['pIndex']]);
            $status['pIndex'] += 1;
            array_splice($data, $status['pIndex'], 1);
        }
        $result = getAddressLabel($data);
        return $result;
    }

    /**
     * @return mixed
     * 获取用户的购物券
     */
    public function getUserCoupon()
    {
        //三种情况下的筛选条件
        $data['not_use'] = [
            ['status', '=', CouponEnum::Can_Be_Used],
            ['state', '=', CouponEnum::Can_Be_Used],
            ['end_time', '>', time()]
        ];
        $data['used'] = [
            ['status', '=', CouponEnum::Not_to_Use]
        ];
        $data['overdue'] = [
            ['status', '=', CouponEnum::Can_Be_Used],
            ['state', '=', CouponEnum::Can_Be_Used],
            ['end_time', '<', time()]
        ];
        //获取用户 可使用,不可使用,已过期的购物券
        $result['used'] = UserCouponModel::getUserCoupon($data['used'], TokenService::getCurrentUid());
        $result['not_use'] = UserCouponModel::getUserCoupon($data['not_use'], TokenService::getCurrentUid());
        $result['overdue'] = UserCouponModel::getUserCoupon($data['overdue'], TokenService::getCurrentUid());
        $result = $this->getCouponCategory($result);
        return $result;
    }

    private function getCouponCount($data)
    {
        //计算数量
        $data['count']['used'] = sizeof($data['used']);
        $data['count']['not_use'] = sizeof($data['not_use']);
        $data['count']['overdue'] = sizeof($data['overdue']);
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * 获取购物券种类
     */
    private function getCouponCategory($data)
    {
        $data['used'] = getCouponCategory($data['used'], 1);
        $data['not_use'] = getCouponCategory($data['not_use'], 1);
        $data['overdue'] = getCouponCategory($data['overdue'], 1);
        return $this->getCouponDate($data);
    }

    /**
     * @param $data
     * @return mixed
     * 计算购物券的时间
     */
    private function getCouponDate($data)
    {
        $data['used'] = self::dateTransForm($data['used']);
        $data['not_use'] = self::dateTransForm($data['not_use']);
        $data['overdue'] = self::dateTransForm($data['overdue']);
        return $this->getCouponCount($data);
    }

    /**
     * @param $data
     * @return mixed
     * 日期转换
     */
    private function dateTransForm($data)
    {
        foreach ($data as $d) {
            $d['start_time'] = date('Y-m-d', $d['start_time']);
            $d['end_time'] = date('Y-m-d', $d['end_time']);
        }
        return $data;
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取用户的商品
     * 可做缓存
     */
    public function getUserGoods()
    {
        //获取用户使用过的商品
        $data = OrderIdModel::getUserGoods(TokenService::getCurrentUid());
        return $this->organizeGoods($data);
    }

    /**
     * @param $goods
     * @return array
     * 组装数组
     */
    private function organizeGoods($goods)
    {
        $data = array();
        for ($i = 0; $i < count($goods); $i++)
        {
            array_push($data, $goods[$i]);
            for ($j = $i + 1; $j < count($goods); $j++)
            {
                if ($goods[$j]['goods_id'] == $goods[$i]['goods_id'])
                {
                    $data[$i]['count'] += $goods[$j]['count'];
                    array_splice($goods, $j, 1);
                    $j = $j-1;
                }
            }
        }
        return $data;
    }

    /**
     * @param $data
     * @throws UserException
     * 删除来点feel界面商品
     */
    public function deleteUserGoods($data)
    {
        foreach ($data['orders'] as $o) {
            OrderIdModel::where('user_id', TokenService::getCurrentUid())
                ->where('goods_id', $o['order_id'])
                ->setField([
                    'state' => OrderStatusEnum::Delete
                ]);
        }
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取用户的订单
     */
    public function getUserOrder()
    {
        //获取不同状态的订单
        //将已过期和已取消的订单合并(统称为已取消)
        $order['done'] = GoodsOrderModel::getUserGoods(OrderStatusEnum::PAID, TokenService::getCurrentUid());
        //0
        $order['unfinished'] = GoodsOrderModel::getUserGoods(OrderStatusEnum::UNPAID, TokenService::getCurrentUid());
        //1
        $data['overdue'] = GoodsOrderModel::getUserGoods(OrderStatusEnum::Overdue, TokenService::getCurrentUid());
        //2
        $data['cancel'] = GoodsOrderModel::getUserGoods(OrderStatusEnum::Cancel, TokenService::getCurrentUid());
        //3
        //合并已取消以及已过期的订单,统称为已取消
        $order['canceled'] = array_merge($data['cancel'], $data['overdue']);
        array_multisort(array_column($order['canceled'], 'price'), SORT_DESC, $order['canceled']);
        return $order;
    }

    /**
     * @param $data
     * @throws UserException
     * 用户确认收获
     */
    public function deliveryUserOrder($data)
    {
        $order = GoodsOrderModel::field('id,user_id,sign')->find($data['id']);
        if ($order['user_id'] != TokenService::getCurrentUid()
            || $order['sign'] != OrderStatusEnum::Deliveried) {
            throw new UserException(['msg' => '该订单不能确认收货,请重试']);
        }
        $order['sign'] = OrderStatusEnum::Done;
        $order->save();
    }

    /**
     * @param $data
     * @throws UserException
     * 用户删除订单
     */
    public function deleteUserOrder($data)
    {
        $data = GoodsOrderModel::where('user_id', TokenService::getCurrentUid())
            ->where('id', $data['id'])
            ->setField('state', OrderStatusEnum::Delete);
        if ($data == 0) {
            throw new UserException(['msg' => '无法删除此订单,请重试']);
        }
    }

    /**
     * @param $data
     * @throws UserException
     * 用户取消订单
     */
    public function cancelUserOrder($data)
    {
        $data = GoodsOrderModel::where('user_id', TokenService::getCurrentUid())
            ->where('id', $data['id'])
            ->setField('status', 2);
        //取消状态(OrderStatusEnum中的Cancel值不同)
        if ($data == 0) {
            throw new UserException(['msg' => '无法取消此订单,请重试']);
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
            $query->field('order_id,goods_id,price,count')
                ->with(['goods' => function ($query) {
                    $query->field('id,name,thu_url,price');
                }]);
        }])
            ->find($data['id']);
        $order = $this->getOrderInfoStatus($order);
        $order['create_time'] = date('Y-m-d H:i:s', $order['create_time']);
        return $order;
    }

    /**
     * @param $data
     * @return mixed
     * 查看未付款的订单的状态(是否过期,过期的话状态变为2)
     */
    private function getOrderInfoStatus($data)
    {
        //超过一天即为过期(1天的时间戳为60*60*24=86400)
        if ($data['status'] == 0) {
            if (time() - $data['create_time'] > 86400) {
                $data['status'] = 2;
            }
        }
        return $data;
    }

    /**
     *判断用户是否为新用户
     */
    public function getUserState()
    {
        $user = UserModel::field('state')
            ->find(TokenService::getCurrentUid());
        if ($user) {
            if ($user['state'] == 0) {
                $user['state'] = 1;
                $user->save();
                return true;
            } else {
                return false;
            }
        }
        throw new UserException([
            'msg' => '没有该用户'
        ]);
    }

    /**
     * @param $data
     * @throws UserException
     * 得到用户的头像昵称
     */
    public function saveUserInfo($data)
    {
        $user = UserModel::find(TokenService::getCurrentUid());
        $user['nickname'] = $data['nickname'];
        $user['avatar'] = $data['avatar'];
        $user->save();
    }
}