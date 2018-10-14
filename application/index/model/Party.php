<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/10
 * Time: 18:55
 */

namespace app\index\model;

use think\Model;

class Party extends Model
{
    protected $hidden =
        [
            'create_time',
            'update_time',
            'state',
            'status'
        ];

    /**
     * @return \think\model\relation\HasMany
     * 参与者
     */
    public function participants()
    {
        return $this->hasMany('PartyOrder', 'party_id', 'id');
    }

    /**
     * @return \think\model\relation\HasMany
     * 留言
     */
    public function message()
    {
        return $this->hasMany('Message', 'party_id', 'id');
    }

    /**
     * @return \think\model\relation\HasOne
     * 用户
     */
    public function user()
    {
        return $this->hasOne('User', 'id', 'user_id');
    }

    public function orderId(){
        return $this->hasMany('OrderId', 'party_id', 'id');
    }

}