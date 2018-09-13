<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/11
 * Time: 17:40
 */

namespace app\index\model;


use think\Model;

class PartyOrder extends Model
{
    protected $hidden =
        [
            'create_time',
            'update_time'
        ];

    public function party()
    {
        return $this->hasOne('Party', 'id', 'party_id');
    }

    public function user()
    {
        return $this->hasOne('User', 'id', 'user_id');
    }
}