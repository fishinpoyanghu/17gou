<?php
/**
 * 文件上传类
 **/
class upload_file {
	/**
	 * 声明*
	 */
	var $upfile_type, $upfile_size, $upfile_name, $upfile;
	var $d_alt, $extention_list, $tmp, $arri;
	var $datetime, $date;
	var $filestr, $size, $ext, $check;
	var $flash_directory, $extention, $file_path, $base_directory;
	var $url; // 文件上传成功后跳转路径;
	var $PicName_Id, $Iframe, $Pic_Path;
	var $is_ResizePic, $pic_width, $pic_height, $im, $base_directory_name;
	function upload_file() {
		/**
		 * 构造函数*
		 */
		$this->set_url ( "http://www.haduo.com/?_c=act&_a=upload_pic" ); // 初始化上传成功后跳转路径;
		$this->set_extention (); // 初始化扩展名列表;
		$this->set_size ( 50 ); // 初始化上传文件KB限制;
		$this->set_date (); // 设置目录名称;
		$this->set_datetime (); // 设置文件名称前缀;
		$this->set_base_directory ( "act" ); // 初始化文件上传根目录名，可修改！;
		$this->set_pic_width ( 200 );
		$this->set_pic_height ( 200 );
		$this->set_is_ResizePic ( 1 );
	}
	/**
	 * 回传图片全路径*
	 */
	function set_sPicName_Id($PicName_Id) {
		$this->PicName_Id = $PicName_Id; //
	}
	/**
	 * 回传Iframe*
	 */
	function set_sIframe($Iframe) {
		$this->Iframe = $Iframe; //
	}
	/**
	 * 缩略宽*
	 */
	function set_pic_width($pic_width) {
		$this->pic_width = $pic_width; //
	}
	/**
	 * 缩略图高*
	 */
	function set_pic_height($pic_height) {
		$this->pic_height = $pic_height; //
	}
	/**
	 * 是否生成缩略图*
	 */
	function set_is_ResizePic($is_ResizePic) {
		$this->is_ResizePic = $is_ResizePic; //
	}
	
	/**
	 * 文件类型*
	 */
	function set_file_type($upfile_type) {
		$this->upfile_type = $upfile_type; // 取得文件类型;
	}
	
	/**
	 * 获得文件名*
	 */
	function set_file_name($upfile_name) {
		$this->upfile_name = $upfile_name; // 取得文件名称;
	}
	
	/**
	 * 获得文件*
	 */
	function set_upfile($upfile) {
		$this->upfile = $upfile; // 取得文件在服务端储存的临时文件名;
	}
	
	/**
	 * 获得文件大小*
	 */
	function set_file_size($upfile_size) {
		$this->upfile_size = $upfile_size; // 取得文件尺寸;
	}
	
	/**
	 * 设置文件上传成功后跳转路径*
	 */
	function set_url($url) {
		$this->url = $url; // 设置成功上传文件后的跳转路径;
	}
	
	/**
	 * 获得文件扩展名*
	 */
	function get_extention() {
		$this->extention = preg_replace ( '/.*\.(.*[^\.].*)*/iU', '\\1', $this->upfile_name ); // 取得文件扩展名;
	}
	
	/**
	 * 设置文件名称*
	 */
	function set_datetime() {
		$this->datetime = date ( "YmdHis" ); // 按时间生成文件名;
	}
	
	/**
	 * 设置目录名称*
	 */
	function set_date() {
		$this->date = date ( "Y-m" ); // 按日期生成目录名称;
	}
	
	/**
	 * 初始化允许上传文件类型*
	 */
	function set_extention() {
		// $this->extention_list = "doc|xls|ppt|avi|txt|gif|jpg|jpeg|bmp|png"; //默认允许上传的扩展名称;
		$this->extention_list = "gif|jpg|jpeg"; // 默认允许上传的扩展名称
	}
	
	/**
	 * 设置最大上传KB限制*
	 */
	function set_size($size) {
		$this->size = $size; // 设置最大允许上传的文件大小;
	}
	
	/**
	 * 初始化文件存储根目录*
	 */
	function set_base_directory($directory) {
		$this->base_directory = $directory; // 生成文件存储根目录;
	}
	/**
	 * 初始化文件存储根目录名称*
	 */
	function set_base_directory_name($base_directory_name) {
		$this->base_directory_name = $base_directory_name; // 生成文件存储根目录;
	}
	
	/**
	 * 初始化文件存储子目录*
	 */
	function set_flash_directory() {
		$this->flash_directory = $this->base_directory . "/" . $this->date; // 生成文件存储子目录;
	}
	
	/**
	 * 错误处理*
	 */
	function showerror($errstr = "未知错误！") {
		echo "<script language=javascript>alert('$errstr');location='javascript:history.go(-1);';</script>";
		exit ();
	}
	
	/**
	 * 跳转*
	 */
	function go_to($str, $url) {
		echo "<script language='javascript'>alert('$str');location='$url';</script>";
		exit ();
	}
	
	/**
	 * 如果根目录没有创建则创建文件存储目录*
	 */
	function mk_base_dir() {
		if (! file_exists ( $this->base_directory )) { // 检测根目录是否存在;
			@mkdir ( $this->base_directory, 0777 ); // 不存在则创建;
		}
	}
	
	/**
	 * 如果子目录没有创建则创建文件存储目录*
	 */
	function mk_dir() {
		if (! file_exists ( $this->flash_directory )) { // 检测子目录是否存在;
			@mkdir ( $this->flash_directory, 0777 ); // 不存在则创建;
		}
	}
	
	/**
	 * 以数组的形式获得分解后的允许上传的文件类型*
	 */
	function get_compare_extention() {
		$this->ext = explode ( "|", $this->extention_list ); // 以"|"来分解默认扩展名;
	}
	
	/**
	 * 检测扩展名是否违规*
	 */
	function check_extention() {
		for($i = 0; each ( $this->ext ); $i ++) // 遍历数组;
{
			if ($this->ext [$i] == strtolower ( $this->extention )) // 比较文件扩展名是否与默认允许的扩展名相符;
{
				$this->check = true; // 相符则标记;
				break;
			}
		}
		if (! $this->check) {
			$this->showerror ( "正确的扩展名必须为" . $this->extention_list . "其中的一种！" );
		}
		// 不符则警告
	}
	
	/**
	 * 检测文件大小是否超标*
	 */
	function check_size() {
		if ($this->upfile_size > round ( $this->size * 1024 )) // 文件的大小是否超过了默认的尺寸;
{
			$this->showerror ( "上传附件不得超过" . $this->size . "KB" ); // 超过则警告;
		}
	}
	
	/**
	 * 文件完整访问路径*
	 */
	function set_file_path() {
		$this->file_path = DATA_PATH . '/act/' . $this->date . "/" . $this->datetime . "." . $this->extention; // 生成文件完整访问路径;
	}
	/**
	 * 返回前台显示图片路径*
	 */
	function set_Pic_Path() {
		$this->Pic_Path = "http://s1.haduo.com/data/act/" . $this->date . "/" . $this->datetime . "." . $this->extention; // 生成前台显示图片路径;
	}
	function ResizeImagess($im, $maxwidth, $maxheight, $name) {
		echo $im . $maxwidth . $maxheight . $name;
		// 取得当前图片大小
		$width = imagesx ( $im );
		$height = imagesy ( $im );
		// 生成缩略图的大小
		if (($width > $maxwidth) || ($height > $maxheight)) {
			$widthratio = $maxwidth / $width;
			$heightratio = $maxheight / $height;
			if ($widthratio < $heightratio) {
				$ratio = $widthratio;
			} else {
				$ratio = $heightratio;
			}
			$newwidth = $width * $ratio;
			$newheight = $height * $ratio;
			
			if (function_exists ( "imagecopyresampled" )) {
				$newim = imagecreatetruecolor ( $newwidth, $newheight );
				imagecopyresampled ( $newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
			} else {
				$newim = imagecreate ( $newwidth, $newheight );
				imagecopyresized ( $newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
			}
			ImageJpeg ( $newim, $name );
			ImageDestroy ( $newim );
		} else {
			ImageJpeg ( $im, $name );
		}
	}
	/**
	 * 上传文件*
	 */
	function copy_file() {
		if (move_uploaded_file ( $this->upfile, $this->file_path )) { // 上传文件;
			
			if ($this->is_ResizePic == 2) {
				
				if ($_FILES ['src'] ['size']) {
					// echo $_FILES['src']['type'];
					if ($_FILES ['src'] ['type'] == "image/pjpeg" || $_FILES ['src'] ['type'] == "image/jpg" || $_FILES ['src'] ['type'] == "image/jpeg") {
						$im = imagecreatefromjpeg ( $this->file_path );
					} elseif ($_FILES ['src'] ['type'] == "image/x-png") {
						$im = imagecreatefrompng ( $this->file_path );
					} elseif ($_FILES ['src'] ['type'] == "image/gif") {
						$im = imagecreatefromgif ( $this->file_path );
					}
					// echo $im;
					// if($im){
					/*
					 * if(file_exists($this->file_path)){
					 * unlink($this->file_path);
					 * }
					 */
					// echo $this->pic_height;
					$this->ResizeImagess ( $im, $this->pic_width, $this->pic_height, $this->file_path );
					ImageDestroy ( $im );
					echo $this->PicName_Id;
					$this->Pic_Path = urlencode ( $this->Pic_Path );
					print "<script language='javascript'>parent.document.getElementById('$this->PicName_Id').value='$this->Pic_Path';location.replace('?_c=act&_a=upload_pic&PicName_Id=$this->PicName_Id&Iframe=$this->Iframe&Is_ReSet=2&PicNames=$this->Pic_Path&pic_height=$this->pic_height&pic_width=$this->pic_width&is_ResizePic=$this->is_ResizePic');</script>";
					// }
				}
			} else {
				print "<script language='javascript'>parent.document.getElementById('$this->PicName_Id').value='$this->Pic_Path';location.replace('?_c=act&_a=upload_pic&PicName_Id=$this->PicName_Id&Iframe=$this->Iframe&Is_ReSet=2&PicNames=$this->Pic_Path');</script>";
			}
		} else {
			print $this->showerror ( "意外错误，请重试！" ); // 上传失败;
		}
	}
	
	/**
	 * 完成保存*
	 */
	function save() {
		
		/*
		 * $this->set_pic_height(); //初始化文件上传子目录名;
		 * $this->set_pic_width(); //初始化文件上传子目录名;
		 */
		$this->set_flash_directory (); // 初始化文件上传子目录名;
		$this->get_extention (); // 获得文件扩展名;
		$this->get_compare_extention (); // 以"|"来分解默认扩展名;
		$this->check_extention (); // 检测文件扩展名是否违规;
		$this->check_size (); // 检测文件大小是否超限;
		$this->mk_base_dir (); // 如果根目录不存在则创建；
		$this->mk_dir (); // 如果子目录不存在则创建;
		$this->set_file_path (); // 生成文件完整访问路径;
		$this->set_Pic_Path ();
		$this->copy_file (); // 上传文件;
	}
}
