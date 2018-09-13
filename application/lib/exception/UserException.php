<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 15:34
 */

namespace app\lib\exception;


use app\lib\exception\base\BaseException;

class UserException extends BaseException
{
    public $code = 901;
    public $msg = '服务器内部错误';
    public $errorCode = 90001;
}