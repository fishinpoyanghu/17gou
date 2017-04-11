<?php
/**
 * Created on 2010-7-8
 *
 * 框架公用配置文件
 * 
 * @author wangyihuang
 */

return array(
	
		// 定义版本号，用于JS/CSS
		'VERSION_JS'           => '20150905',
		'VERSION_CSS'          => '20150905',
		'VERSION_IMG'          => '20150916',
		
		// AUTO ID
	    'AUTOID_USER'          => 100, /// 用户ID
	    'AUTOID_MSG'           => 102, /// 收件箱消息ID
	    'AUTOID_MSG_DETAIL'    => 103, /// 私信消息ID
	    'AUTOID_WXUSER'        => 104,//wx用户ID
    'AUTOID_QQUSER'        => 105,//qq用户ID
    'AUTOID_WBUSER'        => 114,//qq用户ID
	    'AUTOID_CLIENTLOGIN'        => 106,//CLENTLOGIN ID
	    'AUTOID_SCENE'          =>107,
	    'AUTOID_SCENE_PAGE'     =>108,
	    'AUTOID_IMG'            =>109,
	    'AUTOID_IMG_CATEGORY'   =>110,
	    'AUTOID_MUSIC'          =>111,
	    'AUTOID_MUSIC_CATEGORY' =>112,
	    'AUTOID_BBSREPORT'      =>113,
		
		// act
		'AUTOID_A_ACT'         => 600,
		'AUTOID_A_ACT_LOG'     => 601,
		'AUTOID_A_PRIZE'       => 602,
		'AUTOID_A_CLASS'       => 603,
		'AUTOID_A_TPL'         => 604,
	
		// admin app
		'AUTOID_ADMIN'         => 900, // 管理员表
		'AUTOID_ADMIN_GROUP'   => 901, // t_group，管理员所属分组表
		'AUTOID_ADMIN_MENU'    => 902, // t_menu, 后台菜单表
		'AUTOID_PIC'           => 903,
		'AUTOID_PICLIB'        => 916,
		'AUTOID_PIC_CATEGORY'  => 904,
		'AUTOID_PIC_MATERIAL'  => 905,
		'AUTOID_PIC_APP_NAV'   => 906,
		'AUTOID_APP'           => 907,
		'AUTOID_APP_TPL_CATEGORY' => 914,
		'AUTOID_DEPARTMENT'    => 908,
		'AUTOID_APPNAV'        => 909,
		'AUTOID_ICONFONT'      => 910,
		'AUTOID_APPLOADING'    => 911,
		'AUTOID_TPL'           => 912,
		'AUTOID_TPL_CATEGORY'  => 915,
		'AUTOID_APP_PAGE'      => 913,
			
		// b_main
		'AUTOID_M_ARTICLE'          => 801,
		'AUTOID_M_ARTICLE_CATEGORY' => 802,
		'AUTOID_M_MSG'              => 804,
		'AUTOID_M_MSG_NOTIFY'       => 805,
		'AUTOID_M_MSG_SYS'          => 806,
		'AUTOID_M_USER'             => 808,
    'AUTOID_M_WXUSER'           => 810,
    'AUTOID_M_QQUSER'           => 814,
    'AUTOID_M_WBUSER'           => 815,
		'AUTOID_M_EXPOSE'           => 809,
		'AUTOID_M_FORM'             => 811,
		'AUTOID_M_FORM_RES'         => 812,
		'AUTOID_M_ADDRESS'          => 813,
		'AUTOID_M_TEAMWAR'=>816,//百团大战 拼团id
		// bbs
		'AUTOID_POST' 		=> 2001,
	    'AUTOID_REPLY'		=> 2002,
	    'AUTOID_ZAN'		=> 2006,
	    'AUTOID_FAV'		=> 2007,
		'AUTOID_BBS_HOME'   => 2010,
	    'AUTOID_IPOST'		=> 2004,
	    'AUTOID_UPOST'		=> 2005,
		'AUTOID_CLASS'      => 2009,
	    'TARGET_TYPE'		=> 1,
		
		// shop
		'AUTOID_SHOP_ACTIVITY'         => 301,
		'AUTOID_SHOP_ACTIVITY_NUM'     => 302,
		'AUTOID_SHOP_ACTIVITY_USER'    => 312,
		'AUTOID_SHOP_GOODS'            => 303,
		'AUTOID_SHOP_GOODS_ATTR'       => 304,
		'AUTOID_SHOP_GOODS_TYPE'       => 305,
		'AUTOID_SHOP_HOT_GOODS'        => 306,
		'AUTOID_SHOP_LUCKY_NUM'        => 307,
		'AUTOID_SHOP_OPERATE_ACTIVITY' => 308,
		'AUTOID_SHOP_SHOW'             => 309,
		'AUTOID_SHOP_TYPE_ATTR'        => 310,
		'AUTOID_M_REBATE'              => 311,
		'AUTOID_SHOP_TEAMGOODS'=>313,
		// 特殊，专门用来生成惟一文件名
		'AUTOID_FILENAME'      => 9999,
		
		// 网站的通用配置
		'SITE_DOMAIN'                  => 'http://'.$GLOBALS['SYSTEM_CFG']['SITE_LIST'][RUN_MOD].'',
	
	    'STATIC_DOMAIN'                => 'http://'.$GLOBALS['SYSTEM_CFG']['STATIC_LIST'][RUN_MOD],
		'CSS_DOMAIN'                   => 'http://'.$GLOBALS['SYSTEM_CFG']['STATIC_LIST'][RUN_MOD].'/css',
		'JS_DOMAIN'                    => 'http://'.$GLOBALS['SYSTEM_CFG']['STATIC_LIST'][RUN_MOD].'/js',
		'IMG_DOMAIN'                   => 'http://'.$GLOBALS['SYSTEM_CFG']['STATIC_LIST'][RUN_MOD].'/images',
	@	'API_DOMAIN'                   => 'http://'.$GLOBALS['SYSTEM_CFG']['API_LIST'][RUN_MOD].'/',
	    'DATA_DOMAIN'                  => 'http://'.$GLOBALS['SYSTEM_CFG']['STATIC_LIST'][RUN_MOD].'/data',
		'EDITOR_DOMAIN'                => 'http://'.$GLOBALS['SYSTEM_CFG']['STATIC_LIST'][RUN_MOD].'/editor',
		'UPLOAD_DOMAIN'                => isset($GLOBALS['SYSTEM_CFG']['UPLOAD_LIST']) ? 'http://'.$GLOBALS['SYSTEM_CFG']['UPLOAD_LIST'][RUN_MOD].'/' : '',
	    'QR_DOMAIN'                    => isset($GLOBALS['SYSTEM_CFG']['QR_LIST']) ? 'http://'.$GLOBALS['SYSTEM_CFG']['QR_LIST'][RUN_MOD].'/':'',
	
		// Cookie相关的配置
		'COOKIE_PREFIX'                => $GLOBALS['SYSTEM_CFG']['COOKIE_PREFIX'][RUN_MOD], // 规定Cookie变量前缀
		'COOKIE_EXPIRE'                => '', // Cookie有效期，不定义表示cookie浏览器关闭过期
		'COOKIE_DOMAIN'                => '.'.$GLOBALS['SYSTEM_CFG']['COOKIE_DOMAIN'][RUN_MOD], // Cookie有效域名
		'COOKIE_PATH'                  => '/', // Cookie路径
		'COOKIE_KEY'                   => 'daysgogogo', // Cookie加密的key
		
		// 定义异常视图文件
		'APP_EXCEPTION_FILE'           => CORE_ROOT.'/include/exception.view.php',
		
		// 定义跟踪视图文件
		'APP_TRACE_FILE'               => CORE_ROOT.'/include/trace.view.php',
		
		// 日志记录相关参数定义
		'LOG_RECORD'                   => false,   // 默认不记录日志
		'LOG_RECORD_SIZE'              => 67108864, // 日志文件大小限制，64MB
		'LOG_RECORD_LEVEL'             => 'EMERG,ALERT,CRIT,ERR', // 允许记录的日志级别
		
		// 是否显示错误消息，正式发布时不显示
		'SHOW_ERROR_MSG'               => (RUN_MOD == 'deploy') ? false : true,
		'ERROR_PAGE'                   => '',
		'ERROR_MESSAGE'                => '你浏览的页面暂时发生了错误！请稍后再试～',
);
