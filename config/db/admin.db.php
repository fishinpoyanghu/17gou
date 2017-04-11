<?php
/**
 * Created on 2011-6-27
 *
 * b_admin的数据库配置
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
		
			'admin' => array(
					'tbl'   => 't_admin',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'auth' => array(
					'tbl'   => 't_auth',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'group' => array(
					'tbl'   => 't_group',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'department' => array(
					'tbl'   => 't_department',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'menu' => array(
					'tbl'   => 't_menu',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'tpl' => array(
					'tbl'   => 't_tpl',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'tpl_category' => array(
					'tbl'   => 't_tpl_category',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'app_tpl_category' => array(
					'tbl'   => 't_app_tpl_category',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'app' => array(
					'tbl'   => 't_app',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'app_page' => array(
					'tbl'   => 't_app_page',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'app_nav' => array(
					'tbl'   => 't_app_nav',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'app_loading' => array(
					'tbl'   => 't_app_loading',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'icon_font' => array(
					'tbl'   => 't_icon_font',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'pic' => array(
					'tbl'   => 't_pic',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'piclib' => array(
					'tbl'   => 't_piclib',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'pic_category' => array(
					'tbl'   => 't_pic_category',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'pic_material' => array(
					'tbl'   => 't_pic_material',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
			'pic_app_nav' => array(
					'tbl'   => 't_pic_app_nav',
					'split' => 'off', // 目前都填off，表示关闭分库分表功能
			),
        'notice' => array(
            'tbl'   => 't_notice',
            'split' => 'off', // 目前都填off，表示关闭分库分表功能
        ),
	),
);
