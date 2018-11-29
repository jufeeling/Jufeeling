<?php

use think\migration\Migrator;
use think\migration\db\Column;

class DeliveryAddress extends Migrator
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
        $table = $this->table('delivery_address',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('user_id',        'integer') //商品id
            ->addColumn('receipt_name',   'string')  //收货人
            ->addColumn('receipt_area',   'string')  //收获地区
            ->addColumn('receipt_address','string')  //收获详细地址
            ->addColumn('receipt_phone',  'string') //收获电话号码
            ->addColumn('label',          'integer',array('default'=> 1))  //标签(学校,公司)
            ->addColumn('state',          'integer') //标记是否为默认收货地址 0是1不是
            ->create();
    }
}
