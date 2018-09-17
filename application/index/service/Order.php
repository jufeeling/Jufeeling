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
use app\index\model\OrderId;
use app\lib\exception\GoodsException;
use app\index\model\DeliveryAddress as DeliveryAddressModel;

class Order
{
    //用户传过来的商品
    private $oGoods;

    //真实商品
    private $Goods;

    //用户id
    private $uid;

    //折扣价格
    private $salePrice;

    private $receipt_id;

    /**
     * @param $oGoods
     * @param $salePrice
     * @param $receipt_id
     * @return string
     * @throws GoodsException
     * 生成订单
     */
    public function generateOrder($oGoods, $salePrice, $receipt_id)
    {
        $this->salePrice = $salePrice;
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
        $orderId = $this->makeOrderNo();
        $order = new GoodsOrderModel();
        $order['order_id'] = $orderId;
        $order['user_id'] = $this->uid;
        $order['price'] = $orderSnap['price'];
        $order['receipt_name'] = $orderSnap['receipt']['receipt_name'];
        $order['receipt_phone'] = $orderSnap['receipt']['receipt_phone'];
        $order['receipt_address'] = $orderSnap['receipt']['receipt_address'];
        $order->save();
        $create_time = $order['create_time'];
        //判断单个商品购买的个数,并在OrderId表中存进相应的次数
        for ($i = 0; $i < sizeof($this->oGoods); $i++) {
            $goods = GoodsModel::find($this->oGoods[$i]['goods_id']);
            for ($j = 0; $j < $this->oGoods[$i]['count']; $j++) {
                OrderId::create([
                    'order_id' => $orderId,
                    'goods_id' => $this->oGoods[$i]['goods_id'],
                    'price' => $goods['sale_price'],
                    'create_time' => $create_time
                ]);
            }
        }
        return $orderId;
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
        $goods = GoodsModel::all($oGIDs)
            ->field(['id', 'price', 'stock'])
            ->toArray();
        return $goods;
    }


    /**
     * @return array
     * 获取整个订单信息,检查订单是否符合要求(库存的检测以及商品的真实性)
     */
    private function getOrderStatus()
    {
        $status =
            [
                'pass' => true,
                'orderPrice' => -$this->salePrice,
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

}
