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
				'db_cfg_name' => 'core',
				'db_tbl_alias' => 'scene',
				'pkid' => 'sceneid',
				'autoidCode' => C('AUTOID_SCENE'),
				'title' => '场景管理',
				'where' => array(
						'is_tpl' => 0,
                        'dt'=>'is  null'
				),
				'orderby' => 'ORDER BY sceneid DESC',
				'pagesize' => 10, // 列表的分页数目
				// 读取列表的时候是否区分appid, false:不区分 true:区分
				'appid_required' => false, 
				
				// 定义菜单焦点
				'nav' => '1',
				'menu_id' => '36',
				
				// 定义预览页面地址，默认地址是：?c=table&a=preview&pkid={$pkid}；如果是其他地址需要自己开发
				'previewUrl' => '?c=table&a=preview&tbl={$tbl}&pkid={$pkid}',
				
				// 如果有添加和编辑的功能呢，以下2个参数不可修改
				'pageAddUrl'  => '?c=table&a=add&tbl={$tbl}', // add模块的type=page时，链接地址
				'pageEditUrl' => '?c=table&a=edit&tbl={$tbl}&pkid={$pkid}', // edit模块的type=page时，链接地址
				
				// [高级功能] 之 表关联定义 start
				/*'concatList' => array(
						array(
								'db_cfg_name'  => 'bbs',
								'db_tbl_alias' => 'class',
								// 主表的字段xxx(由下面concatid定义)外键关联到目录表的主键字段yyy(由下面pkid定义)
								'pkid' => 'class_id',
								'concatid' => 'class_id',
								// 定义目录表哪些字段需要concat到主表。
								'keys' => array(
										// aaa=>bbb，表示：目录表的字段名aaa连接到主表后的字段名是bbb
										'class_name' => 'class_name',
// 										'pid'  => 'group_pid',
								)
						),
						// 多个表关联在这里继续添加定义
				),*/
				// 表关联 end
				
				// [高级功能] 之 搜索功能 start
				// 支持最多3级树形目录结构，单字段精准或者模糊匹配
				// 要求：多级目录结构必须是在同一个表里定义，目录结构的树形关系通过parent id标记维系，且最顶级的parent id必须是0。
				
				// 如果表有分类目录，且在add/edit/search里需要用到的话，需要定义category_list
				// 重要:分类目录要求只最后一层叶子节点可以作为其他表的外键关联。
				// 重要：目录表的总数据条目要求不能太大，1000条以下为佳，超过的话，请自行开发或者优化本功能。

				'search' => array(
						'useCategory' => false, // 搜索栏是否包含目录选择 true/false
						'searchKey'   => 'scenename', // 定义admin表需要被搜索的字段
						'searchSql'   => '%{$kw}%', // 不加%表示精确匹配，哪边加引号就表示在哪边做模糊检索。
						'searchBtn'   => '搜索', // 搜索按钮的名字
						'searchCid'   => 'scenename', // 定义主表需要检索的目录ID的字段名称，如果未定义，则不进行目录检索
						'palceholder' => '按案例名称搜索', // 输入框的palceholder属性
						
				),
				// 搜索功能 end
		),
		// 列表显示配置
		'list' => array(
				// 第一个字段必须是主键
				'sceneid' => array(
						'name' => 'ID',
				),
				'userid' => array(
						'name' => '用户id',
				),
				'scenename' => array(
						'name' => '场景名称',
				),
				'description' => array(
						'name' => '描述',
				),
                'clickcount'   => array(
                        'name' => '点击数',
                ),
                'thumbnail'   => array(
                        'name' => '缩略图',
                        'type'=>'show',
                        'format' => 'tf_pic_showpic|120',
                ),
                'public_time'  => array(
                        'name' => '发布时间',
                        'format'=>"tpf_date"
                ),
                'from_sceneid'  => array(
                        'name' => '源案例',
                ),
                'is_tpl'   => array(
                        'name' => '案例',
                        'format' => 'tf_judgeIcon',
                ),
                'is_publish'   => array(
                        'name' => '发布',
                        'format' => 'tf_judgeIcon',
                ),
                'is_show'   => array(
                        'name' => '展示',
                        'format' => 'tf_judgeIcon',
                ),
                'is_vertify'   => array(
                        'name' => '审核',
                        'format' => 'tf_judgeIcon',
                ),
				'rt' =>array(
					    'name'=>'创建时间',
					    'format'=>'tpf_date',
				),
                'ut' =>array(
                        'name'=>'更新时间',
                        'format'=>'tpf_date',
                ),
                'dt' =>array(
                        'name'=>'删除时间',
                        'format'=>'tpf_date',
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
				'addBtnName' => '+添加案例',
				'addTitle' => '添加案例',
				'open'   => 'modal', // 操作的类型，modal或者page，modal表示弹出层，page表示采用新的页面。一般带有富文本编辑器，或则插入图片的，采用page。只有add和edit两模块支持page
				'defaultSet' => array(
						'rt'  => 'time', // 调用time()函数
						'ut'  => 'time',
                        'public_time'=>'time',
                        'is_tpl'=>'intval|0',
                        'is_publish'=>'intval|1',
                        'is_show'=>'intval|1',
                        'is_vertify'=>'intval|1'
				),
				'inputGroups' => array(
						'scenename' => array(
								'name' => '场景名称',
								'type' => 'text',
								'require' => true, // 是否标题前加 * 
								'validate' => array(
										'tpv_notnull||场景名称不能为空',
								),
						),
						'description' => array(
								'name' => '描述',
								'type' => 'text',
								'require' => true, // 是否标题前加 * 
								'validate' => array(
                                    'tpv_notnull||场景名称不能为空',
								),
						),
						'clickcount' => array(
								'name' => '点击数',
								'type' => 'text',
                                'validate'=>array(
                                    'tpv_numeric||必须填写数字'
                                )
						),
                        'thumbnail'=>array(
                                'name' => '封面图片',
                                'type' => 'pic', // 当type=show时，只是展示此字段，这个时候可以定义format，不能定义validat
                                'attr' => array(
                                    'width'=>'220',
                                    'height'=>'220',
                                ),
                                'validate' => array(
                                    'tpv_notnull||场景名称不能为空',
                                ),
                        )
				),
				'submitBtn' => array(
						'name' => '提交'
				)
		),
		// 编辑配置，没有编辑功能的话，就把edit去掉
		'edit' => array(
				'editBtnName' => '编辑案例',
				'editTitle' => '编辑案例',
				'open'   => 'modal', // 操作的类型，modal或者page，modal表示弹出层，page表示采用新的页面。一般带有富文本编辑器，或则插入图片的，采用page。只有add和edit两模块支持page
				'defaultSet' => array(
						'ut'  => 'time', // 调用time()函数
				),
				'editGroups' => array(
                    'scenename' => array(
                        'name' => '场景名称',
                        'type' => 'text',
                        'require' => true, // 是否标题前加 *
                        'validate' => array(
                            'tpv_notnull||场景名称不能为空',
                        ),
                    ),
                    'description' => array(
                        'name' => '描述',
                        'type' => 'text',
                        'require' => true, // 是否标题前加 *
                        'validate' => array(
                            'tpv_notnull||场景名称不能为空',
                        ),
                    ),
                    'clickcount' => array(
                        'name' => '点击数',
                        'type' => 'text',
                        'validate'=>array(
                            'tpv_numeric||必须填写数字'
                        )
                    ),
                    'thumbnail'=>array(
                        'name' => '封面图片',
                        'type' => 'pic', // 当type=show时，只是展示此字段，这个时候可以定义format，不能定义validat
                        'attr' => array(
                            'width'=>'220',
                            'height'=>'220',
                        ),
                        'validate' => array(
                            'tpv_notnull||场景名称不能为空',
                        ),
                    )
				),
				'submitBtn' => array(
						'name' => '保存'
				)
		),
		// 删除配置，没有删除功能的话，就把delete去掉
		// 本配置只支持标记删除，就是在表里有个字段，设置它的值=多少表示删除
		'delete' => array(
				'multiBtnName' => '批量删除',
				'del_key' => 'dt',
				'del_value' => time(),
				'confirmMsg' => '确定要删除该案例？',
				'confirmMsgMulti' => '确定要批量删除案例？',
				'defaultSet' => array(
						'ut'  => 'time', // 调用time()函数
				),
		)
);
