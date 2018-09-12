<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 17:28
 */

namespace app\index\validate;


class PartyValidate extends BaseValidate
{
    protected $rule =
        [
            'id'      => 'require|isPositiveInteger',
            'content' => 'require'
        ];

    protected $message =
        [
            'id'      => '请传入正确的id',
            'content' => '评论内容不能为空'
        ];

    protected $scene =
        [
            'id' =>
                [
                    'id'
                ],

            'comment' =>
                [
                    'id',
                    'content'
                ]
        ];
}