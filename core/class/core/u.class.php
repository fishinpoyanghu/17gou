<?php
/**
 * Created on 2010-5-8
 * 
 * 框架积累
 * 
 * @package core
 * @author wangyihuang
 * @version 2.0
 */

class U {}

class BaseCtrl extends U {
	public function test_post_data($array){
		if(!is_array($array)){
			return '';
		}
	 
		foreach ($array as $key => $value) {
			 $_POST[$key]= $value;
			  
		} 
	}
}
class BaseMod extends U {}
class BaseData extends U {}
class BaseHelper extends U {}
