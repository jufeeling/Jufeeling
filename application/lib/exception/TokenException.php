<?php
/**
 * Created by 七月.
 * Author: 七月
 * Date: 2017/5/22
 * Time: 16:56
 */

namespace app\lib\exception;


use app\lib\exception\base\BaseException;

class TokenException extends BaseException
{
    public $code = 401;
    public $msg = 'Token已过期或无效Token';
    public $errorCode = 40001;
}