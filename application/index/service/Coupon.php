<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 15:49
 */

namespace app\index\service;

use app\index\model\Coupon as CouponModel;
use app\index\model\UserCoupon as UserCouponModel;
use app\index\service\Token as TokenService;
use app\lib\exception\CouponException;
use think\facade\Cache;

class Coupon
{
    /**
     * @return mixed
     * 获取所有用户可以领取的优惠券
     */
    public function getAllCoupon()
    {
        //将平台此时可以领取的购物券数据取出 data
        //对比用户购物券
        //如果发现用户已经领取了该购物券
        //则将该条记录从data剔除
        //返回data

      //  $data = Cache::get('coupon');
      //  if(!$data)
      //  {
            $data = CouponModel::where('count', '>', 0)
                ->where('start_time','<',time())
                ->where('end_time', '>', time())
                ->where('state', 0)
                ->select()
                ->toArray();
          //  Cache::set('coupon',$data);
       // }
        $result = array();
        foreach ($data as $key => $d) {
            $userCoupon = UserCouponModel::where('user_id', TokenService::getCurrentUid())
                ->where('coupon_id', $d['id'])
                ->find();
            if (!$userCoupon) {
                array_push($result,$data[$key]);
            }
        }
        return getCouponCategory($result, 2);
    }

    /**
     * @param $data
     * @return bool
     * @throws CouponException
     * 领取优惠券
     */
    public function receiveCoupon($data)
    {
        foreach ($data['coupon'] as $item)
        {
            $coupon = CouponModel::field('count,state,end_time,start_time,species,day')
                ->find($item['id']);
            //先判断是否存在优惠券
            //判断优惠券是否符合领取规则(数量不为0,处于可领取的状态,结束时间没有到)
            //再判断用户是否已经领取过该id的购物券
            if ($coupon) {
                if ($coupon['count'] == 0 || $coupon['state'] == 1 || $coupon['end_time'] < time()) {
                    throw new CouponException(['msg' => '暂时不能领取,请稍后重试']);
                }
                $userCoupon = UserCouponModel::where('coupon_id', $item['id'])
                    ->where('user_id', TokenService::getCurrentUid())
                    ->find();
                if ($userCoupon) {
                    throw new CouponException(['msg' => '不能重复领取优惠券']);
                }
                if($coupon['species'] == CouponModel::NOT_FIXED)
                {
                    $start = strtotime(date("Y-m-d"),time());
                    $add = 86400 * $coupon['day'];
                    $end = $start + $add + 86400 - 1;
                    UserCouponModel::create([
                        'user_id' => TokenService::getCurrentUid(),
                        'coupon_id' => $item['id'],
                        'state' => 0,
                        'end_time' => $end,
                        'start_time' => time()
                    ]);
                }
                else
                {
                    UserCouponModel::create([
                        'user_id' => TokenService::getCurrentUid(),
                        'coupon_id' => $data['id'],
                        'state' => 0,
                        'end_time' => $coupon['end_time'],
                        'start_time' => $coupon['start_time']
                    ]);
                }

                $coupon['count'] -= 1;
                $coupon->save();
            }
            else
            {
                throw new CouponException(['msg' => '未找到该优惠券']);
            }
        }
    }
}