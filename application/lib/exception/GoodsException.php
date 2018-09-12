<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:59
 */

namespace app\lib\exception;


use app\lib\exception\base\BaseException;

class GoodsException extends BaseException
{
    public $code = 501;
    public $msg = '未找到该商品';
    public $errorCode = 50001;
}