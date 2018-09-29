<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 14:29
 */

namespace app\index\model;


use think\Model;

class UserCoupon extends Model
{
    protected $hidden =
        [
            'user_id'
        ];

    /**
     * @return \think\model\relation\BelongsTo
     * 购物券
     */
    public function coupon()
    {
        return $this->belongsTo('Coupon', 'coupon_id', 'id');
    }

}