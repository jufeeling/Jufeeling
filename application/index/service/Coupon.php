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

class Coupon
{
    /**
     * @return mixed
     * 获取所有有效的优惠券
     */
    public function getAllCoupon()
    {
        $data = CouponModel::where('count', '>', 0)
            ->where('end_time', '<', time())
            ->where('state', 0)
            ->select();
        $result = getCouponCategory($data, 2);
        return $result;
    }

    /**
     * @param $data
     * @return bool
     * @throws CouponException
     * 领取优惠券
     */
    public function receiveCoupon($data)
    {
        $coupon = CouponModel::find($data['id']);
        if ($coupon) {
            //判断优惠券的数量
            if ($coupon['count'] == 0) {
                throw new CouponException([
                    'code' => 703,
                    'msg' => '该优惠券已被全部领取',
                    'errorMsg' => 70003
                ]);
            }
            //判断优惠券此时的状态,是否管理员设置不可领取
            else if ($coupon['state'] == 1) {
                throw new CouponException([
                    'code' => 704,
                    'msg' => '该优惠券暂时不能领取',
                    'errorMsg' => 70004
                ]);
            }
            //判断购物券是否过期
            else if ($coupon['end_time'] < time()) {
                throw new CouponException([
                    'code' => 705,
                    'msg' => '该优惠券已过期',
                    'errorMsg' => 70005
                ]);
            }
            //全部排除则领取成功
            else if (
            UserCouponModel::create([
                'user_id' => TokenService::getCurrentUid(),
                'coupon_id' => $data['id'],
                'state' => 0,
                'end_time' => $coupon['end_time'],
                'start_time' => $coupon['start_time']
            ])
            ) {
                $coupon['count'] -= 1;
                $coupon->save();
            } else {
                throw new CouponException();
            }
        } else {
            throw new CouponException([
                'code' => 702,
                'msg' => '未找到该优惠券',
                'errorMsg' => 70002
            ]);
        }
    }
}