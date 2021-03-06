<?php
/**
 * 用户表的后台管理配置文件
 * 
 * 基础定义：
 * [主表] 当前要查询的表，即 base.db_tbl_alias里定义的表
 * [目录表] 主表的目录表，目录表可以是主表本身，例如通常的菜单表，菜单分组和菜单是会放在同一个表里，通过parent id维持之间的树形结构关系。
 * 		但是我们不建议目录结构和叶子节点数据放置在同一个表，因为base.categoryList功能限制了最多从目录表读取1024条数据，叶子节点数据也一起很容易超过1024条从而造成异常；
 * 		如果实在要这么做，可以在表结构里添加一个字段标明哪些是目录数据，并在categoryList的where条件里把这个条件加上。
 * 
 * 系统变量：
 * $tbl: 配置文件名，例如 admin.cfg.php, $tbl=admin。
 * $pkid: 主表的主键的值。
 * 
 * 表定义限制：
 * 1. 必须有int(11)的主键ID，且主键ID不可以用自增长属性，通过定义base.autoidCode来生成主键ID
 * 2. 所有int(11)类型的字段，如果出现在add/edit中，默认值必须是0
 * 
 * @author wangyihuang
 */
return array(
		// 基础信息
		'base' => array(
				'db_cfg_name' => 'admin',
				'db_tbl_alias' => 'app',
				'pkid' => 'appid',
				'autoidCode' => C('AUTOID_APP'),
				'title' => 'APP管理',
				'where' => array(
						'stat' => 0,
				),
				'orderby' => 'ORDER BY appid DESC',
				'pagesize' => 10, // 列表的分页数目
				// 读取列表的时候是否区分appid, false:不区分 true:区分
				'appid_required' => false, 
				
				// 定义菜单焦点
				'nav' => '1',
				'menu_id' => '38',
				
				// 定义预览页面地址，默认地址是：?c=table&a=preview&pkid={$pkid}；如果是其他地址需要自己开发
				'previewUrl' => '?c=table&a=preview&tbl={$tbl}&pkid={$pkid}',
				
				// 如果有添加和编辑的功能呢，以下2个参数不可修改
				'pageAddUrl'  => '?c=table&a=add&tbl={$tbl}', // add模块的type=page时，链接地址
				'pageEditUrl' => '?c=table&a=edit&tbl={$tbl}&pkid={$pkid}', // edit模块的type=page时，链接地址
				
				// [高级功能] 之 表关联定义 start
				'concatList' => array(
						// 这里后面要添加用户表关联
				),
				// 表关联 end
				
				// [高级功能] 之 搜索功能 start
				// 支持最多3级树形目录结构，单字段精准或者模糊匹配
				// 要求：多级目录结构必须是在同一个表里定义，目录结构的树形关系通过parent id标记维系，且最顶级的parent id必须是0。
				'search' => array(			
						'searchKey'   => 'name', // 定义admin表需要被搜索的字段
						'searchSql'   => '%{$kw}%', // 不加%表示精确匹配，哪边加引号就表示在哪边做模糊检索。
						'searchBtn'   => '搜索', // 搜索按钮的名字
						'palceholder' => '按APP名字搜索', // 输入框的palceholder属性						
				),
				// 搜索功能 end
		),
		// 主要是面向用户的APP信息编辑
		'edit' => array(
				'editBtnName' => '编辑',
				'editTitle' => 'APP信息',
				'open'   => 'page', // 操作的类型，modal或者page，modal表示弹出层，page表示采用新的页面。一般带有富文本编辑器，或则插入图片的，采用page。只有add和edit两模块支持page
				'defaultSet' => array(
						'ut'  => 'time', // 调用time()函数
				),
				'inputGroups' => array(
						'appid' => array(
								'name' => 'APPID',
								'type' => 'show',
						),
						'appkey' => array(
								'name' => 'APPKEY',
								'type' => 'show',
						),
						'vip' => array(
								'name' => '客户类型',
								'type' => 'show',
								'format' => 'tpf_app_vipname',
						),
						'name' => array(
								'name' => 'APP名称',
								'type' => 'text',
								'require' => true, // 是否标题前加 *
								'validate' => array(
										'tpv_notnull||APP名称不能为空',
										'tpv_length|1,10||APP长度必须在1-10之间',
								),
						),
						'desc' => array(
								'name' => 'APP描述',
								'type' => 'textarea',
								'placeholder' => '请输入APP描述',
								'tips' => '最长不得超过200个字符',
								'validate' => array(
										'tpv_length|0,200||描述长度不能超过200个字',
								),
						),
						'android_apkname' => array(
								'name' => '安卓包名',
								'type' => 'text',
								'validate' => array(
										'tpv_length|0,40||安卓包名不能超过40个字',
								),
						),
						'ios_bundleid' => array(
								'name' => 'iOS Bundle ID',
								'type' => 'text',
								'validate' => array(
										'tpv_length|0,40||iOS Bundle ID不能超过40个字',
								),
						),
						'wx_appid' => array(
								'name' => '微信appid',
								'type' => 'text',
								'validate' => array(
										'tpv_length|0,60||微信appid不能超过40个字',
								),
						),
						'wx_appsecret' => array(
								'name' => '微信appsecret',
								'type' => 'text',
								'validate' => array(
										'tpv_length|0,60||微信appsecret不能超过40个字',
								),
						),
						'wxmp_appid' => array(
								'name' => '微信公众号appid',
								'type' => 'text',
								'validate' => array(
										'tpv_length|0,60||微信公众号appid不能超过40个字',
								),
						),
						'wxmp_secret' => array(
								'name' => '微信公众号secret',
								'type' => 'text',
								'validate' => array(
										'tpv_length|0,60||微信公众号secret不能超过40个字',
								),
						),
						'qq_appid' => array(
								'name' => 'QQ appid',
								'type' => 'text',
								'validate' => array(
										'tpv_length|0,60||QQ appid不能超过40个字',
								),
						),
						'wb_appid' => array(
								'name' => '微博appid',
								'type' => 'text',
								'validate' => array(
										'tpv_length|0,60||微博appid不能超过40个字',
								),
						),
						'version' => array(
								'name' => '当前版本',
								'type' => 'text',
								'tips' => '格式：v1.0',
								'require' => true, // 是否标题前加 *
								'validate' => array(
										'tpv_notnull||当前版本不能为空',
								),
						),
						'android_update' => array(
								'name' => '安卓更新',
								'type' => 'radio',
								'tips' => '设置安卓用户安装版本低于当前版本的更新策略',
								'options' => array(
										array(
												'text' => '不更新',
												'value' => '0',
										),
										array(
												'text' => '强制更新',
												'value' => '1',
										)
								),
						),
						'ios_update' => array(
								'name' => 'iOS更新',
								'type' => 'radio',
								'tips' => '设置苹果用户安装版本低于当前版本的更新策略',
								'options' => array(
										array(
												'text' => '不更新',
												'value' => '0',
										),
										array(
												'text' => '强制更新',
												'value' => '1',
										)
								),
						),
						'use_open' => array(
								'name' => '第三方登录',
								'type' => 'checkbox',
								'options' => array(
										array(
												'text' => 'QQ',
												'value' => 'qq',
										),
										array(
												'text' => '微信',
												'value' => 'wx',
										),
										array(
												'text' => '微博',
												'value' => 'wb',
										)
								),
						),
						'bgcolor' => array(
								'name' => 'APP色调',
								'type' => 'color',
						),
						'icon' => array(
								'name' => 'APP图标',
								'type' => 'pic',
								'tips' => '120*120的png图片，否则不能正常打包',
								'attr' => array(
										'width' => '120',
										//'height' => '120',
								),
						),
						'date_start' => array(
								'name' => '有效开始日期',
								'type' => 'show',
						),
						'date_overdue' => array(
								'name' => '过期日期',
								'type' => 'show',
						),
				),
				'submitBtn' => array(
						'name' => '保存'
				)
		),
);
