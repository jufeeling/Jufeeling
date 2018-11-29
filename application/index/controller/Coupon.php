<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 15:48
 */

namespace app\index\controller;

use app\index\service\Coupon as CouponService;
use app\index\validate\CouponValidate;
use app\lib\exception\CouponException;
use think\App;
use think\Controller;
use think\facade\Request;

class Coupon extends BaseController
{
    private $coupon;

    public function __construct(App $app = null, CouponService $coupon)
    {
        $this->coupon = $coupon;
        parent::__construct($app);
    }

    /**
     * @return mixed
     * 获取所有购物券
     */
    public function getAllCoupon()
    {
        $data = $this->coupon->getAllCoupon();
        if(sizeof($data) > 0)
        {
            return result($data);
        }
        return result('','没有优惠券可以领取',100);
    }

    /**
     * @return \think\response\Json
     * 领取优惠券
     */
    public function receiveCoupon()
    {
        (new CouponValidate())->scene('id')->goCheck(Request::param());
        try {
            $this->coupon->receiveCoupon(Request::param());
        } catch (CouponException $e) {
            return result('', $e->msg, $e->code);
        }
        return result();
    }
}