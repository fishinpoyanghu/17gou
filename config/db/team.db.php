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
	  'team_goods_type' => array(
			'tbl'   => 't_team_goods_type',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
	   ),
	  'collect' => array(
			'tbl'   => 't_collect',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		), 
	  'team_task' => array(
			'tbl'   => 't_team_task',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		), 
	   'team_lucky_num' => array(
			'tbl'   => 't_team_lucky_num',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
 
		'team_activity_num' => array(
			'tbl'   => 't_team_activity_num',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),		
		'team_num' => array(
			'tbl'   => 't_team_num',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'team_order' => array(
			'tbl'   => 't_team_order',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'team_activity' => array(
			'tbl'   => 't_team_activity',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
        'team_goods' => array(
			'tbl'   => 't_team_goods',
			'split' => 'off', // 目前都填off，表示关闭分库分表功能
		),
		'team_member'=>array(  //百团大战团员
				'tbl'   => 't_team_member',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
				), 
	    'teamwar'=>array(  //百团大战
				'tbl'   => 't_teamwar',
			 	'split' => 'off', // 目前都填off，表示关闭分库分表功能
	 	),
		 
	),
);
