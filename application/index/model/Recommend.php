<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/28
 * Time: 14:59
 */

namespace app\index\model;


use think\Model;

class Recommend extends Model
{

    public function goods(){
        return $this->hasOne('Goods','id','goods_id');
    }
}