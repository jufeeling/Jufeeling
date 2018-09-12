<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 15:48
 */

namespace app\index\controller;

use app\index\service\Coupon as CouponService;
use think\App;
use think\Controller;

class Coupon extends Controller
{
    private $coupon;

    public function __construct(App $app = null,CouponService $coupon)
    {
        $this->coupon = $coupon;
        parent::__construct($app);
    }

    /**
     * @return mixed
     * 获取所有购物券
     */
    public function getAllCoupon(){
        $data = $this->coupon->getAllCoupon();
        return result($data);
    }
}