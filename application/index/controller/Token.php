<?php


namespace app\index\controller;

use app\index\service\UserToken;
use app\index\validate\TokenGet;
use app\index\service\Token as TokenService;
use app\lib\exception\ParameterException;
use think\Controller;
use think\facade\Request;

class Token extends Controller
{
    public function getToken()
    {
        (new TokenGet())->scene('check')->goCheck(Request::param());
        $ut = new UserToken(Request::param('code'));
        $token = $ut->get();
        return result(['token' => $token,], '获取成功');
    }

    public function verifyToken($token = '')
    {
        if (!$token) {
            throw new ParameterException([
                'token不允许为空'
            ]);
        }
        $valid = TokenService::verifyToken($token);
        return result(['isValid' => $valid], '获取成功');
    }
}