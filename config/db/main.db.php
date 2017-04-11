<?php
/**
 * Created on 2015-11-17
 *
 * b_main的数据库配置
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
		'sys_notify'=>array(  //用户扩展表
				'tbl'   => 't_sys_notify',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				),
		'follow_goods'=>array(  //用户扩展表
				'tbl'   => 't_follow_goods',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				),
		'work_task'=>array(  //用户扩展表
				'tbl'   => 't_work_task',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				),
		'packet_msg'=>array(  //用户扩展表
				'tbl'   => 't_packet_msg',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				),
		'outdisk'=>array(  //用户扩展表
				'tbl'   => 't_outdisk',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				),
		 'disk'=>array(  //用户扩展表
				'tbl'   => 't_disk',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				),

		  'sysset'=>array(  //用户扩展表
				'tbl'   => 't_sysset',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				), 
		 'cash'=>array(  //用户扩展表
				'tbl'   => 't_cash',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				),
		 'refund'=>array(  //用户扩展表
				'tbl'   => 't_refund',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				), 
		   'christmas'=>array(  //此表是双12 活动分享中奖商品活动专用
				'tbl'   => 't_christmas',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				),  
		    'user_extend'=>array(  //用户扩展表
				'tbl'   => 't_user_extend',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				),  
		    'activity_person'=>array(  //此表是双12 活动分享中奖商品活动专用
				'tbl'   => 't_activity_person',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				),
			'agency' => array(
					'tbl'   => 't_agency',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'article' => array(
					'tbl'   => 't_article',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'article_category' => array(
					'tbl'   => 't_article_category',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'fav' => array(
					'tbl'   => 't_fav',
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
			'msg_sys' => array(
					'tbl'   => 't_msg_sys',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'user' => array(
					'tbl'   => 't_user',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'wxuser' => array(
					'tbl'   => 't_wxuser',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
        'qquser' => array(
            'tbl'   => 't_qquser',
            'split' => 'off', // 目前都填off，表示关闭分库分表功能
        ),
        'wbuser' => array(
            'tbl'   => 't_wbuser',
            'split' => 'off', // 目前都填off，表示关闭分库分表功能
        ),
			'address' => array(
					'tbl'   => 't_address',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'expose' => array(
					'tbl'   => 't_expose',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'form' => array(
					'tbl'   => 't_form',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'form_res' => array(
					'tbl'   => 't_form_res',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
	),
);
