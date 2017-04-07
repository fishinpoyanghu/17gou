<?php

class TreeMod extends BaseMod {
	
	/**
	 * 把一棵树转成select的下拉option列表
	 * options = array(
	 * 		array(
	 * 			'text'=>'...',
	 * 			'value' => '...',
	 * 		),
	 * 		array(
	 * 			'text'=>'...',
	 * 			'value' => '...',
	 * 		),
	 * )
	 */
	public function tree2Options($node_list, $pkid_key, $node_name) {
		
		$option_list = array();
		foreach ($node_list as $node) {
			
			$prefix = '';
			if ($node['level'] > 1) {
				for ($i=1; $i < $node['level']; $i++) {
					$prefix .= '　';
				}
				$prefix .= '┝';
			}
			
			$option_list[] = array(
					'text' => $prefix.$node[$node_name],
					'value' => $node[$pkid_key],
					'level' => $node['level'],
			);
		}
		
		return $option_list;
	}
	
	/**
	 * 找到一个节点的所有子节点
	 * 
	 * @param array $dep_list
	 * @param array $dep_ids
	 * @param int $pid
	 */
	public function findSubNodeIds($node_list, &$sub_node_ids, $pid, $pkid_key, $pid_key) {
		
		foreach ($node_list as $k=>$node) {
			if ($node[$pid_key] == $pid) {
				$sub_node_ids[] = $node[$pkid_key];
				//unset($node_list[$k]);
				
				$this->findSubNodeIds($node_list, $sub_node_ids, $node[$pkid_key], $pkid_key, $pid_key);
			}
		}
	}
	
	/**
	 * 返回非结构化的树形结构
	 * 
	 * @param array $list
	 * @param int $top_node_pid
	 * @param string $pkid_key
	 * @param string $pid_key
	 * @return array $tree
	 */
	public function getTree($list, $top_node_pid, $pkid_key, $pid_key) {
		
		if (empty($pid_key)) return $list;
		
		$node_list = array();
		
		// 先找出来顶级节点
		$top_node = false;
		foreach ($list as $k=>$row) {
			if ($row[$pid_key] == $top_node_pid) {
				$top_node = $row;
				unset($list[$k]);
				
				$top_node = $this->richNode($top_node, null, $pkid_key);
				$node_list[] = $top_node;
				
				$this->findSubTree($list, $node_list, $top_node, $pkid_key, $pid_key);
			}
		}
		
		return $node_list;
	}
	
	/**
	 * 找到一棵树的子树
	 * @param array $list
	 * @param array $node_list
	 * @param array $parent_node
	 * @param string $pkid_key
	 * @param string $pid_key
	 */
	private function findSubTree(&$list, &$node_list, $parent_node, $pkid_key, $pid_key) {
		
		foreach ($list as $k=>$row) {
			
			if ($row[$pid_key] == $parent_node[$pkid_key]) {
				
				$node = $this->richNode($row, $parent_node, $pkid_key);
				
				$node_list[] = $node;
				
				$this->findSubTree($list, $node_list, $node, $pkid_key, $pid_key);
			}
		}
	}
	
	/**
	 * 对节点 加上level和path，
	 * level表示节点所处的当前树的层级
	 * path表示节点所有父节点的路径
	 * 
	 * @param array $node
	 * @param array $parent_node
	 * @return array $node
	 */
	private function richNode($node, $parent_node, $pkid_key) {
		
		if (empty($parent_node)) {
			$parent_node = array(
					'level' => 0,
					'path'  => array(),
			);
		}
		
		$node['level'] = $parent_node['level']+1;
		$node['path'] = $parent_node['path'];
		if (!empty($parent_node[$pkid_key])) $node['path'][] = $parent_node[$pkid_key];
		
		return $node;
	}
}