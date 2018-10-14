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
            'id'      => 'require|isPositiveInteger',
            'form_id' => 'require'
        ];

    protected $message =
        [
            'id'      => '请传入正确的id',
            'form_id' => '请传入form_id'
        ];

    protected $scene =
        [
            'id' =>
                [
                    'id',
                    'form_id'
                ],
        ];
}