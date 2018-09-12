<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 15:49
 */

namespace app\index\service;

use app\index\model\Coupon as CouponModel;

class Coupon
{
    public function getAllCoupon(){
        $data = CouponModel::where('count','>',0)
            ->where('end_time','<',time())
            ->select();
        $result = getCouponCategory($data,2);
        return $result;
    }
}