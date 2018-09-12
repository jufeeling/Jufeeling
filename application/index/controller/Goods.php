<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:01
 */

namespace app\index\controller;

use app\index\service\Goods as GoodsService;
use think\App;
use think\Controller;

class Goods extends Controller
{
    private $goods;
    public function __construct(App $app = null,GoodsService $goods)
    {
        $this->goods = $goods;
        parent::__construct($app);
    }

    public function getAllGoods(){
        $data = $this->goods->getAllGoods();
        return result($data);
    }
}