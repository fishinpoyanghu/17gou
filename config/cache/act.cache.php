<?php
/**
 * Created on 2011-6-20
 *
 * 防刷系统缓存类型配置
 * 
 * 缓存类型 => array('sid'=>缓存服务器名称, 'timeout'=>过期时间,0表示永不过期)
 */

return array(
	'login'  => array('sid'=>'user_cs', 'timeout'=>0), /// 永不过期	login
	'pic'    => array('sid'=>'pic_cs', 'timeout'=>0),
);
