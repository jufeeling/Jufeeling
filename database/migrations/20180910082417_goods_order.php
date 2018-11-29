<?php

use think\migration\Migrator;
use think\migration\db\Column;

class GoodsOrder extends Migrator
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
        $table = $this->table('goods_order',array('engine'=>'MyISAM'));
        $table
            ->addIndex(array('id',), array('unique' => true))
            ->addColumn('user_id',         'integer') //用户id
            ->addColumn('order_id',        'string')  //订单id
            ->addColumn('tracking_id',     'string',array('default' => 0)) //快递单号
            ->addColumn('tracking_company','string',array('default' => '0'))
            ->addColumn('prepay_id',       'string')  //微信支付id
            ->addColumn('price',           'decimal') //原价
            ->addColumn('sale_price',      'decimal') //支付价格
            ->addColumn('sale',            'decimal') //折扣
            ->addColumn('carriage',        'integer') //运费
            ->addColumn('receipt_id',      'integer') //收货地址id
            ->addColumn('coupon_id',       'integer') //购物券i
            ->addColumn('receipt_name',    'string')  //收货人
            ->addColumn('receipt_address', 'string')  //收获地址
            ->addColumn('receipt_phone',   'string')  //收获手机号码
            ->addColumn('sign',            'integer',array('default'=> 0)) //标记用户是否签收 0未发货1已发货2已签收
            ->addColumn('state',           'integer',array('default'=> 0)) //标记用户是否删除此订单,0未删除1删除
            ->addColumn('status',          'integer',array('default'=> 0)) //标记订单是否已被支付 0未支付1支付2取消
            ->addColumn('isDeleteAdmin',   'integer',array('default'=> 0)) //标记是否被后台删除 0未删除1删除
            ->addColumn('create_time',     'integer')
            ->addColumn('update_time',     'integer')
            ->create();
    }
}
