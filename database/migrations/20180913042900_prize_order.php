<?php

use think\migration\Migrator;
use think\migration\db\Column;

class PrizeOrder extends Migrator
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
        $table = $this->table('prize_order',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('prize_id',   'integer')  //商品id
            ->addColumn('user_id',    'integer')  //管理员id
            ->addColumn('state',      'integer',array('default'=>'0')) //0未中奖,1中奖
            ->addColumn('create_time','integer')
            ->addColumn('update_time','integer')
            ->addForeignKey('prize_id','prize','id')
            ->addForeignKey('user_id', 'user', 'id')
            ->create();
    }
}
