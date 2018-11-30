<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:01
 */

namespace app\index\controller;

use app\index\model\Title as TitleModel;
use app\index\service\Goods as GoodsService;
use app\index\validate\GoodsValidate;
use app\lib\exception\GoodsException;
use think\App;
use think\Controller;
use think\facade\Cache;
use think\facade\Request;

class Goods extends Controller
{

    public function __construct(App $app = null)
    {
        parent::__construct($app);
    }

    /**
     * @return \think\response\Json
     * 获取推荐提示(聚小喵今日推荐)
     */
    public function getRecommendTitle()
    {
        $data = Cache::get('title');
        if($data){
            return result($data);
        }
        $data = TitleModel::where('status',0)
            ->find();
        Cache::set('title',$data,3600);
        return result($data);
    }

    /**
     * @param GoodsService $goods
     * @return \think\response\Json
     * 获取所有商品
     */
    public function getAllGoods(GoodsService $goods)
    {
        (new GoodsValidate())->scene('category')->goCheck(Request::param());
        $data = $goods->getAllGoods(Request::param());
      //  return result('','商品全部下架',404);
        return result($data);
    }

    /**
     * @param GoodsService $goods
     * @return \think\response\Json
     * 筛选商品
     */
    public function conditionGoods(GoodsService $goods)
    {
        (new GoodsValidate())->scene('condition')->goCheck(Request::param());
        $data = $goods->conditionGoods(Request::param());
      //  return result('','商品全部下架',404);
        if(count($data['data']) == 0)
        {
            return result($data,'',100);
        }
        return result($data);
    }

    /**
     * @param GoodsService $goods
     * @return \think\response\Json
     * 获取推荐商品
     */
    public function getRecommendGoods(GoodsService $goods)
    {
        $data = $goods->getRecommendGoods();
    //    return result('','商品全部下架',404);
        return result($data);
    }

    /**
     * @param GoodsService $goods
     * @return \think\response\Json
     * 获取商品详情
     */
    public function getGoodsDetail(GoodsService $goods)
    {
        (new GoodsValidate())->scene('id')->goCheck(Request::param());
        try {
            $data = $goods->getGoodsDetail(Request::param());
        } catch (GoodsException $e) {
            return result('', $e->msg, $e->code);
        }
    //    return result('','商品全部下架',404);
        return result($data);
    }

    /**
     * @param GoodsService $goods
     * @return \think\response\Json
     */
    public function getSearchGoods(GoodsService $goods)
    {
        (new GoodsValidate())->scene('search')->goCheck(Request::param());
        $data = $goods->getSearchGoods(Request::param());
     //   return result('','商品全部下架',404);
        return result($data);
    }

    /**
     * @return \think\response\Json
     * 获取热门搜索的文案
     */
    public function getHotSearch()
    {
        $data = config('jufeel_config.search');
        return result($data);
    }
}