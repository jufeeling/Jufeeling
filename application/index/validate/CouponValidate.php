<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:25
 */

namespace app\index\validate;


class CouponValidate extends BaseValidate
{
    protected $rule =
        [
            'id'      => 'require|isPositiveInteger',
        ];

    protected $message =
        [
            'id'      => '请传入正确的id',
        ];

    protected $scene =
        [
            'id' =>
                [
                    'id'
                ],
        ];
}