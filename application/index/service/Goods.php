<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:02
 */

namespace app\index\service;

use app\index\model\Goods as GoodsModel;
use app\lib\exception\GoodsException;

class Goods
{
    /**
     * @param $data
     * @return array|\PDOStatement|string|\think\Collection
     * 获取所有商品
     */
    public function getAllGoods($data)
    {
        //获取全部商品
        if ($data['category'] == 0) {
            $goods = GoodsModel::with('category')
                ->where('stock', '>', 0)
                ->order('create_time desc')
                ->field('id,name,pic_url,price,sale_price,category_id')
                ->select();
        }
        //获取分类下的商品
        else {
            $goods = GoodsModel::with('category')
                ->where('stock', '>', 0)
                ->where('category_id', $data['category'])
                ->field('id,name,pic_url,price,sale_price,category_id')
                ->order('create_time desc')
                ->select();
        }
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
            ->field('name,pic_url,price,sale_price,category_id')
            ->order('create_time desc')
            ->select();
        return $goods;
    }
}