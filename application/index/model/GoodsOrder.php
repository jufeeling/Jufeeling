<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 15:41
 */

namespace app\index\model;


use think\Model;

class GoodsOrder extends Model
{
    public function goods(){
        return $this->hasMany('OrderId','order_id','id');
    }
}