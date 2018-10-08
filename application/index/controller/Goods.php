<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:01
 */

namespace app\index\controller;

use app\index\service\Goods as GoodsService;
use app\index\validate\GoodsValidate;
use app\lib\exception\GoodsException;
use think\App;
use think\Controller;
use think\facade\Request;

class Goods extends BaseController
{
    private $goods;

    public function __construct(App $app = null, GoodsService $goods)
    {
        $this->goods = $goods;
        parent::__construct($app);
    }

    /**
     * @return \think\response\Json
     * 获取所有商品
     */
    public function getAllGoods()
    {
        (new GoodsValidate())->scene('category')->goCheck(Request::param());
        $data = $this->goods->getAllGoods(Request::param());
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 筛选商品
     */
    public function conditionGoods(){
        (new GoodsValidate())->scene('condition')->goCheck(Request::param());
        $data = $this->goods->conditionGoods(Request::param());
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 获取推荐商品
     */
    public function getRecommendGoods(){
        $data = $this->goods->getRecommendGoods();
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 获取商品详情
     */
    public function getGoodsDetail()
    {
        (new GoodsValidate())->scene('id')->goCheck(Request::param());
        try {
            $data = $this->goods->getGoodsDetail(Request::param());
        } catch (GoodsException $e) {
            return result('', $e->msg, $e->code);
        }
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 获取搜索的内容
     */
    public function getSearchGoods()
    {
        (new GoodsValidate())->scene('search')->goCheck(Request::param());
        $data = $this->goods->getSearchGoods(Request::param());
        return result($data);
    }
}