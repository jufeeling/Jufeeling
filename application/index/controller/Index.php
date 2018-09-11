<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/8/10
 * Time: 11:13
 */

namespace app\index\controller;


use app\index\model\User;
use think\Controller;

class Index extends Controller
{
    public function index(){
        User::create([
           'openid' => '1',
            'nickname'=> 's',
            'avatar' => 's',
            'state' =>1,
            'delete_time' => 1
        ]);
    }
}