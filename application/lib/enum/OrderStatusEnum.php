<?php

namespace app\lib\enum;


class OrderStatusEnum
{
    // 待支付
    const UNPAID = 0;

    // 已支付
    const PAID = 1;

    //过期
    const Overdue = 2;

    //未发货
    const NotDelivery = 0;

    //已发货
    const Deliveried = 1;

    //订单已完成

    const Done = 2;

    //OrderId下的删除
    const Delete = 1;

    const Undelete = 0;

    //未使用过的
    const NotUse = 0;

    //使用过的
    const Used = 1;

    const Cancel = 3;
}