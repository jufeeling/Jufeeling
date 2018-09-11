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
        $way = config('way.way');

        return result($way[1]);
    }
}