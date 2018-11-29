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
use app\index\model\GoodsOrder;
use app\index\model\UserCoupon;
use app\index\service\Token as TokenService;
use app\index\model\OrderId;
use app\index\model\ShoppingCart;
use app\lib\enum\CouponEnum;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\GoodsException;
use app\index\model\DeliveryAddress as DeliveryAddressModel;
use app\lib\exception\OrderException;
use think\Db;

define('startPrice',-10);

class Order
{

    private $couponPrice = startPrice;
    //用户传过来的商品
    private $oGoods;
    //真实商品
    private $Goods;
    //用户id
    private $uid;
    //折扣价格
    private $coupon_id;
    private $receipt_id;
    private $sale_price;
    private $carriage;

    /**
     * @param $oGoods
     * @param $coupon_id
     * @param $receipt_id
     * @param $carriage
     * @return string
     * @throws GoodsException
     * 生成订单
     */
    public function generateOrder($oGoods, $coupon_id, $receipt_id ,$carriage)
    {
        (new Cart())->saveCacheToDb();
        $this->coupon_id = $coupon_id;
        $this->oGoods = $oGoods;
        $this->receipt_id = $receipt_id;
        $this->carriage = $carriage;
        $this->Goods = $this->getGoodsByOrder($oGoods);
        $this->uid = Token::getCurrentUid();

        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            throw new GoodsException(['msg' => '抱歉,已没有库存']);
        }
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        return $order;
    }

    /**
     * @param $orderSnap
     * @return mixed
     * @throws OrderException
     * 生成订单
     */
    public function createOrder($orderSnap)
    {
        //检查购物券
        //计算出折扣价格
        $this->checkCoupon();
        //订单入库并返回id
        $id = $this->saveOrder($orderSnap);
        //单个orderId入库
        //减库存
        //删除购物车记录(有该订单的记录)
        for ($i = 0; $i < sizeof($this->oGoods); $i++) {
            //开启事务
            $model = Db::table('goods');
            $model->startTrans();
            $goods = GoodsModel::field('id,stock,sale_price,sold')->lock(true)
                ->find($this->oGoods[$i]['goods_id']);
            $goods['stock'] -= $this->oGoods[$i]['count'];
            $goods['sold'] +=  $this->oGoods[$i]['count'];
            //成功提交事务
            if ($goods->save()) {
                $model->commit();
            } else {
                $model->rollback();
            }
            $goods->save();
            $this->deleteCartRecord($this->oGoods[$i]['goods_id']);
            OrderId::create([
                'user_id' => TokenService::getCurrentUid(),
                'order_id' => $id,
                'goods_id' => $this->oGoods[$i]['goods_id'],
                'price' => $goods['sale_price'],
                'count' => $this->oGoods[$i]['count'],
                'create_time' => time()
            ]);
        }
        return $id;
    }

    /**
     * 查看此订单是否使用购物券
     */
    private function checkCoupon()
    {
        //修改购物券使用情况
        $userCoupon = UserCoupon::with('coupon')
            ->where('user_id', TokenService::getCurrentUid())
            ->where('coupon_id', $this->coupon_id)
            ->find();
        if ($userCoupon) {
            $userCoupon['status'] = 1;
            $userCoupon->save();
            $this->sale_price = $userCoupon['coupon']['sale'];
        } else {
            $this->sale_price = 0;
        }
    }

    /**
     * @param $orderSnap
     * @return int|string
     * @throws OrderException
     * 将订单存入数据库
     */
    private function saveOrder($orderSnap)
    {
        if ($orderSnap['price'] - $this->sale_price < 0.01) {
            throw new OrderException(['msg' => '支付价格错误']);
        }
        $value =
            [
                'price' => $orderSnap['price'],
                'status' => OrderStatusEnum::UNPAID,
                'user_id' => TokenService::getCurrentUid(),
                'order_id' => $this->makeOrderNo(),
                'sale' => $this->sale_price,
                'sale_price' => $orderSnap['price'] - $this->sale_price,
                'receipt_name' => $orderSnap['receipt']['receipt_name'],
                'receipt_phone' => $orderSnap['receipt']['receipt_phone'],
                'receipt_address' => $orderSnap['receipt']['receipt_area'].$orderSnap['receipt']['receipt_address'],
                'coupon_id'   => $this->coupon_id,
                'receipt_id' => $this->receipt_id,
                'carriage' => $this->carriage,
                'create_time' => time()
            ];
        return GoodsOrderModel::insertGetId($value);
    }

    /**
     * @param $id
     * 下订单时删除购物车记录
     */
    public function deleteCartRecord($id)
    {
        $data = ['user_id' => Token::getCurrentUid(), 'goods_id' => $id];
        ShoppingCart::where($data)->delete();
    }

    /**
     * @param $status
     * @return array
     * 生成订单快照
     */
    public function snapOrder($status)
    {
        $snap = ['price' => 0, 'pStatus' => [], 'snapAddress' => null];
        $snap['price'] = $status['orderPrice'];
        $snap['receipt'] = DeliveryAddressModel::find($this->receipt_id);
        return $snap;
    }

    /**
     * @param $oGoods
     * @return array
     * @throws GoodsException
     * 获取真实商品信息
     */
    private function getGoodsByOrder($oGoods)
    {
        $oGIDs = [];
        $goods = [];
        foreach ($oGoods as $item) {
            array_push($oGIDs, $item['goods_id']);
        }
        foreach ($oGIDs as $item)
        {
            $record = GoodsModel::field('id,name,stock,price,sale_price,sold,carriage,state')
                ->find($item);
            if($record['state'] == 0)
            {
                array_push($goods,$record);
            }
            else
            {
                throw new GoodsException([
                    'msg' => $record['name'] . '商品已下架'
                ]);
            }
        }
        return $goods;
    }

    /**
     * @param $order_id
     * @return array
     * 根据订单号检查产品的库存
     */
    public function checkOrderStock($order_id)
    {
        $Goods = OrderId::where('order_id', $order_id)->select();
        $this->oGoods = $Goods;
        $this->Goods = $this->getGoodsByOrder($this->oGoods);
        $status = $this->getOrderStatus();
        return $status;
    }

    /**
     * @return array
     */
    private function getOrderStatus()
    {
        $status =
            [
                'orderPrice' => 0,
                'pass' => true,
                'pStatusArray' => []
            ];
        foreach ($this->oGoods as $oGood) {
            $gStatus = $this->getGoodStatus($oGood['goods_id'], $oGood['count'], $this->Goods);
            if (!$gStatus['haveStock']) {
                $status['pass'] = false;
            }
            $status['orderPrice'] += $gStatus['totalPrice'];
            array_push($status['pStatusArray'], $gStatus);
        }
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
        $pStatus =
            [
                'id' => null,
                'haveStock' => false,
                'count' => 0,
                'totalPrice' => 0
            ];
        for ($i = 0; $i < count($goods); $i++) {
            if ($oGID == $goods[$i]['id']) {
                $pIndex = $i;
            }
        }
        if ($pIndex == -1) {
            throw new GoodsException(['msg' => 'id为' . $oGID . '的商品不存在，创建订单失败']);
        }
        else
        {
            $good = $goods[$pIndex];
            $pStatus['id'] = $good['id'];
            $pStatus['count'] = $oCount;
            $pStatus['totalPrice'] = $good['sale_price'] * $oCount;
            if ($good['stock'] - $oCount >= 0) {
                $pStatus['haveStock'] = true;
            }
            else
            {
                throw new GoodsException(['msg' => $good['name'] . '库存不足']);
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
        $orderSn = $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
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
        //先将缓存中购物车提交到数据库
        //获取提交的数据的商品
        //获取默认地址
        //获取预订单总价格
        //获取此订单可用的优惠券以及数量
        //  $carriage = 0;


        $count = count($data['goods']);
        $result['goods_count'] = 0;
        $carriage = 0;
        for ($i = 0; $i < $count; $i++) {
            $result['goods'][$i] = GoodsModel::where('id', $data['goods'][$i]['goods_id'])
                ->field('id,name,category_id,price,sale_price,stock,thu_url,carriage')
                ->find();
            $carriage += $result['goods'][$i]['carriage'];
            $result['goods'][$i]['count'] = $data['goods'][$i]['count'];
            $result['goods_count'] += $result['goods'][$i]['count'];
        }
        $result['address'] = DeliveryAddressModel::getDefaultAddress(TokenService::getCurrentUid());
        if(empty($result['address']))
        {
            $result['address'] = DeliveryAddressModel::where('user_id',TokenService::getCurrentUid())->find();
        }
        $result['price'] = $this->getGoodsPrice($result['goods']);
        $result['coupon'] = $this->getOrderCoupon($result['goods']);
        $result['coupon_count'] = sizeof($result['coupon']);
        $result['carriage'] = $carriage;
        return $result;
    }

    /**
     * @param $data
     * @return int
     *
     */
    private function getGoodsPrice($data)
    {
        $price = 0;
        foreach ($data as $d) {
            $nowPrice = bcmul ($d['sale_price'],$d['count'],1);
            $price = bcadd($price,$nowPrice,1);
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
        //先获取用户所有可用的优惠券
        //遍历优惠券
        //判断优惠券的种类
        //如果是所有商品类,则直接判断优惠券的规则是否达到订单的金额
        //如果不是所有商品类,则判断该分类下的订单的商品是否已达到规则
        //记录id
        $coupon = array();
        $val = [
            ['user_id', '=', TokenService::getCurrentUid()],
            ['status', '=', CouponEnum::Can_Be_Used],
            ['state', '=', CouponEnum::Can_Be_Used],
            ['start_time', '<', time()],
            ['end_time', '>', time()]
        ];
        $coupons = UserCoupon::with('coupon')
                             ->where($val)
                             ->select();
        foreach ($coupons as $item)
        {
            if($item['coupon']['category'] == 0)
            {
                foreach ($data as $dItem)
                {
                    $this->couponPrice += $dItem['sale_price'] * $dItem['count'];
                }
            }
            else
            {
                foreach ($data as $dItem)
                {
                    if($dItem['category_id'] == $item['coupon']['category'])
                    {
                        $this->couponPrice += $dItem['sale_price'] * $dItem['count'];
                    }
                }
            }
            if($this->couponPrice > startPrice)
            {
                if($item['coupon']['rule'] <= $this->couponPrice)
                {
                    array_push($coupon,$item['coupon_id']);
                }
            }
            $this->couponPrice = startPrice;
        }
        return $coupon;
    }
}