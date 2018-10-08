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
            'id'          => 'require|isPositiveInteger',
            'content'     => 'require',
            'description' => 'require',
            'image'       => 'require',
            'way'         => 'require',
            'people_no'   => 'require|isPositiveInteger',
            'date'        => 'require',
            'time'        => 'require',
            'site'        => 'require',
        ];

    protected $message =
        [
            'id'          => '请传入正确的id',
            'content'     => '评论内容不能为空',
            'description' => '聚说不能为空',
            'way'         => '方式不能为空',
            'people_no'   => '人数不能为空',
            'date'        => '日期不能为空',
            'time'        => '时间不能为空',
            'site'        => '地点不能为空',
            'url'         => '图片地址不能为空'
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
                ],

            'host' =>
                [
                    'description',
                    'way',
                    'people_no',
                    'date',
                    'time',
                    'site',
                    'image'
                ]
        ];
}