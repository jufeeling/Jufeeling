<?php

use think\migration\Migrator;
use think\migration\db\Column;

class GoodsOrder extends Migrator
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
    public function change()
    {
        $table = $this->table('goods_order',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('user_id',         'integer') //用户id
            ->addColumn('order_id',        'integer') //订单id
            ->addColumn('prepay_id',       'string')  //微信支付id
            ->addColumn('price',           'decimal') //价格
            ->addColumn('receipt_name',    'string')  //收货人
            ->addColumn('receipt_address', 'string')  //收获地址
            ->addColumn('receipt_phone',   'integer') //收获手机号码
            ->addColumn('state',           'integer') //标记用户是否删除此订单,0未删除1删除
            ->addColumn('create_time',     'integer')
            ->addColumn('update_time',     'integer')
            ->addForeignKey('user_id', 'user', 'id',['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->create();
    }
}
