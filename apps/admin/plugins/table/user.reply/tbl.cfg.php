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
				'db_cfg_name' => 'main',
				'db_tbl_alias' => 'reply',
				'pkid' => 'uid',
				'autoidCode' => C('AUTOID_USER'),
				'title' => '用户管理',
				'where' => array(
						'stat' => 0,
				),
				'orderby' => 'ORDER BY uid DESC',
				'pagesize' => 13, // 列表的分页数目
				// 读取列表的时候是否区分appid, false:不区分 true:区分
				'appid_required' => false, 
				
				// 定义菜单焦点
				'nav' => '1',
				'menu_id' => '20',
				
				// 定义预览页面地址，默认地址是：?c=table&a=preview&pkid={$pkid}；如果是其他地址需要自己开发
				//'previewUrl' => '?c=table&a=preview&tbl={$tbl}&pkid={$pkid}',
				
				// 如果有添加和编辑的功能呢，以下2个参数不可修改
				'pageAddUrl'  => '?c=table&a=add&tbl={$tbl}', // add模块的type=page时，链接地址
				'pageEditUrl' => '?c=table&a=edit&tbl={$tbl}&pkid={$pkid}', // edit模块的type=page时，链接地址
				
				// [高级功能] 之 表关联定义 start
				'concatList' => array(
				),
				// 表关联 end
				
				// [高级功能] 之 搜索功能 start
				'search' => array(		
						'searchKey'   => 'nick', // 定义admin表需要被搜索的字段
						'searchSql'   => '%{$kw}%', // 不加%表示精确匹配，哪边加引号就表示在哪边做模糊检索。
						'searchBtn'   => '搜索', // 搜索按钮的名字
						'palceholder' => '按昵称搜索', // 输入框的palceholder属性
				),
				// 搜索功能 end
		),
		// 列表显示配置
		'list' => array(
				// 第一个字段必须是主键
				'uid' => array(
						'name' => 'ID',
				),
				'icon' => array(
						'name' => '头像',
				),
				'name' => array(
						'name' => '帐号',
				),
				'nick' => array(
						'name' => '昵称',
				),
				'type' => array(
						'name' => '用户类型',
				),
				'rt' => array(
						'name' => '注册时间',
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
		// 编辑配置，没有编辑功能的话，就把edit去掉
		'edit' => array(
				'editBtnName' => '编辑',
				'editTitle' => '编辑用户',
				'open'   => 'page', // 操作的类型，modal或者page，modal表示弹出层，page表示采用新的页面。一般带有富文本编辑器，或则插入图片的，采用page。只有add和edit两模块支持page
				'defaultSet' => array(
						'ut'  => 'time', // 调用time()函数
				),
				'inputGroups' => array(
						'name' => array(
								'name' => '用户名',
								'type' => 'show', // 当type=show时，只是展示此字段，这个时候可以定义format，不能定义validate
								//'format' => 'tpf_date',
						),
						'nick' => array(
								'name' => '昵称',
								'type' => 'text',
								'tips' => '1-10个汉字或字母数字', // 提示文字，就text和password支持
								'validate' => array(
										'tpv_length|0,10||昵称不合法',
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
				'multiBtnName' => '<i class="icon-ban-circle"></i> 批量封禁',
				'del_key' => 'stat',
				'del_value' => 1,
				'confirmMsg' => '确定要封禁该用户？',
				'confirmMsgMulti' => '确定要批量封禁这些用户？',
				'defaultSet' => array(
						'ut'  => 'time', // 调用time()函数
				),
		),
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
