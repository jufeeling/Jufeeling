<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 16:03
 */

namespace app\index\model;


use think\Model;

class Goods extends Model
{
    protected $hidden =
        [
            'create_time',
            'update_time'
        ];

    public function category(){
        return $this->belongsTo('GoodsCategory','category_id','id');
    }
}