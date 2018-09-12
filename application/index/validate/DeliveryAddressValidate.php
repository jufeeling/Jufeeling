<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 11:39
 */

namespace app\index\validate;


class DeliveryAddressValidate extends BaseValidate
{
    protected $rule =
        [
            'id'      => 'require|isPositiveInteger',
            'name'    => 'require',
            'phone'   => 'require|isMobile',
            'area'    => 'require',
            'address' => 'require',
            'label'   => 'require|number|between:1,3',
            'state'   => 'require|number|between:0,1'
        ];

    protected $message =
        [
            'id'      => '请传入正确的id',
            'name'    => '收货人不能为空',
            'phone'   => '请输入正确的联系方式',
            'area'    => '所在地区不能为空',
            'address' => '详细地址不能为空',
            'label'   => '请选择正确的标签',
            'state'   => '状态出错'  //设置是否为默认地址
        ];

    protected $scene =
        [
            'add' =>
                [
                    'name',
                    'phone',
                    'area',
                    'address',
                    'label',
                    'state'
                ],

            'id' =>
                [
                    'id'
                ],

            'update' =>
                [
                    'id',
                    'name',
                    'phone',
                    'area',
                    'address',
                    'label',
                    'state'
                ],
        ];
}