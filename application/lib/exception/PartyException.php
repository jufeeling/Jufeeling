<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 17:35
 */

namespace app\lib\exception;


use app\lib\exception\base\BaseException;

class PartyException extends BaseException
{
    public $code = 601;
    public $msg = '没有找到该聚会';
    public $errorCode = 60001;
}