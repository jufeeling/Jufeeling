<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 12:27
 */
return [

    //收货地址标签
    'label' =>
        [
            1 => '家',
            2 => '公司',
            3 => '学校'
        ],

    //商品类别
    'goods_category' =>
        [
            0 => '所有宝贝',
            1 => '精酿啤酒',
            2 => '预调酒水',
            3 => '花式饮料'
        ],

    //微信支付回调地址
    'redirect_notify' => 'jufeel.jufeeling.com/pay/notify',

    //商品筛选条件
    'goods_condition' =>
        [
            0 =>
                [
                    'country'=> ['China','Japan','America','England']

                ],
        ],
];