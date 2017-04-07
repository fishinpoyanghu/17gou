<?php
/**
 * @since 2016-01-13
 * note 并发情况下，进行锁机制，在这里使用的是文件锁
 */
class NcLockMod extends BaseMod{
	
	private $path;
	private $file = array();
	private $inLock = array();
	private $handle = array();//文件句柄
	
	//初始化
	public function __construct(){
		$this->path = dirname(dirname(__FILE__)).'/lock/';
	}
	
	//锁文件
	public function lockFile($file, $key = 'default'){
		if($this->inLock[$key]) return false;
		$this->file[$file] = 1;
		$this->inLock[$key] = true;
		$file = $this->path.$file;
		$this->handle[$key] = fopen($file , 'w');
		return flock($this->handle , LOCK_EX);
	}
	
	//解锁
	public function unLockFile($key = 'default'){
		fclose($this->handle[$key]);
		$this->inLock[$key] = false;
	}
}