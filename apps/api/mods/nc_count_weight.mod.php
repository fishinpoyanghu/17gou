<?php
/**
 * @since 2016-01-27
 */
class NcCountWeightMod extends BaseMod{
	
	public function run(){
		$currentTime = time();
		$hourAgo = $currentTime - 3600;
		$nc_list_mod = Factory::getMod('nc_list');
		$where = array(
			'a.rt' => array(
				$hourAgo,">="
			)
		);
		
	}
}