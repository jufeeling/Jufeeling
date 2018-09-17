<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/17
 * Time: 13:58
 */

namespace app\lib\exception;


use app\lib\exception\base\BaseException;

class OrderException extends BaseException
{
    public $code = 510;
    public $msg = '订单生成失败';
    public $errorCode = 50010;
}