<?php
/**
 * Created on 2010-5-8
 * 
 * 工厂类
 * 用户创建框架的类对象
 * 
 * @package core
 * @author wangyihuang
 * @version 2.0
 */

// 类定义开始
class Factory {
	
	public static function getPath($type, $class_name='') {
		
		switch($type) {
			case 'ctrl':
				$ctrl_path = C('ctrl_path');
				if (!is_array($ctrl_path)) $ctrl_path = array();
				
				$ret = get_app_root().'/ctrls'.(isset($ctrl_path[$class_name]) ? '/'.$ctrl_path[$class_name] : '');
				
				return $ret;
				break;
			
			case 'mod':
				$mod_path = C('mod_path');
				if (!is_array($mod_path)) $mod_path = array();
				
				$ret = get_app_root().'/mods'.(isset($mod_path[$class_name]) ? '/'.$mod_path[$class_name] : '');
				
				return $ret;
				break;
			
			case 'data':
				$data_path = C('data_path');
				if (!is_array($data_path)) $data_path = array();
				
				$ret = get_app_root().'/datas'.(isset($data_path[$class_name]) ? '/'.$data_path[$class_name] : '');
				
				return $ret;
				break;
						
			case 'view':
				return get_app_root().'/views';
				break;
			default:
				break;
		}
	}
	
	/**
	 * 获取控制器类实例
	 * 
	 * 实例化，并返回一个控制器对象
	 */
	public static function getCtrl($class_name) {
		
		static $_instance = array();		
		
		$path = Factory::getPath('ctrl', $class_name);
		$identify = to_guid_string($path.'/'.$class_name);
		
		// 如果还未初始化，或者是终端脚本
		if (IS_CLI || (!isset($_instance[$identify]))) {
			
			$ctrl_file = $path.'/'.$class_name.'.ctrl.php';
			
			if (!is_file($ctrl_file)) throw_exception('加载控制器文件时出现异常，可能是控制器不存在。ctrl-file:'.$ctrl_file);
			
			require_once($ctrl_file);
			
			$class_name = parse_name($class_name.'_ctrl', 1);
			
			$o = new $class_name();
			
			$_instance[$identify] = $o;
		}
		
		return $_instance[$identify];
	}
	
	/**
	 * 获取模块类实例
	 * 
	 * 实例化并返回一个模块对象
	 */
	public static function getMod($class_name) {
		
		static $_instance = array();
		
		$path = Factory::getPath('mod', $class_name);
		
		$identify = to_guid_string($path.'/'.$class_name);
		
		// 如果还未初始化，或者是终端脚本
		if (IS_CLI || (!isset($_instance[$identify]))) {
			
			$module_file = $path.'/'.$class_name.'.mod.php';
			
			if (!is_file($module_file)) {
				trigger_error(__FUNCTION__.': module file no found: '.$module_file, E_USER_ERROR);
			}
			
			require_once($module_file);
			
			$class_name = parse_name($class_name.'_mod', 1);
			
			$o = new $class_name();
			
			$_instance[$identify] = $o;
		}
		
		return $_instance[$identify];
	}
	
	/**
	 * 获取数据层实例
	 * 
	 * 返回一个数据层class对象
	 */
	public static function getData($class_name) {
		
		static $_instance = array();
		
		$path = Factory::getPath('data', $class_name);
		
		$identify = to_guid_string($path.'/'.$class_name);
		
		// 如果还未初始化，或者是终端脚本
		if (IS_CLI || (!isset($_instance[$identify]))) {
			
			$data_file = $path.'/'.$class_name.'.data.php';
			
			if (!is_file($data_file)) {
				trigger_error(__FUNCTION__.': data file no found: '.$data_file, E_USER_ERROR);
			}

			// 开始加载编译过的data缓存文件
			require_once($data_file);
			
			$class_name = parse_name($class_name.'_data', 1);
			
			$o = new $class_name();
			
			$_instance[$identify] = $o;			
		}
		
		return $_instance[$identify];
	}
	
	/**
	 * 输出视图
	 */
	public static function getView($filename, $data=array(), $output=true){
		
		if (!headers_sent()) header('Content-type: text/html; charset=utf-8'); // temp
		
		if ($data) {
			if (is_array($data)) {
				//	foreach ($data as $k => $v) { if (!isset($$k)) $$k = $v; }			
				// replace this loop with 'extract' function. php internal is much more faster. by jingwei 
				extract($data, EXTR_SKIP);
			}
			elseif (is_object($data)) {
				$data = get_object_vars($data);
			}
			else {
				trigger_error(__FUNCTION__.': unsupported variable type of data', E_USER_ERROR);
			}
		}
		
		$path = Factory::getPath('view');
		
		$view_file = $path . '/' . $filename . '.view.php';
		
		if (!is_file($view_file)) {
			trigger_error(__FUNCTION__.': view file no found: '.$view_file, E_USER_ERROR);
		}
		
		if ($output) {
			return require($view_file);
		}
		else {
			ob_start();
			require $view_file;
			$view_content = ob_get_clean();
			
			return $view_content;
		}
	}
	
	/**
	 * 编译Data文件
	 * 
	 * 返回编译后的data content
	 */
	public static function compileData($data_file) {
		
		$data_content = file_get_contents($data_file);
		
		return $data_content;
	}
	
	/**
	 * 编译视图文件
	 * 
	 * 返回编译后的view content
	 */
	public static function compileView($view_file) {
		
		$view_content = file_get_contents($view_file);
		
		return $view_content;
	}

    /**
     * 跨模块调用控制器
     */
    public static function getModulePath($module,$type, $class_name=''){
        switch($type) {
            case 'ctrl':
                $ctrl_path = C('ctrl_path');
                if (!is_array($ctrl_path)) $ctrl_path = array();

                $ret = get_app_root().'/../'.$module.'/ctrls'.(isset($ctrl_path[$class_name]) ? '/'.$ctrl_path[$class_name] : '');

                return $ret;
                break;

            case 'mod':
                $mod_path = C('mod_path');
                if (!is_array($mod_path)) $mod_path = array();

                $ret = get_app_root().'/../'.$module.'/mods'.(isset($mod_path[$class_name]) ? '/'.$mod_path[$class_name] : '');

                return $ret;
                break;

            case 'data':
                $data_path = C('data_path');
                if (!is_array($data_path)) $data_path = array();

                $ret = get_app_root().'/../'.$module.'/datas'.(isset($data_path[$class_name]) ? '/'.$data_path[$class_name] : '');

                return $ret;
                break;

            case 'helper':
                return get_app_root().'/../'.$module.'/helpers';
                break;

            case 'view':
                return get_app_root().'/../'.$module.'/views';
                break;
            default:
                break;
        }
    }

    public static function getModuleCtrl($module,$class_name){
        static $_instance = array();

        $path = Factory::getModulePath($module,'ctrl', $class_name);
        $identify = to_guid_string($path.'/'.$class_name);

        // 如果还未初始化，或者是终端脚本
        if (IS_CLI || (!isset($_instance[$identify]))) {

            $ctrl_file = $path.'/'.$class_name.'.ctrl.php';

            if (!is_file($ctrl_file)) throw_exception('加载控制器文件时出现异常，可能是控制器不存在。ctrl-file:'.$ctrl_file);

            require_once($ctrl_file);

            $class_name = parse_name($class_name.'_ctrl', 1);

            $o = new $class_name();

            $_instance[$identify] = $o;
        }

        return $_instance[$identify];
    }

    public static function getModuleMod($module,$class_name){
        static $_instance = array();

        $path = Factory::getModulePath($module,'mod', $class_name);

        $identify = to_guid_string($path.'/'.$class_name);

        // 如果还未初始化，或者是终端脚本
        if (IS_CLI || (!isset($_instance[$identify]))) {

            $module_file = $path.'/'.$class_name.'.mod.php';

            if (!is_file($module_file)) {
                trigger_error(__FUNCTION__.': module file no found: '.$module_file, E_USER_ERROR);
            }

            require_once($module_file);

            $class_name = parse_name($class_name.'_mod', 1);

            $o = new $class_name();

            $_instance[$identify] = $o;
        }

        return $_instance[$identify];
    }

    public static function getModuleData($module,$class_name){
        static $_instance = array();

        $path = Factory::getModulePath($module,'data', $class_name);

        $identify = to_guid_string($path.'/'.$class_name);

        // 如果还未初始化，或者是终端脚本
        if (IS_CLI || (!isset($_instance[$identify]))) {

            $data_file = $path.'/'.$class_name.'.data.php';

            if (!is_file($data_file)) {
                trigger_error(__FUNCTION__.': data file no found: '.$data_file, E_USER_ERROR);
            }

            // 开始加载编译过的data缓存文件
            require_once($data_file);

            $class_name = parse_name($class_name.'_data', 1);

            $o = new $class_name();

            $_instance[$identify] = $o;
        }

        return $_instance[$identify];
    }

    public static function getModuleView($module,$filename, $data=array(), $output=true){
        if (!headers_sent()) header('Content-type: text/html; charset=utf-8'); // temp

        if ($data) {
            if (is_array($data)) {
                //	foreach ($data as $k => $v) { if (!isset($$k)) $$k = $v; }
                // replace this loop with 'extract' function. php internal is much more faster. by jingwei
                extract($data, EXTR_SKIP);
            }
            elseif (is_object($data)) {
                $data = get_object_vars($data);
            }
            else {
                trigger_error(__FUNCTION__.': unsupported variable type of data', E_USER_ERROR);
            }
        }

        $path = Factory::getModulePath($module,'view');

        $view_file = $path . '/' . $filename . '.view.php';

        if (!is_file($view_file)) {
            trigger_error(__FUNCTION__.': view file no found: '.$view_file, E_USER_ERROR);
        }

        if ($output) {
            return require($view_file);
        }
        else {
            ob_start();
            require $view_file;
            $view_content = ob_get_clean();

            return $view_content;
        }
    }

}
