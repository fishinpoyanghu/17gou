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
 * 语法定义：
 * xxx|yyy,zzz||ppp：xxx是函数名；xxx函数会在除了当前操作字段外，传入yyy和zzz变量（多个变量以,分隔，之间不允许有空格，分隔符是|)；ppp是描述文字，用于函数返回的提示或者其他用途（记住分隔符是||)
 * 
 * @author wangyihuang
 */
return array(
		// 基础信息
		'base' => array(
				'db_cfg_name' => 'admin',
				'db_tbl_alias' => 'admin',
				'pkid' => 'admin_id',
				'autoidCode' => C('AUTOID_ADMIN'),
				'title' => '管理员管理',
				'where' => array(
						'stat' => 0,
				),
				'orderby' => 'ORDER BY admin_id DESC',
				'pagesize' => 13, // 列表的分页数目
				// 读取列表的时候是否区分appid, false:不区分 true:区分
				'appid_required' => false, 
				
				// 定义菜单焦点
				'nav' => '16',
				'menu_id' => '6',
				
				// 定义预览页面地址，默认地址是：?c=table&a=preview&pkid={$pkid}；如果是其他地址需要自己开发
				'previewUrl' => '?c=table&a=preview&tbl={$tbl}&pkid={$pkid}',
				
				// 如果有添加和编辑的功能呢，以下2个参数不可修改
				'pageAddUrl'  => '?c=table&a=add&tbl={$tbl}', // add模块的type=page时，链接地址
				'pageEditUrl' => '?c=table&a=edit&tbl={$tbl}&pkid={$pkid}', // edit模块的type=page时，链接地址
				
				// [高级功能] 之 表关联定义 start
				'concatList' => array(
						array(
								'db_cfg_name'  => 'admin',
								'db_tbl_alias' => 'group',
								// 主表的字段xxx(由下面concatid定义)外键关联到目录表的主键字段yyy(由下面pkid定义)
								'pkid' => 'group_id',
								'concatid' => 'group_id',
								// 定义目录表哪些字段需要concat到主表。
								'keys' => array(
										// aaa=>bbb，表示：目录表的字段名aaa连接到主表后的字段名是bbb
										'name' => 'group_name',
// 										'pid'  => 'group_pid',
								)
						),
						// 多个表关联在这里继续添加定义
				),
				// 表关联 end
				
				// [高级功能] 之 搜索功能 start
				'search' => array(		
						'searchKey'   => 'name', // 定义admin表需要被搜索的字段
						'searchSql'   => '%{$kw}%', // 不加%表示精确匹配，哪边加引号就表示在哪边做模糊检索。
						'searchBtn'   => '搜索', // 搜索按钮的名字
						'palceholder' => '按用户名搜索', // 输入框的palceholder属性
				),
				// 搜索功能 end
		),
		// 列表显示配置
		'list' => array(
				// 第一个字段必须是主键
				'admin_id' => array(
						'name' => 'ID',
				),
				'name' => array(
						'name' => '帐号',
				),
				'realname' => array(
						'name' => '真实姓名',
				),
				'mobile' => array(
						'name' => '手机',
				),
				'weixin' => array(
						'name' => '微信',
				),
				'qq' => array(
						'name' => 'QQ',
				),
				'last_login' => array(
						'name' => '最后登录',
						'format' => 'tpf_date',
				),
				'_ops' => array(
						'name' => '操 作', // _ops表示操作菜单
						'cnt' => array(
								'edit||<i class="icon-pencil gray-dark" title="编辑"></i>', // <a href="?c=table&a=edit&{$pkid_name}={$pkid}&{$default}">编辑</a>', // 如果是链接{$default}是必须的，系统会根据这个自动加上表相关的信息
								'reset||<i class="icon-key gray-dark" title="重置密码"></i>',
								'delete||<i class="icon-ban-circle gray" title="封禁"></i>', // '<a data-id="{$pkid}" class="js-delete">封禁</a>',
						)
					),
		),
		// 创建配置，没有创建功能的话，就把add去掉
		'add' => array(
				'addBtnName' => '添加员工 <i class="icon-plus"></i>',
				'addTitle' => '添加员工',
				'open'   => 'modal', // 操作的类型，modal或者page，modal表示弹出层，page表示采用新的页面。一般带有富文本编辑器，或则插入图片的，采用page。只有add和edit两模块支持page
				'defaultSet' => array(
						'rt'  => 'time', // 调用time()函数
						'ut'  => 'time',
						'ip'  => 'get_ip|1',
				),
				'inputGroups' => array(
						'name' => array(
								'name' => '用户名',
								'type' => 'text',
								'require' => true, // 是否标题前加 * 
								'tips' => '长度6-20', // 提示文字，就text和password支持
								'validate' => array(
										'tpv_notnull||用户名不能为空',
										'tpv_length|6,20||用户名长度必须在6-20之间',
										'tpv_uname||用户名只能是字母、数字或者下划线',
										'tpc_do_unique||用户名已存在', // 默认函数，唯一性检查
								),
						),
						'password' => array(
								'name' => '密码',
								'type' => 'password',
								'require' => true, // 是否标题前加 * 
								'tips' => '密码必须是字母、数字或者下划线', // 提示文字，就text和password支持
								'validate' => array(
										'tpv_length|6,20||密码长度必须在6-20之间',
										'tpv_uname||密码只能是字母、数字或者下划线'
								),
						),
						'department_id' => array(
								'name' => '部门',
								'type' => 'select',
								'require' => true,
								'tips' => '选择所属部门',
								'options' => array(
// 										array(
// 												'text' => '请选择部门',
// 												'value' => '0',
// 										),
								),
								// options_source定义从数据库读取options，会array_merge到上面的options选项里
								'options_source' => array(
										'db_cfg_name'  => 'admin',
										'db_tbl_alias' => 'department',
										'pkid'         => 'department_id',
										'parentId'     => 'pid', // 如果没有定义parentId，表示只有一级的目录结构
										'nodeName'     => 'name', // 定义每一层树形结构的存储名称的字段名
										// 定义读取全category的where条件
										'where' => array(
												'company_id' => '{$company_id}',
												'stat' => 0,
										),
								)
						),
						/*
						'group_id' => array(
								'name' => '权限组',
								'require' => true, // 是否标题前加 * 
								// 特殊类型，表示目录分组，需要从目录去读取信息。
								// 重要：当配置此类型时，base里一定要配置categoryList的信息。
								// 重要：当配置此类型时，不需要配置validate，系统会根据require字段来决定是否检测要求最低一层叶子节点必选。
								'type' => 'category',
						),
						*/
						'realname' => array(
								'name' => '真实姓名',
								'type' => 'text',
								'tips' => '尊姓大名', // 提示文字，就text和password支持
								'validate' => array(
										'tpv_length|0,20||真实姓名不合法',
								),
						),
// 						'realname' => array(
// 								'name' => '真实姓名',
// 								'type' => 'pic',
// 								'attr' => array(
// 										'width' => '120',
// 										//'height' => '120',
// 								),
// 								'validate' => array(
// 										'tpv_length|0,20||真实姓名不合法',
// 								),
// 						),
						'mobile' => array(
								'name' => '手机',
								'type' => 'text',
								'validate' => array(
										'tpv_mobile|1||手机号码不合法',
								),
						),
						'email' => array(
								'name' => '邮箱',
								'type' => 'text',
								'validate' => array(
										'tpv_email|1||邮箱不合法',
								),
						),
						'desc' => array(
								'name' => '备注',
								'type' => 'textarea',
								'validate' => array(
										'tpv_length|0,200||长度不能超过100个字',
								),
						),
// 						// 目前最多只支持配置一个editor
// 						'desc' => array(
// 								'name' => '备注',
// 								'type' => 'editor',
// 								'placeholder' => '请输入备注',
// 								'validate' => array(
// 										'tpv_length|0,200||长度不能超过100个字',
// 								),
// 						),
						
// 						'stat' => array(
// 								'name' => '状态',
// 								'type' => 'select',
// 								'options' => array(
// 										array(
// 												'text' => '正常',
// 												'value' => '0',
// 										),
// 										array(
// 												'text' => '封号',
// 												'value' => '1',
// 										)
// 								),
// 								'validate' => array(
// 										'tpv_notnull||状态至少要选1个'
// 								),
// 						),
// 						'stat2' => array(
// 								'name' => '状态',
// 								'type' => 'radio',
// 								'options' => array(
// 										array(
// 												'text' => '正常',
// 												'value' => '0',
// 										),
// 										array(
// 												'text' => '封号',
// 												'value' => '1',
// 										)
// 								),
// 								'validate' => array(
// 										'tpv_notnull||状态至少要选1个'
// 								),
// 						),
// 						'stat' => array(
// 								'name' => '状态',
// 								'type' => 'checkbox',
// 								'options' => array(
// 										array(
// 												'text' => '正常',
// 												'value' => '0',
// 										),
// 										array(
// 												'text' => '封号',
// 												'value' => '1',
// 										)
// 								),
// 								'validate' => array(
// 										'tpv_notnull||状态至少要选2个'
// 								),
// 						),
				),
				'submitBtn' => array(
						'name' => '提交'
				)
		),
		// 编辑配置，没有编辑功能的话，就把edit去掉
		'edit' => array(
				'editBtnName' => '编辑',
				'editTitle' => '编辑管理员',
				'open'   => 'modal', // 操作的类型，modal或者page，modal表示弹出层，page表示采用新的页面。一般带有富文本编辑器，或则插入图片的，采用page。只有add和edit两模块支持page
				'defaultSet' => array(
						'ut'  => 'time', // 调用time()函数
				),
				'inputGroups' => array(
						'name' => array(
								'name' => '用户名',
								'type' => 'show', // 当type=show时，只是展示此字段，这个时候可以定义format，不能定义validate
								//'format' => 'tpf_date',
						),
						'department_id' => array(
								'name' => '部门',
								'type' => 'select',
								'require' => true,
								'tips' => '选择所属部门',
								'options' => array(
								),
								// options_source定义从数据库读取options，会array_merge到上面的options选项里
								'options_source' => array(
										'db_cfg_name'  => 'admin',
										'db_tbl_alias' => 'department',
										'pkid'         => 'department_id',
										'parentId'     => 'pid', // 如果没有定义parentId，表示只有一级的目录结构
										'nodeName'     => 'name', // 定义每一层树形结构的存储名称的字段名
										// 定义读取全category的where条件
										'where' => array(
												'company_id' => '{$company_id}',
												'stat' => 0,
										),
								)
						),
						/*
						'group_id' => array(
								'name' => '权限组',
								'require' => true, // 是否标题前加 * 
								// 特殊类型，表示目录分组，需要从目录去读取信息。
								// 重要：当配置此类型时，base里一定要配置categoryList的信息。
								// 重要：当配置此类型时，不需要配置validate，系统会根据require字段来决定是否检测要求最低一层叶子节点必选。
								'type' => 'category',
						),
						*/
						'realname' => array(
								'name' => '真实姓名',
								'type' => 'text',
								'tips' => '尊姓大名', // 提示文字，就text和password支持
								'validate' => array(
										'tpv_length|0,20||真实姓名不合法',
								),
						),
// 						'realname' => array(
// 								'name' => '真实姓名',
// 								'type' => 'pic',
// 								'attr' => array(
// 										"width" => '120',
// 										//'height' => '120',
// 								),
// 								'validate' => array(
// 										'tpv_length|0,120||真实姓名不合法',
// 								),
// 						),
						'mobile' => array(
								'name' => '手机',
								'type' => 'text',
								'validate' => array(
										'tpv_mobile|1||手机号码不合法',
								),
						),
						'email' => array(
								'name' => '邮箱',
								'type' => 'text',
								'validate' => array(
										'tpv_email|1||邮箱不合法',
								),
						),
// 						'stat' => array(
// 								'name' => '状态',
// 								'type' => 'select',  // radio/checkbox/select
// 								'options' => array(
// 										array(
// 												'text' => '正常',
// 												'value' => '0',
// 										),
// 										array(
// 												'text' => '封号',
// 												'value' => '1',
// 										)
// 								),
// 								'validate' => array(
// 										'tpv_notnull||状态至少要选2个'
// 								),
// 						),
						'desc' => array(
								'name' => '备注',
								'type' => 'textarea',
								'validate' => array(
										'tpv_length|0,200||长度不能超过100个字',
								),
						),
						/*
						// 目前最多只支持配置一个editor
						'desc' => array(
								'name' => '备注',
								'type' => 'editor',
								'validate' => array(
										'tpv_length|0,200||长度不能超过100个字',
								),
						),
						*/
						/*
						'ip' => array(
								'name' => '注册IP',
								'type' => 'show',
								'format' => 'tpf_ip',
						),
						'last_ip' => array(
								'name' => '最后登录IP',
								'type' => 'show',
								'format' => 'tpf_ip',
						),
						'last_login' => array(
								'name' => '最后登录时间',
								'type' => 'show',
								'format' => 'tpf_date',
						),
						*/
				),
				'submitBtn' => array(
						'name' => '保存'
				)
		),
		// 删除配置，没有删除功能的话，就把delete去掉
		// 本配置只支持标记删除，就是在表里有个字段，设置它的值=多少表示删除
		'delete' => array(
				'multiBtnName' => '<i class="icon-ban-circle"></i> 批量封禁',
				'del_key' => 'stat',
				'del_value' => 1,
				'confirmMsg' => '确定要封禁该管理员？',
				'confirmMsgMulti' => '确定要批量封禁这些管理员？',
				'defaultSet' => array(
						'ut'  => 'time', // 调用time()函数
				),
		),
		/*
		// 修改分类，没有修改分类功能的话，就把classify去掉
		'classify' => array(
				'multiBtnName' => '<i class="icon-folder-close-alt"></i> 修改分类',
				'name' => '权限组', // 分类的字段描述
				'categoryId' => 'group_id', // 定义存储分类的字段
				'require' => true,
				'defaultSet' => array(
						'ut'  => 'time', // 调用time()函数
				),
		),
		*/
		// 重置密码配置，没有重置密码功能的话，就把password去掉
		'reset' => array(
				'name' => '重置',
				'key'  => 'password', // 保存密码的字段
				'type' => 'random',  // random:直接重置成随机密码，并告知管理员，目前只支持random
				'defaultSet' => array(
						'ut'  => 'time', // 调用time()函数
				),
		),
);
