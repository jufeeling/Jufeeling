<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:02
 */

namespace app\index\service;

use app\index\model\Goods as GoodsModel;
use app\index\model\Recommend as RecommendModel;
use app\lib\exception\GoodsException;

class Goods
{
    private $condition = array();
    /**
     * @param $data
     * @return array|\PDOStatement|string|\think\Collection
     * 获取所有商品
     */
    public function getAllGoods($data)
    {
        $condition = config('jufeel_config.goods_condition');
        //获取全部商品
        if ($data['category'] == 0) {
            $goods['data'] = GoodsModel::with('category')
                ->where('stock', '>', 0)
                ->where('state',0)
                ->order('create_time desc')
                ->field('id,name,thu_url,price,sale_price,category_id')
                ->select();
        } //获取分类下的商品
        else {
            $goods = GoodsModel::with('category')
                ->where('stock', '>', 0)
                ->where('state',0)
                ->where('category_id', $data['category'])
                ->field('id,name,thu_url,price,sale_price,category_id')
                ->order('create_time desc')
                ->select();
        }
        $goods['condition'] = $condition[$data['category']];
        return $goods;
    }


    /**
     * @param $data
     * @return array|\PDOStatement|string|\think\Collection
     * 发现BUG
     */
    public function conditionGoods($data){
        $condition_value = config('jufeel_config.goods_condition_value');
        $this->condition['name'] = $condition_value[1]['name'][$data['condition'][0]];
        $this->condition['description'] = $condition_value[1]['description'][$data['condition'][1]];
        $this->condition['price'] = $condition_value[1]['price'][$data['condition'][2]];
        $goods = GoodsModel::with('category')
            ->where('stock', '>', 0)
            ->where('state',0)
            ->where($this->condition)
            ->field('id,name,thu_url,price,sale_price,category_id')
            ->order('create_time desc')
            ->select();
        return $goods;
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取推荐商品
     */
    public function getRecommendGoods(){
        $goods = RecommendModel::with(['goods'=>function($query){
            $query->where('stock','>',0)
                ->where('state',0)
                ->field('id,name,thu_url,price,sale_price,category_id')
                ->with('category');
        }])
            ->select();
        return $goods;
    }

    /**
     * @param $data
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws GoodsException
     * 获取商品详情
     */
    public function getGoodsDetail($data)
    {
        //获取商品详情
        $goods = GoodsModel::with('category')
            ->where('id', $data['id'])
            ->find();
        if ($goods) {
            return $goods;
        }
        throw new GoodsException();
    }

    /**
     * @param $data
     * @return array|\PDOStatement|string|\think\Collection
     * 获取搜索内容
     */
    public function getSearchGoods($data)
    {
        $goods = GoodsModel::with('category')
            ->where('stock', '>', 0)
            ->where('name|description', 'like', '%' . $data['content'] . '%')
            ->where('state',0)
            ->field('name,thu_url,price,sale_price,category_id')
            ->order('create_time desc')
            ->select();
        return $goods;
    }
}