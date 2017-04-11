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
    'db' => 'agent',

    'w_server' => array('192.168.0.2', 'agent', 'nfjkernksdferniui'),
	
	'r_server' => array(
		array('192.168.0.2', 'agent', 'nfjkernksdferniui'),
	),
	'tbl_list' => array(    
			'user' => array(
					'tbl'   => 't_user',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			), 
	),
);
