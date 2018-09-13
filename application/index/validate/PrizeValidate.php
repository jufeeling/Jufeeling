<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 13:43
 */

namespace app\index\validate;


class PrizeValidate extends BaseValidate
{
    protected $rule =
        [
            'id'          => 'require|isPositiveInteger',
        ];

    protected $message =
        [
            'id'          => '请传入正确的id',
        ];

    protected $scene =
        [
            'id' =>
                [
                    'id'
                ],
        ];
}