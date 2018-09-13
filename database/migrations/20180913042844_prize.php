<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Prize extends Migrator
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
        $table = $this->table('prize',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('goods_id',       'integer')  //商品id
            ->addColumn('admin_id',       'integer')  //管理员id
            ->addColumn('open_prize_time','integer')  //开奖时间
            ->addColumn('state',          'integer',array('default'=>'0')) //0还可以参与抽奖,1抽奖结束
            ->addColumn('create_time',    'integer')
            ->addColumn('update_time',    'integer')
            ->addForeignKey('goods_id','goods','id',['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->addForeignKey('admin_id','admin','id',['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
            ->create();
    }
}
