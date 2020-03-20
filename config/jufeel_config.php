<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 12:27
 */
return [

    //虚拟头像

    'avatar' =>
    [
        0  => 'https://oss.joofeel.com/avatar/0.jpg',
        1  => 'https://oss.joofeel.com/avatar/1.jpg',
        2  => 'https://oss.joofeel.com/avatar/2.jpg',
        3  => 'https://oss.joofeel.com/avatar/3.jpg',
        4  => 'https://oss.joofeel.com/avatar/4.jpg',
        5  => 'https://oss.joofeel.com/avatar/5.jpg',
        6  => 'https://oss.joofeel.com/avatar/6.jpg',
        7  => 'https://oss.joofeel.com/avatar/7.jpg',
        8  => 'https://oss.joofeel.com/avatar/8.jpg',
        9  => 'https://oss.joofeel.com/avatar/9.jpg',
        10 => 'https://oss.joofeel.com/avatar/10.jpg',
        11 => 'https://oss.joofeel.com/avatar/11.jpg',
        12 => 'https://oss.joofeel.com/avatar/12.jpg',
        13 => 'https://oss.joofeel.com/avatar/13.jpg',
        14 => 'https://oss.joofeel.com/avatar/14.jpg',
        15 => 'https://oss.joofeel.com/avatar/15.jpg',
        16 => 'https://oss.joofeel.com/avatar/16.jpg',
        17 => 'https://oss.joofeel.com/avatar/17.jpg',
        18 => 'https://oss.joofeel.com/avatar/18.jpg',
        19 => 'https://oss.joofeel.com/avatar/19.jpg',
        20 => 'https://oss.joofeel.com/avatar/20.jpg',
        21 => 'https://oss.joofeel.com/avatar/21.jpg',
        22 => 'https://oss.joofeel.com/avatar/22.jpg',
        23 => 'https://oss.joofeel.com/avatar/23.jpg',
        24 => 'https://oss.joofeel.com/avatar/24.jpg',
        25 => 'https://oss.joofeel.com/avatar/25.jpg',
        26 => 'https://oss.joofeel.com/avatar/26.jpg',
        27 => 'https://oss.joofeel.com/avatar/27.jpg',
        28 => 'https://oss.joofeel.com/avatar/28.jpg',
        29 => 'https://oss.joofeel.com/avatar/29.jpg',
        30 => 'https://oss.joofeel.com/avatar/30.jpg',
        31 => 'https://oss.joofeel.com/avatar/31.jpg',
        32 => 'https://oss.joofeel.com/avatar/32.jpg',
        33 => 'https://oss.joofeel.com/avatar/33.jpg',
        34 => 'https://oss.joofeel.com/avatar/34.jpg',
        35 => 'https://oss.joofeel.com/avatar/35.jpg',
        36 => 'https://oss.joofeel.com/avatar/36.jpg',
    ],

    //收货地址标签
    'label' =>
        [
            0 => '其他',
            1 => '家',
            2 => '公司',
            3 => '学校'
        ],

    //商品类别
    'goods_category' =>
        [
            0 => '所有宝贝',
            1 => '全球精酿',
            2 => '低度轻饮',
            3 => '花式饮品',
            4 => '美味零食'
        ],

    //微信支付回调地址
    'redirect_notify' => 'jufeel.jufeeling.com/pay/notify',

    //商品筛选条件
    'goods_condition' =>
        [

            1 =>
                [
                    0 => ['默认','比利时','美国','英国','法国','冰岛','墨西哥','新西兰','意大利','挪威'],
                    1 => ['默认','粉象','乐蔓','角鲨头','左手','美人鱼','红砖','罗格','酿酒狗','1664','冰岛无双','科罗娜','大蜥蜴','维奥拉','碉堡','裸岛','西丽','迷失海岸'],    //品牌
                    2 => ['默认','低于4.5°','4.5°-7.5°','大于7.5°'],   //度数
                    'name' => ['国家','品牌','度数']
                ],
            2 =>
                [
                    0 => ['默认','中国','瑞典','丹麦','西班牙','比利时','德国','意大利','英国'],
                    1 => ['默认','西打酒','果味啤酒','起泡酒','葡萄酒','梅子酒'],           //种类
                    2 => ['默认','5°以下','5°-8°','大于8°'],   //度数
                    'name' => ['国家','种类','度数']
                ],
            3 =>
                [
                    0 => ['默认','中国','日本','澳大利亚','韩国','泰国','印尼','土耳其','马来西亚'],
                    1 => ['默认','汽水','果汁','其他'],     //种类
                    2 => ['默认','175ml','180ml','190ml','200ml','250ml','290ml','300ml','330ml','350ml','375ml','380ml','400ml','450ml','485ml','500ml',''],  //规格
                    'name' => ['国家','种类','规格']
                ],
            4 =>
                [
                    0 => ['默认','中国','日本','俄罗斯','英国','韩国','印尼','西班牙','泰国'],
                    1 => ['默认','糕点','饼干','果仁','糖果','其他'],  //种类
                    2 => ['默认','松软','香脆','Q弹','绵柔','嚼劲'],   //口味
                    'name' => ['国家','种类','口感']
                ],

        ],

    'oss_config' => [
       'accessKeyId'   => '',
       'accessKeySecret'  => '',
       'endpoint'    => '',
       'bucket' => '',
    ],

    //热门搜索内容
    'search' => [
        '苹果教皇','LeftHand','DogfishHead','RedBrick','白梨西打'
    ]

];
