<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/18
 * Time: 15:35
 */

namespace app\index\model;


use think\Model;

class ShoppingCart extends Model
{
    public function goods(){
        return $this->belongsTo('Goods','goods_id','id');
    }
}