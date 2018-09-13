<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 13:45
 */

namespace app\lib\exception;


use app\lib\exception\base\BaseException;

class PrizeException extends BaseException
{
    public $code = 801;
    public $msg = '抽奖失败';
    public $errorCode = 80001;
}