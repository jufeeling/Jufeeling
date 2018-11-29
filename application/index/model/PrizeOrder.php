<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/13
 * Time: 13:47
 */

namespace app\index\model;


use think\Model;

class PrizeOrder extends Model
{
    public function user(){
        return $this->hasOne('user','id','user_id');
    }
}