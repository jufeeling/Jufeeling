<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:21
 */

namespace app\lib\exception;


use app\lib\exception\base\BaseException;

class CouponException extends BaseException
{
    public $code = 701;
    public $msg = '领取失败';
    public $errorCode = 70001;
}