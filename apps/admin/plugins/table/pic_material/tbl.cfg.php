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
				'db_tbl_alias' => 'pic_material',
				'pkid' => 'pic_material_id',
				'autoidCode' => C('AUTOID_PIC_MATERIAL'),
				'title' => '素材库管理',
				'where' => array(
						'stat' => 0,
				),
				'orderby' => 'ORDER BY pic_category_id ASC,pic_material_id DESC',
				'pagesize' => 20, // 列表的分页数目
				// 读取列表的时候是否区分appid, false:不区分 true:区分
				'appid_required' => false, 
				
				// 定义菜单焦点
				'nav' => '52',
				'menu_id' => '14',
				
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
				// 支持最多3级树形目录结构，单字段精准或者模糊匹配
				// 要求：多级目录结构必须是在同一个表里定义，目录结构的树形关系通过parent id标记维系，且最顶级的parent id必须是0。
				
				// 搜索功能 end
		),
		// 列表显示配置
		'list' => array(
				// 第一个字段必须是主键
				'pic_material_id' => array(
						'name' => '素材ID',
				),
				'url' => array(
						'name' => '缩略图',
						'format' => 'tf_pic_material_showpic|54,54'
				),
				'style' => array(
						'name' => '样式',
						'format' => 'tf_pic_material_getcname|style',
				),
				'color' => array(
						'name' => '颜色',
						'format' => 'tf_pic_material_getcname|color',
				),
				'pic_category_id' => array(
						'name' => '类型',
						'format' => 'tf_pic_material_getcname|classify',
				),
// 				'name' => array(
// 						'name' => '图片名称',
// 				),
				'rt'   => array(
						'name' => '创建时间',
						'format' => 'tpf_date',
				),
				'_ops' => array(
						'name' => '操作', // _ops表示操作菜单
						'cnt' => array(
								'delete||<i class="icon-trash gray" title="删除"></i>', // '<a data-id="{$pkid}" class="js-delete">封禁</a>',
						)
					),
		),
		// 删除配置，没有删除功能的话，就把delete去掉
		// 本配置只支持标记删除，就是在表里有个字段，设置它的值=多少表示删除
		'delete' => array(
				'multiBtnName' => '<i class="icon-trash"></i> 批量删除',
				'del_key' => 'stat',
				'del_value' => 1,
				'confirmMsg' => '确定要删除该图标？',
				'confirmMsgMulti' => '确定要批量删除图标？',
				'defaultSet' => array(
						'ut'  => 'time', // 调用time()函数
				),
		),
);
