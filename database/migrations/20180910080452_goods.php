<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Goods extends Migrator
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
        $table = $this->table('goods',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('name',        'string')  //商品名
            ->addColumn('category_id', 'integer') //分类id
            ->addColumn('price',       'decimal') //价格
            ->addColumn('sale_price',  'decimal') //折扣价格
            ->addColumn('stock',       'integer') //库存
            ->addColumn('description', 'string')  //描述
            ->addColumn('notice',      'string')  //须知
            ->addColumn('carriage',    'decimal') //运费
            ->addColumn('thu_url',     'string')  //缩略图
            ->addColumn('cov_url',     'string')  //封面图
            ->addColumn('det_url',     'string')  //详情图
            ->addColumn('shop_id',     'integer') //商家id
            ->addColumn('state',       'integer',array('default'=> 0)) //状态 标记是否上架或下架 0上架1下架
            ->addColumn('create_time', 'integer')
            ->addColumn('update_time', 'integer')
            ->create();
    }
}
