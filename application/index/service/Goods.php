<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:02
 */

namespace app\index\service;

use app\index\model\Goods as GoodsModel;

class Goods
{
    public function getAllGoods(){
        $goods = GoodsModel::with('category')
            ->order('create_time desc')
            ->select();
        return $goods;
    }
}