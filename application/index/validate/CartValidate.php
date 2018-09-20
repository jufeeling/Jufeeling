<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/18
 * Time: 15:36
 */

namespace app\index\validate;

class CartValidate extends BaseValidate
{
    protected $rule =
        [
            'goods_id'      => 'require|isPositiveInteger',
            'count'         => 'require|isPositiveInteger',
        ];

    protected $message =
        [
            'goods'      => '商品列表不能为空',
        ];

    protected $scene =
        [
            'add' =>
                [
                    'goods_id',
                    'count'
                ],
        ];
}