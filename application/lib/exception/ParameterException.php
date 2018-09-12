<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/5/23
 * Time: 12:21
 */

namespace app\lib\exception;


use app\lib\exception\base\BaseException;

class ParameterException extends BaseException
{
    public $code = 201;
    public $msg = '参数错误';
    public $errorCode = 20001;
}