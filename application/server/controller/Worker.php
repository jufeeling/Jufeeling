<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/21
 * Time: 11:59
 */

namespace app\server\controller;
use think\worker\Server;
use Workerman\Lib\Timer;

class Worker extends Server
{
    protected $processes=1;
    public function onWorkerStart($work)
    {
        Timer::add(1, array($this, 'index'), array(), true);
    }


    public function index()
    {
        $a = \app\index\model\Goods::where('id',1)->find();
        $a['stock'] = $a['stock'] + 1;
        $a->save();
    }
}