<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 11:50
 */

namespace app\lib\exception;

use app\lib\exception\base\BaseException;

class DeliveryAddressException extends BaseException
{
    public $code = 101;
    public $msg = '新增收获地址出错';
    public $errorCode = 10001;
}