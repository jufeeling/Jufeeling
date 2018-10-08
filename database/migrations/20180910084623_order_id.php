'<?php

use think\migration\Migrator;
use think\migration\db\Column;

class OrderId extends Migrator
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
        $table = $this->table('order_id',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('order_id',    'integer') //订单id
            ->addColumn('goods_id',    'integer') //商品id
            ->addColumn('user_id',     'integer') //数量
            ->addColumn('select',      'integer') //是否已使用 0未使用1使用
            ->addColumn('price',       'decimal') //购买时的价格
            ->addColumn('count',       'integer') //单个商品买的个数
            ->addColumn('create_time', 'integer')
            ->addColumn('update_time', 'integer')
            ->addForeignKey('goods_id', 'goods', 'id')
            ->addForeignKey('order_id', 'goods_order', 'id')
            ->addForeignKey('user_id',  'user', 'id')
            ->create();
    }
}
