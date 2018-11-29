<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 13:42
 */

namespace app\index\controller;

use app\index\validate\PrizeValidate;
use app\lib\exception\PrizeException;
use think\App;
use think\Controller;
use think\facade\Request;
use app\index\service\Prize as PrizeService;

class Prize extends BaseController
{
    private $prize;

    public function __construct(App $app = null, PrizeService $prize)
    {
        $this->prize = $prize;
        parent::__construct($app);
    }

    /**
     * @return \think\response\Json
     * 抽奖
     */
    public function prizeDraw()
    {
        (new PrizeValidate())->scene('id')->goCheck(Request::param());
        try {
            $this->prize->prizeDraw(Request::param());
        } catch (PrizeException $e) {
            return result('', $e->msg, $e->code);
        }
        return result();
    }

    /**
     * @return \think\response\Json
     * 获取当前试手气数据
     */
    public function getPrizeInfo()
    {
        try {
            $data = $this->prize->getPrizeInfo();
        } catch (PrizeException $e) {
            return result('', $e->msg, $e->code);
        }
        return result($data);
    }

    public function getPrizeInfoById()
    {
        (new PrizeValidate())->scene('info')->goCheck(Request::param());
        $data = $this->prize->getPrizeInfoById(Request::param());
        return result($data);
    }
}