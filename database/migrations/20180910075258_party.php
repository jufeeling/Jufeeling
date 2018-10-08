<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Party extends Migrator
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
        $table = $this->table('party',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('user_id',     'integer')  //用户id
            ->addColumn('image',       'string')   //封面图
            ->addColumn('description', 'string')   //聚说
            ->addColumn('way',         'integer',array('default'=>'0')) //方式
            ->addColumn('people_no',   'integer')  //人数
            ->addColumn('remaining_people_no','integer') //剩余可报名人数
            ->addColumn('date',        'string')   //日期
            ->addColumn('time',        'string')   //时间
            ->addColumn('site',        'string')   //地点
            ->addColumn('create_time', 'integer')
            ->addColumn('update_time', 'integer')
            ->addColumn('status',      'integer',array('default'=>'0'))  //标记派对是否被删除 0未删除
            ->addColumn('state',       'integer',array('default'=>'0'))  //标记派对是否已过期 0未过期
            ->addForeignKey('user_id', 'user', 'id')
            ->create();
    }
}
