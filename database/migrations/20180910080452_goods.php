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
            ->addColumn('goods_id',            'string')  //商品编码
            ->addColumn('name',                'string')  //商品名
            ->addColumn('category_id',         'integer') //分类id
            ->addColumn('stock',               'integer') //库存
            ->addColumn('notice',              'string')  //须知
            ->addColumn('carriage',            'integer',array('default'=> 0)) //运费
            ->addColumn('recommend_reason',    'string')  //推荐理由
            ->addColumn('channels',            'string')  //购买渠道
            ->addColumn('purchase_address',    'string')  //购买地址
            ->addColumn('shop',                'string')  //商家名
            ->addColumn('delivery_place' ,     'string')  //发货地
            ->addColumn('logistics_standard' , 'integer') //物流标准(进货运费)

            ->addColumn('purchase_price' ,     'decimal') //采购价
            ->addColumn('cost_price' ,         'decimal') //成本价
            ->addColumn('reference_price' ,    'decimal') //参考价
            ->addColumn('price',               'decimal') //定价
            ->addColumn('sale_price',          'decimal') //折扣价格
                //条件
            ->addColumn('country',             'string')  //国家
            ->addColumn('brand',               'string')  //品牌
            ->addColumn('degrees',             'string')  //度数
            ->addColumn('type',                'string')  //种类
            ->addColumn('specifications',      'string')  //规格
            ->addColumn('flavor',              'string')  //口味

            ->addColumn('thu_url',             'string')  //缩略图
            ->addColumn('cov_url',             'string')  //封面图
            ->addColumn('det_url',             'string')  //详情图
            ->addColumn('share_url',           'string')  //分享图

            ->addColumn('state',               'integer',array('default'=> 0)) //状态 标记是否上架或下架 0上架1下架
            ->addColumn('create_time',         'integer')
            ->addColumn('update_time',         'integer')
            ->create();
    }
}
