<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:56
 */

namespace app\index\validate;

class GoodsValidate extends BaseValidate
{
    protected $rule =
        [
            'id'         => 'require|isPositiveInteger',
            'category'   => 'require',
            'content'    => 'require',
        ];

    protected $message =
        [
            'id'         => '请传入有效的id',
            'category'   => '请传入正确的分类',
            'content'    => '请输入搜索内容',
        ];

    protected $scene =
        [
            'id'       =>
                [
                    'id'
                ],

            'category' =>
                [
                    'category'
                ],

            'search'   =>
                [
                    'content'
                ],

        ];
}