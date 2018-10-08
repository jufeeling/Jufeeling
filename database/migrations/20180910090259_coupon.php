<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Coupon extends Migrator
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
    //购物券
    public function change()
    {
        $table = $this->table('coupon',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('name',       'string')
            ->addColumn('rule',       'decimal') //规则
            ->addColumn('sale',       'decimal') //折扣价格
            ->addColumn('category',   'integer',array('default' => 0)) //折扣商品分类 默认全部商品
            ->addColumn('count',      'integer') //数量
            ->addColumn('state',      'integer') //状态 0可以领取1不能领取
            ->addColumn('start_time', 'integer') //开始时间
            ->addColumn('end_time',   'integer') //结束时间
            ->create();
    }
}
