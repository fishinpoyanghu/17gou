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
				'db_tbl_alias' => 'menu',
				'pkid' => 'menu_id',
				'autoidCode' => C('AUTOID_ADMIN_MENU'),
				'title' => '导航菜单管理',
				'where' => array(
						'pid' => 0,
						'stat' => 0,
				),
				'orderby' => 'ORDER BY pid ASC, weight DESC, menu_id DESC',
				'pagesize' => 10, // 列表的分页数目
				// 读取列表的时候是否区分appid, false:不区分 true:区分
				'appid_required' => false, 
				
				// 定义菜单焦点
				'nav' => '52',
				'menu_id' => '25',
				
				// 定义预览页面地址，默认地址是：?c=table&a=preview&pkid={$pkid}；如果是其他地址需要自己开发
				'previewUrl' => '?c=table&a=preview&tbl={$tbl}&pkid={$pkid}',
				
				// 如果有添加和编辑的功能呢，以下2个参数不可修改
				'pageAddUrl'  => '?c=table&a=add&tbl={$tbl}', // add模块的type=page时，链接地址
				'pageEditUrl' => '?c=table&a=edit&tbl={$tbl}&pkid={$pkid}', // edit模块的type=page时，链接地址
				
				// [高级功能] 之 表关联定义 start
				'concatList' => array(						
				),
				// 表关联 end
				
				// [高级功能] 之 搜索功能 start
				'search' => array(		
						'searchKey'   => 'name', // 定义主表需要被搜索的字段
						'searchSql'   => '%{$kw}%', // 不加%表示精确匹配，哪边加引号就表示在哪边做模糊检索。
						'searchBtn'   => '搜索', // 搜索按钮的名字
						'palceholder' => '按菜单名搜索', // 输入框的palceholder属性
				),
				// 搜索功能 end
		),
		// 列表显示配置
		'list' => array(
				// 第一个字段必须是主键
				'menu_id' => array(
						'name' => '菜单ID',
				),
				'name' => array(
						'name' => '菜单名',
				),
				'url' => array(
						'name' => '链接地址',
				),
				'weight' => array(
						'name' => '权重'
				),
				'rt'   => array(
						'name' => '创建时间',
						'format' => 'tpf_date',
				),
				'_ops' => array(
						'name' => '操作', // _ops表示操作菜单
						'cnt' => array(
								'edit||<i class="icon-pencil gray-dark" title="编辑"></i>', // <a href="?c=table&a=edit&{$pkid_name}={$pkid}&{$default}">编辑</a>', // 如果是链接{$default}是必须的，系统会根据这个自动加上表相关的信息
								'delete||<i class="icon-trash gray" title="删除"></i>', // '<a data-id="{$pkid}" class="js-delete">封禁</a>',
						)
					),
		),
		// 创建配置，没有创建功能的话，就把add去掉
		'add' => array(
				'addBtnName' => '+添加菜单',
				'addTitle' => '添加菜单',
				'open'   => 'page', // 操作的类型，modal或者page，modal表示弹出层，page表示采用新的页面。一般带有富文本编辑器，或则插入图片的，采用page。只有add和edit两模块支持page
				'defaultSet' => array(
						'pid' => 0,
						'iscat' => 1,
						'rt'  => 'time', // 调用time()函数
						'ut'  => 'time',
				),
				'inputGroups' => array(
						'name' => array(
								'name' => '菜单名',
								'type' => 'text',
								'require' => true, // 是否标题前加 *
								'validate' => array(
										'tpv_notnull||菜单名不能为空',
										'tpv_length|2,10||菜单名长度必须在2-10之间',
								),
						),
						'url' => array(
								'name' => '链接地址',
								'type' => 'text',
								'require' => false, // 是否标题前加 *
								'validate' => array(
										'tpv_length|0,200||菜单名长度不能超过200字符',
								),
						),
						'hide_header' => array(
								'name' => '是否隐藏',
								'type' => 'radio',
								'require' => true, // 是否标题前加 *
								'options' => array(
										array(
												'text' => '否',
												'value' => '0',
										),
										array(
												'text' => '是',
												'value' => '1',
										)
								),
								'validate' => array(
										'tpv_notnull||必须选择是否隐藏顶部导航',
								),
						),
						'weight' => array(
								'name' => '权重',
								'tips' => '整数,值越大越靠前',
								'type' => 'text',
								'require' => true, // 是否标题前加 *
								'validate' => array(
										'tpv_notnull||请填写权重数值',
										'tpv_numeric||权重必须是>=0的整数',
								),
						),
				),
				'submitBtn' => array(
						'name' => '提交'
				)
		),

		// 编辑配置，没有编辑功能的话，就把edit去掉
		'edit' => array(
				'editBtnName' => '编辑',
				'editTitle' => '编辑管理员',
				'open'   => 'page', // 操作的类型，modal或者page，modal表示弹出层，page表示采用新的页面。一般带有富文本编辑器，或则插入图片的，采用page。只有add和edit两模块支持page
				'defaultSet' => array(
						'ut'  => 'time', // 调用time()函数
				),
				'inputGroups' => array(
						'name' => array(
								'name' => '菜单名',
								'type' => 'text',
								'require' => true, // 是否标题前加 *
								'validate' => array(
										'tpv_notnull||菜单名不能为空',
										'tpv_length|2,10||菜单名长度必须在2-10之间',
								),
						),
						'url' => array(
								'name' => '链接地址',
								'type' => 'text',
								'require' => false, // 是否标题前加 *
								'validate' => array(
										'tpv_length|0,200||菜单名长度不能超过200字符',
								),
						),
						'hide_header' => array(
								'name' => '是否隐藏',
								'type' => 'radio',
								'require' => true, // 是否标题前加 *
								'options' => array(
										array(
												'text' => '否',
												'value' => '0',
										),
										array(
												'text' => '是',
												'value' => '1',
										)
								),
								'validate' => array(
										'tpv_notnull||必须选择是否隐藏顶部导航',
								),
						),
						'weight' => array(
								'name' => '权重',
								'tips' => '整数,值越大越靠前',
								'type' => 'text',
								'require' => true, // 是否标题前加 *
								'validate' => array(
										'tpv_notnull||请填写权重数值',
										'tpv_numeric||权重必须是>=0的整数',
								),
						),
				),
				'submitBtn' => array(
						'name' => '保存'
				)
		),
		// 删除配置，没有删除功能的话，就把delete去掉
		// 本配置只支持标记删除，就是在表里有个字段，设置它的值=多少表示删除
		'delete' => array(
				'multiBtnName' => '<i class="icon-trash"></i> 批量删除',
				'del_key' => 'stat',
				'del_value' => 1,
				'confirmMsg' => '确定要删除此导航菜单？',
				'confirmMsgMulti' => '确定要批量删除导航菜单？',
				'defaultSet' => array(
						'ut'  => 'time', // 调用time()函数
				),
		),
);
