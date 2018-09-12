<?php

use think\migration\Migrator;
use think\migration\db\Column;

class UserCoupon extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    //用户购物券
    public function change()
    {
        $table = $this->table('user_coupon',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('user_id',   'integer') //商品id
            ->addColumn('coupon_id', 'integer') //优惠券id
            ->addColumn('state',     'integer') //0未使用,1已使用
            ->addColumn('start_time', 'integer') //开始时间
            ->addColumn('end_time',   'integer') //结束时间
            ->addForeignKey('user_id',   'user',  'id',['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->addForeignKey('coupon_id', 'coupon', 'id',['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->create();
    }
}
