<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 13:50
 */

namespace app\index\model;


use think\Model;

class Prize extends Model
{
    /**
     * @return \think\model\relation\HasOne
     * 关联商品
     */
    public function goods(){
        return $this->hasOne('goods','id','goods_id');
    }

    /**
     * @return \think\model\relation\HasMany
     * 关联抽奖订单
     */
    public function orders(){
        return $this->hasMany('prize_order','prize_id','id');
    }
}