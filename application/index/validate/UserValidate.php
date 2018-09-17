<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 15:13
 */

namespace app\index\validate;


class UserValidate extends BaseValidate
{
    protected $rule =
        [
            'id'     => 'require|isPositiveInteger',
            'check'  => 'require',
        ];

    protected $message =
        [
            'check' => '请选择您要使用的商品',
            'id'    => '请传入正确的id',
        ];

    protected $scene =
        [
            'check' =>
                [
                    'check'
                ],

            'id' =>
                [
                    'id'
                ],
        ];
}