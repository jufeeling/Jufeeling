<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/17
 * Time: 9:34
 */

namespace app\index\service;

use app\index\model\Goods as GoodsModel;
use app\index\model\GoodsOrder as GoodsOrderModel;
use app\index\model\Coupon as CouponModel;
use app\index\model\GoodsOrder;
use app\index\model\UserCoupon;
use app\index\service\Token as TokenService;
use app\index\model\OrderId;
use app\index\model\ShoppingCart;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\GoodsException;
use app\index\model\DeliveryAddress as DeliveryAddressModel;
use app\index\service\User as UserService;

class Order
{
    //用户传过来的商品
    private $oGoods;

    //真实商品
    private $Goods;

    //用户id
    private $uid;

    //折扣价格
    private $coupon_id;

    private $receipt_id;



    /**
     * @param $oGoods
     * @param $coupon_id
     * @param $receipt_id
     * @return string
     * @throws GoodsException
     * 生成订单
     */
    public function generateOrder($oGoods, $coupon_id, $receipt_id)
    {
        $this->coupon_id = $coupon_id;
        $this->oGoods = $oGoods;
        $this->Goods = $this->getGoodsByOrder($oGoods);
        $this->uid = Token::getCurrentUid();
        $this->receipt_id = $receipt_id;
        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            throw new GoodsException([
                'msg' => '抱歉,已没有库存'
            ]);
        }
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        return $order;
    }

    /**
     * @param $orderSnap
     * @return string
     * 生成订单
     */
    public function createOrder($orderSnap)
    {
        $coupon = CouponModel::find($this->coupon_id);

        //修改购物券使用情况
        $userCoupon = UserCoupon::where('user_id',TokenService::getCurrentUid())
            ->where('coupon_id',$coupon['id'])
            ->find();
        $userCoupon['status'] = 1;
        $userCoupon->save();
        $orderId = $this->makeOrderNo();
        $order = new GoodsOrderModel();
        $order['order_id'] = $orderId;
        $order['user_id'] = $this->uid;
        $order['price'] = $orderSnap['price'];
        $order['sale_price'] = $orderSnap['price'] - $coupon['sale'];
        $order['receipt_name'] = $orderSnap['receipt']['receipt_name'];
        $order['receipt_phone'] = $orderSnap['receipt']['receipt_phone'];
        $order['receipt_address'] = $orderSnap['receipt']['receipt_address'];
        $order['status'] = OrderStatusEnum::UNPAID;
        $order->save();
        $create_time = $order['create_time'];
        //判断单个商品购买的个数,并在OrderId表中存进相应的次数
        for ($i = 0; $i < sizeof($this->oGoods); $i++) {
            //查询真实的商品
            $goods = GoodsModel::find($this->oGoods[$i]['goods_id']);
            //减库存
            $goods['stock'] = $goods['stock'] - $this->oGoods[$i]['count'];
            $goods->save();
            //删除购物车记录
            $this->deleteCartRecord($this->oGoods[$i]['goods_id']);
            //生成购买订单
            OrderId::create([
                'user_id' => TokenService::getCurrentUid(),
                'order_id' => $order['id'],
                'goods_id' => $this->oGoods[$i]['goods_id'],
                'price' => $goods['sale_price'] * $this->oGoods[$i]['count'],
                'count' => $this->oGoods[$i]['count'],
                'create_time' => $create_time
            ]);
        }
        //检测是否为新用户(改变状态)
        (new UserService())->checkNewUser();
        return $order['id'];
    }

    /**
     * @param $id
     * 下订单时删除购物车记录
     */
    public function deleteCartRecord($id)
    {
        $data = [
            'user_id' => Token::getCurrentUid(),
            'goods_id' => $id
        ];
        ShoppingCart::where($data)->delete();
    }

    /**
     * @param $status
     * @return array
     * 生成订单快照
     */
    public function snapOrder($status)
    {
        $snap = [
            'price' => 0,
            'pStatus' => [],
            'snapAddress' => null,
        ];
        $snap['price'] = $status['orderPrice'];
        $snap['receipt'] = DeliveryAddressModel::find($this->receipt_id);
        return $snap;
    }

    /**
     * @param $oGoods
     * @return mixed
     * 获取真实商品信息
     */
    private function getGoodsByOrder($oGoods)
    {
        $oGIDs = [];
        foreach ($oGoods as $item) {
            array_push($oGIDs, $item['goods_id']);
        }
        $goods = GoodsModel::all($oGIDs);
        return $goods;
    }

    /**
     * @param $order_id
     * @return array
     * 根据订单号检查产品的库存
     */
    public function checkOrderStock($order_id)
    {
        $Goods = OrderId::where('order_id', $order_id)
            ->field('goods_id,order_id')
            ->select();
        $this->oGoods = $Goods;
        $this->Goods = $this->getGoodsByOrder($this->oGoods);
        $status = $this->getOrderStatus($order_id);
        return $status;
    }

    /**
     * @param $order_id
     * @return array
     */
    private function getOrderStatus($order_id)
    {
        $status =
            [
                'pass' => true,
                'pStatusArray' => []
            ];
        foreach ($this->oGoods as $oGood) {
            $gStatus = $this->getGoodStatus($oGood['goods_id'], $oGood['count'], $this->Goods);
            if (!$gStatus['haveStock']) {
                $status['pass'] = false;
            }
            array_push($status['pStatusArray'], $gStatus);
        }
        $order = GoodsOrder::where('order_id',$order_id)
                ->find();
        $status['totalPrice'] = $order['price'];
        return $status;
    }

    /**
     * @param $oGID
     * @param $oCount
     * @param $goods
     * @return array
     * @throws GoodsException
     * 检查单个商品的库存以及是否存在
     */
    public function getGoodStatus($oGID, $oCount, $goods)
    {
        $pIndex = -1;
        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'count' => 0,
            'totalPrice' => 0,
        ];
        for ($i = 0; $i < count($goods); $i++) {
            if ($oGID == $goods[$i]['id']) {
                $pIndex = $i;
            }
        }
        if ($pIndex == -1) {
            throw new GoodsException(
                [
                    'msg' => 'id为' . $oGID . '的商品不存在，创建订单失败'
                ]);
        } else {
            $good = $goods[$pIndex];
            $pStatus['id'] = $good['id'];
            $pStatus['count'] = $oCount;
            $pStatus['totalPrice'] = $good['price'] * $oCount;
            if ($good['stock'] - $oCount >= 0) {
                $pStatus['haveStock'] = true;
            }
        }
        return $pStatus;
    }

    /**
     * @return string
     * 生成订单号
     */
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    /**
     * @param $data
     * @return mixed
     * 得到预订单
     */
    public function generatePreOrder($data)
    {

        /**
         *保证两种数据格式一样
         */
        if($data['goods_id'] !=0 || $data['count'] !=0){
            $result['goods'] = GoodsModel::where('id', $data['goods_id'])
                ->field('name,category_id,price,sale_price,stock,thu_url')
                ->select();
            $result['goods'][0]['count'] = $data['count'];
        }
        else{
            //定义新数组
            $result['goods'] = [];
            //获取预数据
            $preData['goods'] = ShoppingCart::with(['goods'=>function($query){
                $query->field('id,name,category_id,price,sale_price,stock,thu_url');
            }])->select();
            //将Count添加到预数据的goods中
            foreach ($preData['goods'] as $r){
                $r['goods']['count'] = $r['count'];
            }
            //组装新数组
            foreach ($preData['goods'] as $r) {
                array_push($result['goods'], $r['goods']);
            }

        }
        if(($data['delivery_address']) == 0){
            $result['address'] = DeliveryAddressModel::where('user_id',TokenService::getCurrentUid())
                ->where('state',0)
                ->find();
        }
        else{
            $result['address'] = DeliveryAddressModel::find($data['delivery_address']);
        }
        $result['coupon'] = $this->getOrderCoupon($result['goods']);
        $result['goods_count'] = count($result['goods']);
        $result['price'] = $this->getGoodsInfo($result['goods']);
        $result['coupon_count'] = sizeof($result['coupon']);
        return $result;
    }

    /**
     * @param $data
     * @return int
     *
     */
    private function getGoodsInfo($data){
        $price = 0;
        foreach ($data as $d){
            $price += $d['price'] * $d['count'];
        }
        return $price;
    }
    /**
     * @param $data
     * @return array
     * 得到订单可用的优惠券
     */
    private function getOrderCoupon($data)
    {
        $coupon = array();

        $condition = [
            'user_id' => TokenService::getCurrentUid(),
            'status'  => 0,
            'state'   => 0
        ];
        $coupons = UserCoupon::with('coupon')
            ->where($condition)
            ->where('start_time','<',time())
            ->where('end_time','>',time())
            ->select();
        foreach ($coupons as $c) {
            $count = sizeof($coupon);
            $price = 0;
            if ($c['coupon']['category'] == 0) {
                for ($i = 0; $i < sizeof($data); $i++) {
                    $price += $data[$i]['sale_price'] * $data[$i]['count'];
                }
            } else {
                for ($i = 0; $i < sizeof($data); $i++) {
                    if ($data[$i]['category_id'] == $c['coupon']['category']) {
                        $price += $data[$i]['sale_price'] * $data[$i]['count'];
                    }
                }
            }
            if ($c['coupon']['rule'] <= $price) {
                $coupon[$count] = $c['coupon_id'];
            }
        }
        return $coupon;
    }

}
