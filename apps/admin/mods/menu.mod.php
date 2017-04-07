<?php
/**
 * 菜单的处理类
 */

class MenuMod extends BaseMod {
	
	private $t_mod;
	
	public function __construct() {
		
		$tbl = 'menu';
		$this->t_mod = Factory::getMod('table');
		
		$this->t_mod->tryLoadTable($tbl);
	}
	
	/**
	 * 得到顶部导航菜单
	 */
	public function getTopMenuList() {
		
		$where = array(
				'pid' => 0,
				'stat' => 0,
		);
		
		$orderby = ' ORDER BY weight DESC, menu_id DESC';
		
		$pagesize = 1024;
		
		$menu_list = $this->t_mod->getTableListSimple($where, $orderby, 0, $pagesize);
		$ret = array();
		
		foreach ($menu_list as $menu) {
			$ret[$menu['menu_id']] = $menu;
		}
		
		return $ret;
	}
	
	/**
	 * 得到左边菜单列表
	 * 
	 * @param int $nav
	 * @return array
	 */
	public function getLeftMenuList($nav) {
		
		$where = array(
				'pid' => $nav,
				'stat' => 0,
		);
		
		$orderby = ' ORDER BY weight DESC, menu_id DESC';
		
		$pagesize = 1024;
		
		$group_menu_list = $this->t_mod->getTableListSimple($where, $orderby, 0, $pagesize);
		
		$pid_list = array();
		foreach ($group_menu_list as $group_menu) {
			$pid_list[] = $group_menu['menu_id'];
		}
		
		if (count($pid_list) > 0) {
			$where = array(
					'pid' => array($pid_list, 'in'),
					'stat' => 0,
			);
			
			$menu_list_tmp = $this->t_mod->getTableListSimple($where, $orderby, 0, $pagesize);
			$menu_list = array();
			foreach ($menu_list_tmp as $menu) {
				if (!isset($menu_list[$menu['pid']])) {
					$menu_list[$menu['pid']] = array($menu);
				}
				else {
					$menu_list[$menu['pid']][] = $menu;
				}
			}
			
			unset($menu_list_tmp);
		}
		
		foreach ($group_menu_list as $k=>$group_menu) {
			
			$group_menu_list[$k]['sub_list'] = isset($menu_list[$group_menu['menu_id']]) ? $menu_list[$group_menu['menu_id']] : array();
		}
		
		return $group_menu_list;
	}
}