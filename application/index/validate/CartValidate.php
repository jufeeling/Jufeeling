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
            'goods_id' => 'require|isPositiveInteger',
            'count'    => 'require|isPositiveInteger',
            'id'       => 'require|isPositiveInteger',
            'select'   => 'require',
        ];

    protected $message =
        [
            'goods'  => '商品列表不能为空',
            'count'  => '数量不能为空',
            'id'     => 'id不能为空',
            'select' => '选择的状态不能为空'
        ];

    protected $scene =
        [
            'add' =>
                [
                    'goods_id',
                    'count'
                ],

            'count' =>
                [
                    'count',
                    'id'
                ],

            'id' =>
                [
                    'id'
                ],

            'select' =>
                [
                    'id',
                    'select'
                ],

            'all' =>
                [
                    'select'
                ],
        ];
}