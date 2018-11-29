<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/26
 * Time: 14:45
 */

namespace app\index\model;


use think\Model;

class PartyGoods extends Model
{
    public function goods()
    {
        return $this->belongsTo('Goods', 'goods_id', 'id');
    }
}