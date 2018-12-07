<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 13:44
 */

namespace app\index\service;

use app\index\model\PrizeOrder as PrizeOrderModel;
use app\index\model\Prize as PrizeModel;
use app\index\model\PrizeOrder;
use app\index\service\Token as TokenService;
use app\lib\exception\PrizeException;
use think\facade\Cache;

class Prize
{
    public function prizeDraw($data)
    {
        //参与抽奖
        //先判断是否存在该抽奖记录
        //判断该用户是否已经参与过该次抽奖
        //新增记录
        $prize = PrizeModel::where('state', 0)->find($data['id']);
        if ($prize) {
            $prize_order = PrizeOrderModel::where('prize_id', $data['id'])
                ->where('user_id', TokenService::getCurrentUid())
                ->find();
            if ($prize_order) {
                throw new PrizeException(['msg' => '不可重复参与']);
            }
            if (PrizeOrderModel::create(
                [
                    'form_id' => $data['form_id'],
                    'prize_id' => $data['id'],
                    'user_id' => TokenService::getCurrentUid()
                ]
            )) {
                Cache::rm('prize');
            }
            else {
                throw new PrizeException();
            }
        } else {
            throw new PrizeException(['msg' => '未找到该抽奖奖品']);
        }
    }

    /**
     * 获取试手气数据
     */
    public function getPrizeInfo()
    {
        //判定是否存在(存在返回,不存在从数据库拿数据)
        //设置缓存
//        $cachePrize = Cache::get('prize');
//        if ($cachePrize)
//        {
//            $prizeInfo = $cachePrize;
//        }
//        else
//        {
            $prizeInfo = PrizeModel::with(['goods' => function ($query) {
                $query->field('id,thu_url,name');
            }])
                ->withCount('orders')
                ->where('open_prize_time','>',time())
                ->where('state', 0)
                ->find();
       // }
        if ($prizeInfo) {
     //       Cache::set('prize', $prizeInfo);
            return $this->assemblePrizeData($prizeInfo);
        }
        throw new PrizeException(['code' => 802, 'msg' => '暂时没有奖品参与抽奖']);
    }

    /**
     * @param $data
     * @return mixed
     * @throws PrizeException
     * 从模板消息跳进来
     */
    public function getPrizeInfoById($data)
    {
        //先判定redis缓存中是否存在该条记录
        //如果存在则赋值给prizeInfo
        //如果不存在则从数据库取出数据并赋值给prizeInfo和redis缓存
        //处理数据
        $prizeInfo = PrizeModel::with(['goods' => function ($query) {
                $query->field('id,thu_url,name');
            }])
                ->where('state',1)
                ->field('id,goods_id')
                ->withCount('orders')
                ->find($data['id']);
        if ($prizeInfo)
        {
            $prizeInfo['orders_count'] +=100;
            $prizeInfo['self'] = PrizeOrderModel::where('prize_id', $data['id'])
                ->where('user_id',TokenService::getCurrentUid())
                ->field('state')
                ->find();

            $prizeInfo['isPrize'] = PrizeOrderModel::with(['user'=>function($query){
                $query->field('nickname,id,avatar');
            }])
                ->field('user_id')
                ->where('prize_id', $data['id'])
                ->where('state',1)
                ->find();
            $prizeInfo['goods']['name'] = html_entity_decode($prizeInfo['goods']['name'] );
            return $prizeInfo;
        }
        throw new PrizeException(['code' => 802, 'msg' => '未找到该记录']);
    }

    /**
     * @param $data
     * @return mixed
     * 重新组装数据
     */
    private function assemblePrizeData($data)
    {
        $data['orders_count'] += 100;
        $data['open_prize_time'] = date('m-d H:i', $data['open_prize_time']);
        $order = PrizeOrderModel::with('user')->where('prize_id', $data['id'])
            ->where('user_id', TokenService::getCurrentUid())
            ->find();
        if ($order) {
            $data['is_draw'] = true;
        } else {
            $data['is_draw'] = false;
        }
        $data['avatar'] = $this->getPrizeUserAvatar($order['user']);
        return $data;
    }

    /**
     * @param $user
     * @return mixed
     * 获取虚拟头像
     */
    private function getPrizeUserAvatar($user)
    {
        $data = config('jufeel_config.avatar');
        //将0到19列成一个数组
        //打乱数组
        //截取数组中的某一段得到新数组
        //遍历拿到随机数中的头像
        //返回
        $numbers = range(0, 36);
        shuffle($numbers);
        $result = array_slice($numbers, 0, 16);
        for ($i = 0; $i < 16; $i++) {
            $avatar[$i] = $data[$result[$i]];
        }
        if ($user) {
            $avatar[0] = $user['avatar'];
        }
        return $avatar;
    }
}