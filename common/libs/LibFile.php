<?php
/**
 * 文件上传相关的操作类
 * 
 * @author wangyihuang
 */

class LibFile {
	
	// 允许上传的文件类型
	private $uptypes;
	
	// 允许上传的文件后缀名
	private $upexts;
	
	// 文件类型对应的后缀
	private $uptypes_ext;
	
	// 允许上传的最大文件大小，单位是KB
	private $max_size;
		
	public function __construct() {
		
		// 默认支持jpg、png和gif图片
		$this->uptypes = array(
				'image/jpg',
				'image/jpeg',
				'image/png',
				'image/x-png',
				'image/pjpeg',
				'image/gif',
		);
		
		// 默认支持的后缀名是：.jpg, .jpeg, .png, .gif
		$this->upexts = array(
				'.jpg',
				'.jpeg',
				'.png',
				'.gif',
		);
		
		// 上传类型对应的后缀名
		$this->uptypes_ext = array(
				'image/jpg' => '.jpg',
				'image/jpeg' => '.jpg',
				'image/png' => '.png',
				'image/x-png' => '.png',
				'image/pjpeg' => '.jpg',
				'image/gif' => '.jpg',
		);
		
		// 默认允许上传的文件大小是1MB
		$this->max_size = 1024;
	}
	
	/**
	 * 设置允许上传的文件类型
	 * @param array $uptypes
	 */
	public function setUptypes($uptypes) {
		$this->uptypes = $uptypes;
	}
	
	/**
	 * 设置允许上传的文件后缀
	 * @param array $upexts
	 */
	public function setUpexts($upexts) {
		$this->upexts = $upexts;
	}
	
	/**
	 * 设置允许上传的文件最大大小，单位是KB
	 * 
	 * @param int $max_size
	 */
	public function setMaxsize($max_size) {
		$this->max_size = $max_size;
	}
	
	/**
	 * 执行图片上传
	 *
	 * 图片默认会被上传到 uploads/{$year}/{$month}/下，
	 *
	 * @param String $name, 上传文件的控件的name <input type="file" name="$name" />
	 */
	public function doUploadBase64($name) {
		
		// 判断上传文件名不能为空
		if (empty($name)) {
			return make_result(1, '缺少文件上传参数');
		}
		
		// 判断$_POST是否有此名称的上传
		if (!isset($_POST[$name])) {
			return make_result(1, '上传文件错误');
		}
		
		$bimg64s = $_POST[$name];
		
		// 判断是否是数组
		if (!is_array($bimg64s)) {
			$bimg64s = array($bimg64s);
		}
		
		$data = array();
		foreach ($bimg64s as $k=>$str) {
			
			if (!preg_match('/^(data:\s*(image\/\w+);base64,)/', $str, $res)) {
				return make_result(1, '非法的base64图片');
			}
			
			if (!in_array($res[2], $this->uptypes)) {
				return make_result(1, '不被允许的文件类型');
			}
			
			$str = base64_decode(str_replace($res[1], '', $str));
					
			if (!$str) {
				return make_result(1, '图片上传解码失败！');
			}
			
			$filesize = strlen($str);
			if ($filesize > ($this->max_size*1024)) {
				return make_result(1, '上传文件超出规定大小');
			}
			
			// 到这里，表示合法了，可以上传啦
			$ext = $this->uptypes_ext[$res[2]];
			$dest_dirinfo = $this->getUploadDestDirInfo();
			$dest_dir = $dest_dirinfo['abs_dir'];
			$dest_filename = $this->getUploadDestFileName($ext);
			$dest_url = $dest_dirinfo['url'].$dest_filename;
			$dest_abs_path = $dest_dir.$dest_filename;
			
			file_put_contents($dest_abs_path, $str);
			
			$image_size = getimagesize($dest_abs_path);
			
			$data[] = array(
					'src_name' => 'paste_img',
					'dest_name' => str_replace($ext, '', $dest_filename),
					'w' => $image_size[0],
					'h' => $image_size[1],
					'url' => $dest_url,
					'extension' => str_replace('.','', $ext),
					'type' => $res[2],
					'abs_path' => $dest_abs_path,
					'filesize' => $filesize,
			);
		}
		
		return make_result(0, 'succ', $data);
	}

	/**
	 * 上传文件
	 * @param string $name 上传文件的控件的name <input type="file" name="$name" />
	 * @param string $uploadPath 文件上传的目录,在这个目录下面再用date('Ym')创建一层目录
	 * @param string $uploadUrl 文件上传目录的URL
	 * @param int $size 上传限制,单位为kb
	 * @param array $ext 上传格式限制,如array('.jpg', '.gif', '.png')
	 * @param string $fileName 指定文件名
	 * @return array 返回array('state' => bool, 'msg' => string, 'url' => string)
	 *			 state 上传状态,成功为true,失败为false
	 *			 msg   上传失败的提示信息: SIZE_OVER(上传的尺寸过大)/ EXT_ERROR(格式不允许)/UPLOAD_ERROR上传失败
	 *			 url	 上传成功可访问到该图片的url
	 */
	public static function upload($name, $uploadPath, $uploadUrl, $size = 1000, $ext = array(), $fileName = ''){
		$res = array();
		$_FILES[$name]['tmp_name'] = str_replace("\\\\", "\\", $_FILES[$name]['tmp_name']); //框架自动加反斜杠了
		if(!is_uploaded_file($_FILES[$name]['tmp_name']) || !$_FILES[$name]['name']){
			$res['state'] = false;
			$res['url'] = "";
			$res['msg'] = "UPLOAD_ERROR";
			return $res;
		}

		$uploadInfo = self::getUploadInfo($_FILES[$name]['name'], $uploadPath, $uploadUrl, $fileName);

		//格式验证
		$current_type = $uploadInfo['ext'];

		if(!in_array($current_type, $ext)){
			$res['msg'] = "EXT_ERR";
			$res['state'] = false;
			//return $res;
		}
		//大小验证

		$file_size = 1024 * $size;
		if($_FILES[$name]['size'] > $file_size){
			$res['msg'] = "SIZE_OVER";
			$res['state'] = false;
			return $res;
		}

		//保存图片
		$res['state'] = true;
		$res['url'] = $uploadInfo['uploadUrl'];
		$res['file'] = $uploadInfo['uploadpath'];

		$result = move_uploaded_file($_FILES[$name]['tmp_name'], $uploadInfo['uploadpath']);
		if(!$result){
			$res['state'] = false;
			$res['url'] = "";
			$res['file'] = "";
			$res['msg'] = "UPLOAD_ERROR";
		}
		$res['ext'] = $current_type;
		$res['msg'] = "UPLOAD_SUCCESS";

		return $res;
	}

	/**
	 * 获得文件上传路径及URL信息
	 * @param string $oldFileName $_FILES["$name"]['name']
	 * @param string $uploadPath 文件上传的目录
	 * @param string $uploadUrl 文件上传目录的URL
	 * @param string $fileName 指定文件名
	 *
	 * @return array 返回  array('url' => string, 'uploadFile' => string);
	 * 其中: url		可访问文件的URL
	 * uploadFile 新文件的完整路径
	 * 文件名命名规则 date('dHis') . mt_rand(1000, 9999).'_s' + 文件格式
	 * $fileName 固定的文件名
	 */
	private static function getUploadInfo($oldFileName, $uploadPath, $uploadUrl, $fileName = ''){
		$sp = substr($uploadPath, -1, 1) == '/' || substr($uploadPath, -1, 1) == '\\' ? '' : '/';
		$info = array();
		if(!$fileName){
			$_dm = date("ym");
			$uploadPath = $uploadPath . $sp . $_dm . '/';
			$uploadUrl = $uploadUrl . $sp . $_dm . '/';
			$_fileName = date('dHis') . mt_rand(1000, 9999) . self::getExt($oldFileName);
		}
		else{
			$_fileName = $fileName;
		}
		$info['uploadDir'] = $uploadPath;
		$sp = substr($uploadPath, -1, 1) == '/' || substr($uploadPath, -1, 1) == '\\' ? '' : '/';
		$uploadPath = $uploadPath . $sp . $_fileName;
		$uploadUrl = $uploadUrl . $sp . $_fileName;


		$info['uploadpath'] = $uploadPath;
		$info['uploadUrl'] = $uploadUrl;
		$info['ext'] = self::getExt($oldFileName);

		//检查是否有该文件夹，如果没有就创建
		if(!is_dir($info['uploadDir'])){
			mkdir($info['uploadDir'], 0775, true);
		}

		return $info;
	}

	public static function getExt($fileName){
		return strtolower(strrchr($fileName, '.'));
	}

	/**
	 *在线管理图片
	 * @param string $path	  图片文件的绝对路径
	 * @param string $files	   引用数组赋初值为空数组
	 *
	 * @return string $files	   文件数组
	 */
	public static function fileManage($originPath, $url, $allowFiles, &$files = array()){
		$_dm = date("ym");
		$path = $originPath . $_dm . '/';
		if(!is_dir($path)) return array();
		$_handle = opendir($path);
		while(false !== ($file = readdir($_handle))){
			if($file != '.' && $file != '..'){
				$path2 = $path . $file;
				if(is_dir($path2)){
					self::fileManage($path2, $allowFiles, $files);
				}
				else{
					if(preg_match("/\.(".$allowFiles.")$/i", $file)){
						$files[] = array(
							'url'=> $url.substr($path2, strlen($originPath)),
							'mtime'=> filemtime($path2)
						);
					}
				}
			}
		}
		return $files;
	}
	
	/**
	 * 执行图片上传
	 * 
	 * 图片默认会被上传到 uploads/{$year}/{$month}/下，
	 * 
	 * @param String $name, 上传文件的控件的name <input type="file" name="$name" />
	 */
	public function doUploadPic($name,$size='',$path='') {

		// 判断上传文件名不能为空
		if (empty($name)) {
			return make_result(1, '缺少文件上传参数');
		}
		
		// 判断$_FILES是否有此名称的上传
		if (!isset($_FILES[$name])) {
			return make_result(1, '上传文件错误');
		}
		
		if (!is_uploaded_file($_FILES[$name]['tmp_name'])) {
			return make_result(1, '上传的图片不存在');
		}
		if($size){
			list($width, $height) = getimagesize($_FILES[$name]['tmp_name']);
			list($mWidth, $mHeight) = explode('*', $size);
			if(abs($width - $mWidth) <= 2 && abs($height - $mHeight) <= 2){
			}else{
				return make_result(1, '图片尺寸错误,请上传'.$size.'的图片');
			}
		}
		
		$files = $_FILES[$name];

		// 这里，判断是单张图片上传，还是多张图片上传转化成多张图片的形式
		if (!is_array($files['name'])) {
			$files['name'] = array($files['name']);
			$files['type'] = array($files['type']);
			$files['tmp_name'] = array($files['tmp_name']);
			$files['error'] = array($files['error']);
			$files['size'] = array($files['size']);
		}
		
		// 合法性判断
		if (count($files['name']) < 1) {
			return make_result(1, '上传文件不能为空');
		}		
		
		foreach ($files['name'] as $k=>$v) {
			// 判断文件大小是否在允许范围内
			if ($files['size'][$k] > ($this->max_size*1024)) {
				return make_result(1, '上传文件超出规定大小');
			}
			
			// 判断文件上传类型是否在允许范围内
			if (!in_array($files['type'][$k], $this->uptypes)) {
				return make_result(1, '上传文件类型不合法');
			}
			
			// 判断文件后缀是否合法
			$pinfo = pathinfo($v);
			$ext = '.'.strtolower($pinfo['extension']);
			if (!in_array($ext, $this->upexts)) {
				return make_result(1, '上传文件后缀不合法');
			}
		}
		// end 合法性判断
		
		// 到这里，表示合法了，可以上传啦
		$data = array();
		
		$dest_dirinfo = $this->getUploadDestDirInfo($path);
		$dest_dir = $dest_dirinfo['abs_dir'];
		
		foreach ($files['name'] as $k=>$v) {
			
			$dest_filename = $this->getUploadDestFileName($ext);
			$dest_url = $dest_dirinfo['url'].$dest_filename;
			$dest_abs_path = $dest_dir.$dest_filename;
			
			$image_size = getimagesize($files['tmp_name'][$k]);
			
			if(!move_uploaded_file($files['tmp_name'][$k], $dest_abs_path)) {
				return make_result(1, '移动文件出错');
			}
			
			$data[] = array(
					'src_name' => $pinfo['filename'],
					'dest_name' => str_replace($ext, '', $dest_filename),
					'w' => $image_size[0],
					'h' => $image_size[1],
					'url' => $dest_url,
					'extension' => $pinfo['extension'],
					'type' => $files['type'][$k],
					'abs_path' => $dest_abs_path,
					'filesize' => $files['size'][$k],
			);
		}
		
		return make_result(0, 'succ', $data);
	}



    /**
     * 保存远程图片
     *
     *
     */
    function downloadImage($url){
        if(trim($url)==''){
            return make_result(1, '地址为空');
        }

        $ext = ".jpg";
        $dest_dirinfo = $this->getUploadDestDirInfo();
        $dest_dir = $dest_dirinfo['abs_dir']; // 图片存放的真实目标目录
        $dest_filename = $this->getUploadDestFileName($ext); // 图片存放的真实目标文件名
        $dest_url = $dest_dirinfo['url'].$dest_filename; // 图片存放的网络地址路径，形如 2015/11/xxxx.jpg
        $dest_abs_path = $dest_dir.$dest_filename; // 图片存放的真实路径，形如 /data/web/bigh5/uploads/2015/11/xxxx.jpg


        //获取远程文件所采用的方法
        $ch=curl_init();
        $timeout=5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $img=curl_exec($ch);
        curl_close($ch);

        //文件大小
        $fp2=@fopen($dest_abs_path,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);

        $image_size = getimagesize($dest_abs_path);

        $data = array(
            'src_name' => "0",
            'dest_name' => str_replace($ext, '', $dest_filename),
            'w' => $image_size[0],
            'h' => $image_size[1],
            'url' => $dest_url, // 图片存放的网络地址路径，形如 2015/11/xxxx.jpg
            'extension' => $ext,
            'type' => filetype($dest_abs_path),
            'abs_path' => $dest_abs_path, // 图片存放的真实路径，形如 /data/web/bigh5/uploads/2015/11/xxxx.jpg
            'filesize' => filesize($dest_abs_path),
        );

        return make_result(0, 'succ', $data);
    }


	/**
	 * 得到上传文件存放的目标目录，如果不存在，则创建
	 * 
	 * @return array array('abs_dir' => $abs_dir, 'url' => $url);
	 */
	private function getUploadDestDirInfo($path='') {
		
		$subdir = date('Y').'/'.date('m').'/';
		$dir = UPLOAD_PATH.$path.$subdir;
		
		if(!is_dir($dir)){
			mkdir($dir, 0775, true);
		}
		
		return array(
				'abs_dir' => $dir,
				'url' => $path.$subdir,
		);
	}
	
	/**
	 * 得到上传文件存放到目标目录的文件名
	 * 
	 * @param string $ext, 文件的后缀名
	 */
	private function getUploadDestFileName($ext) {
		
		$len = mt_rand(6,10);
		$src = array(0,1,2,3,4,5,6,7,8,9,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z);
		$src2 = array(a,b,c,d,e,f,g,h,i,j,k,m,n,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z);
		
		$rstr = '';
		for ($i=0; $i < $len; $i++) {
		
			$rstr .= $src[mt_rand(0, count($src)-1)];
		}
		
		return get_auto_id(C('AUTOID_FILENAME')).$src2[mt_rand(0, count($src2)-1)].$rstr.$ext;
	}





}