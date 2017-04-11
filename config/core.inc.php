<?php
/**
 * Created on 2010-3-13
 *
 * 系统定义的核心列表文件
 * 
 * @author wangyihuang
 */

// 系统默认的核心列表文件
return array(
	CORE_ROOT.'/class/core/db_op.class.php',
	CORE_ROOT.'/class/core/factory.class.php',
	CORE_ROOT.'/class/core/app.class.php',
	CORE_ROOT.'/class/core/u.class.php',
	CORE_ROOT.'/class/core/app_exception.class.php',
	CORE_ROOT.'/class/core/log.class.php',
	CORE_ROOT.'/class/core/cookie.class.php',
	
	// 包含缓存相关
	CORE_ROOT.'/class/util/cache/cache.class.php',
	CORE_ROOT.'/class/util/cache/plugins/cache_mm.class.php',
	
	// 包含队列相关
	CORE_ROOT.'/class/util/redis/Predis.php',
	CORE_ROOT.'/class/util/cache/queue.class.php',
	
	COMMON_PATH.'/include/public.inc.php',
	COMMON_PATH.'/include/apppub.inc.php',
);
?>