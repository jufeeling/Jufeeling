<?php

use think\migration\Migrator;
use think\migration\db\Column;

class PartyOrder extends Migrator
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
        $table = $this->table('party_order',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('user_id',     'integer') //用户id
            ->addColumn('party_id',    'integer') //聚会id
            ->addColumn('status',      'integer',array('default' => 0)) //标记用户是否删除派对记录 0未删除1删除
            ->addColumn('create_time', 'integer')
            ->addColumn('update_time', 'integer')
            ->create();
    }
}
