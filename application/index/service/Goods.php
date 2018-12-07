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
use think\facade\Cache;

class Goods
{
    /**
     * @param $data
     * @return array|\PDOStatement|string|\think\Collection
     * 获取所有商品
     */
    public function getAllGoods($data)
    {
        $cache_name = 'goods_all' . $data['category'];
        $cacheValue = Cache::get($cache_name);
        if($cacheValue)
        {
            return $cacheValue;
        }
        if ($data['category'] == 0) {
            $val = [['state', '=', 0], ['stock', '>', 0]];
        } else {
            $val = [['stock', '>', 0], ['state', '=', 0], ['category_id', '=', $data['category']]];
        }
        $goods['data'] = GoodsModel::with('category')
                                   ->with('label')
                                   ->where($val)
                                   ->order('create_time desc')
                                   ->field('id,name,thu_url,price,sale_price,category_id')
                                   ->select();
        foreach ($goods['data'] as $item)
        {
            $item['name'] = html_entity_decode($item['name']);
        }
        //判断缓存中是否有goods
        //如果有则返回,没有则得到数据、存缓存并返回数据
        if($data['category'] == 0)
        {
            return $goods;
        }
        $condition = config('jufeel_config.goods_condition');
        //获取全部商品
        $goods['condition'] = $condition[$data['category']];
        Cache::set($cache_name,$goods,7200);
        return $goods;
    }

    /**
     * @param $data
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function conditionGoods($data)
    {
        //拿到筛选条件
        //拿到排序条件
        //查询
        $val = $this->getScreeningCondition($data['category'], $data['value']);
        switch ($data['sort']) {
            case 0:
                $order = ['create_time desc'];
                break;
            case 1:
                $order = ['sale_price asc'];
                break;
            case 2:
                $order = ['sale_price desc'];
                break;
        }
        $goods['data'] = GoodsModel::with('category')
            ->with('label')
            ->where('stock', '>', 0)
            ->where('state', 0)
            ->where('category_id',$data['category'])
            ->where($val)
            ->field('id,name,thu_url,price,sale_price,category_id,degrees')
            ->order($order)
            ->select();
        foreach ($goods['data'] as $item)
        {
            $item['name'] = html_entity_decode($item['name']);
        }
        return $goods;
    }

    /**
     * @param $category
     * @param $value
     * @return array
     * 获取筛选条件的值
     */
    private function getScreeningCondition($category, $value)
    {
        //根据商品分类得到不同的筛选条件
        //确定筛选条件后返回
        //将筛选条件为0的剔除数组
        switch ($category) {
            case 1:
                $data = ['brand' => $value[1], 'degrees' => html_entity_decode($value[2])];
                break;
            case 2:
                $data = ['type' => $value[1], 'degrees' => html_entity_decode($value[2])];
                break;
            case 3:
                $data = ['type' => $value[1], 'specifications' => $value[2]];
                break;
            case 4:
                $data = ['type' => $value[1], 'flavor' => $value[2]];
                break;
        }
        //将不符合条件的剔除
        $data['country'] = $value[0];
        $result = array_diff($data, [0]);
        return $result;
    }

    /**
     * @return array|\PDOStatement|string|\think\Collection
     * 获取推荐商品
     */
    public function getRecommendGoods()
    {
        //判断缓存中是否存在recommendGoods
        //如果没有则查询并存缓存
        $cacheGoods = Cache::get('recommend');
        if ($cacheGoods) {
            return $cacheGoods;
        }
        $goods = RecommendModel::with(['goods' => function ($query) {
            $query->with('label')
                  ->field('id,name,thu_url,price,sale_price,category_id')
                  ->with('category');
        }])
            ->field('goods_id')
            ->order('create_time desc')
            ->select();
        foreach ($goods as $item)
        {
            $item['goods']['name'] = html_entity_decode($item['goods']['name']);
        }
        Cache::set('recommend', $goods, 7200);
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
        $cache_name = 'goods_detail' . $data['id'];

        $cacheValue = Cache::get($cache_name);
        if($cacheValue)
        {
            return $cacheValue;
        }
        $field = [
            'id',
            'name',
            'carriage',
            'category_id',
            'price',
            'sale_price',
            'thu_url',
            'cov_url',
            'det_url',
            'delivery_place',
            'state'
        ];
        //获取商品详情
        $goodsDetail = GoodsModel::with('category')
            ->with('images')
            ->with('label')
            ->field($field)
            ->find($data['id']);
        $goodsDetail['name'] = html_entity_decode($goodsDetail['name']);
        Cache::set($cache_name,$goodsDetail,7200);
        return $goodsDetail;
    }

    /**
     * @param $data
     * @return array|\PDOStatement|string|\think\Collection
     * 获取搜索内容
     */
    public function getSearchGoods($data)
    {
        $goods = GoodsModel::with('category')
                           ->with('label')
                           ->where('stock', '>', 0)
                           ->where('name', 'like', '%' . $data['content'] . '%')
                           ->where('state', 0)
                           ->field('name,thu_url,price,sale_price,category_id,id')
                           ->order('create_time desc')
                           ->select();
        return $goods;
    }

}