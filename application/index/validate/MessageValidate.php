<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/25
 * Time: 14:06
 */

namespace app\index\validate;


class MessageValidate extends BaseValidate
{
    protected $rule =
        [
            'content'         => 'require',
        ];

    protected $message =
        [
            'content'    => '请输入搜索内容',
        ];

    protected $scene =
        [

            'send' =>
                [
                    'content',
                ]

        ];
}