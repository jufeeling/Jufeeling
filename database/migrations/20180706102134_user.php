<?php

use think\migration\Migrator;

class User extends Migrator
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
        $table = $this->table('user',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('openid',     'string')   //微信id
            ->addColumn('nickname',   'string')   //昵称
            ->addColumn('avatar',     'string')   //头像
            ->addColumn('state',      'integer',array('default' => 0)) //0为新用户,1为老用户
            ->addColumn('create_time','integer')
            ->addColumn('update_time','integer')
            ->create();
    }
}
