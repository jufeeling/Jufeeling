<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Sales extends Migrator
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
        $table = $this->table('Sales',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('goods_id',   'integer') //商品id
            ->addColumn('sale_price', 'decimal') //折扣价格
            ->addColumn('start_time', 'integer') //开始时间
            ->addColumn('end_time',   'integer') //结束时间
            ->addForeignKey('goods_id',   'goods', 'id',['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->create();
    }
}
