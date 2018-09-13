<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/11
 * Time: 17:49
 */

namespace app\index\model;


use think\Model;

class Message extends Model
{
    /**
     * @return \think\model\relation\HasOne
     * 用户
     */
    public function user()
    {
        return $this->hasOne('User', 'id', 'user_id');
    }
}