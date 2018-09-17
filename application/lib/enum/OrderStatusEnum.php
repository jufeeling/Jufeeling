<?php

namespace app\lib\enum;


class OrderStatusEnum
{
    // 待支付
    const UNPAID = 0;

    // 已支付
    const PAID = 1;

    // 已发货
    const DELIVERED = 2;

    // 已支付，但库存不足
    const PAID_BUT_OUT_OF = 3;
}