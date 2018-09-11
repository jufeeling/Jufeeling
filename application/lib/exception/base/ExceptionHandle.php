<?php
/**
 * Created by PhpStorm.
 * User: larry
 * Date: 2018/5/22
 * Time: 下午5:33
 */

namespace app\lib\exception\base;

use think\exception\Handle;
use think\facade\Request;
use think\facade\Log;

class ExceptionHandle extends Handle
{
    private $code;
    private $msg;
    private $errorCode;

    //需要返回客户端当前请求的URL路径
    public function render(\Exception $e)
    {

        if ($e instanceof BaseException) {
            //如果是自定义异常
            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;
        } else {
            if (config('app_debug')) {
                return parent::render($e);
            } else {
                $this->code = 500;
                $this->msg = '服务器内部错误';
                $this->errorCode = 999;
                $this->recordErrorLog($e);
            }
        }

        $result = [
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => Request::url()
        ];
        return json($result,$this->code);
    }
    private function recordErrorLog(\Exception $e)
    {
        Log::record($e->getMessage(),'error');
    }
}