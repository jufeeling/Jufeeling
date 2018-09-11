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
    public function change()
    {
        $table = $this->table('Coupon',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('user_id',        'integer') //商品id
            ->addColumn('receipt_user',   'string')
            ->addColumn('receipt_address','string')
            ->addColumn('receipt_phone',  'integer')
            ->addForeignKey('user_id', 'user', 'id',['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->create();
    }
}
