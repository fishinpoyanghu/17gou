<?php
/**
 * Created on 2015-12-29
 *
 * b_shop的数据库配置
 *
 * tbl_list 分库分表参数split说明：
 * 1. 为no时，库名会自动补上"_0", 表名不变
 * 2. 为off时，库名则不自动补上"_0"，完全保持原样
 * 3. 为define时，表示由用户自定义
 * 4. 为数组时，表示系统默认分库分表规则，格式必须是: array(库的个数，每个库包含的表的个数)。HASH!
 *
 */

return array(
	'db' => '17gou',

	'w_server' => array('192.168.0.2', '17gou', 'nfjkernksdferniui'),
	
	'r_server' => array(
		array('192.168.0.2', '17gou', 'nfjkernksdferniui'),
	),

	'tbl_list' => array(

		'activity' => array(
			'tbl'   => 't_activity',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'activity_num' => array(
			'tbl'   => 't_activity_num',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'goods' => array(
			'tbl'   => 't_goods',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'goods_attr' => array(
			'tbl'   => 't_goods_attr',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'goods_type' => array(
			'tbl'   => 't_goods_type',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'hot_goods' => array(
			'tbl'   => 't_hot_goods',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'lucky_num' => array(
			'tbl'   => 't_lucky_num',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'operate_activity' => array(
			'tbl'   => 't_operate_activity',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'show' => array(
			'tbl'   => 't_show',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'show_zan' => array(
			'tbl'   => 't_show_zan',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'show_comment' => array(
			'tbl'   => 't_show_comment',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'type_attr' => array(
			'tbl'   => 't_type_attr',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'activity_user' => array(
			'tbl'   => 't_activity_user',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'rebate' => array(
			'tbl'   => 't_rebate',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'logistics' => array(
			'tbl'   => 't_logistics',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'packet' => array(
			'tbl'   => 't_packet',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'order' => array(
			'tbl'   => 't_order',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'task' => array(
			'tbl'   => 't_task',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'signal' => array(
			'tbl'   => 't_signal',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'invite_count' => array(
			'tbl'   => 't_invite_count',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'point' => array(
			'tbl'   => 't_point',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'point_detail' => array(
			'tbl'   => 't_point_detail',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'money' => array(
			'tbl'   => 't_money',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'banner' => array(
			'tbl'   => 't_banner',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
        'pcbanner' => array(
            'tbl'   => 't_pcbanner',
            'split' => 'off', // 目前都填off，表示关闭分库分表功能
        ),
		'point_rule' => array(
			'tbl'   => 't_point_rule',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'sign' => array(
			'tbl'   => 't_sign',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'fenxiao' => array(
			'tbl'   => 't_fenxiao',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'lottery' => array(
			'tbl'   => 't_lottery',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'lottery_record' => array(
			'tbl'   => 't_lottery_record',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'red' => array(
			'tbl'   => 't_red',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'red_activity' => array(
			'tbl'   => 't_red_activity',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'red_user' => array(
			'tbl'   => 't_red_user',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'red_order' => array(
			'tbl'   => 't_red_order',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		't_false' => array(
			'tbl'   => 't_false',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'fen_xiao' => array(
			'tbl'   => 't_fenxiao',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'user' => array(
			'tbl'   => 't_user',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'msg_sys' => array(
			'tbl'   => 't_msg_sys',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'msg' => array(
			'tbl'   => 't_msg',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'msg_notify' => array(
			'tbl'   => 't_msg_notify',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'cash' => array(
			'tbl'   => 't_cash',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'num' => array(
	            'tbl'   => 't_num',
	            'split' => 'off', // 目前都填off，表示关闭分库分表功能
        ),
        'shua' => array(
            'tbl'   => 't_shua',
            'split' => 'off', // 目前都填off，表示关闭分库分表功能
        ),
        'yijian' => array(
            'tbl'   => 't_yijian',
            'split' => 'off', // 目前都填off，表示关闭分库分表功能
        ),
        'notice' => array(
            'tbl'   => 't_notice',
            'split' => 'off', // 目前都填off，表示关闭分库分表功能
        ),
	),
);
