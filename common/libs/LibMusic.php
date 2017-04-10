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

    // 允许上传的最大文件大小，单位是KB
    private $max_size;

    public function __construct() {

        // 默认支持jpg、png和gif图片
        $this->uptypes = array(
            //audio
            'mp3'   => 'audio/mp3',
            'mid'   => 'audio/midi',
            'ogg'   => 'audio/ogg',
            'mp4a'  => 'audio/mp4',
            'wav'   => 'audio/wav',
            'wma'   => 'audio/x-ms-wma',
        );

        // 默认支持的后缀名是：.mp3
        $this->upexts = array(
            '.mp3',
            '.mid',
            '.ogg',
            '.mp4a',
            '.wav',
            '.wma',
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
     * 执行音乐上传
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

        if (false === strpos($_POST[$name], 'data:audio/mpeg;base64,')) {
            return make_result(1, '非法上传！');
        }

        $str = base64_decode(str_replace('data:audio/mpeg;base64,', '', $_POST[$name]));

        if (!$str) {
            return make_result(1, '图片上传解码失败！');
        }

        $filesize = strlen($str);
        if ($filesize > ($this->max_size*1024)) {
            return make_result(1, '上传文件超出规定大小');
        }

        // 到这里，表示合法了，可以上传啦
        $ext = '.mp3';
        $dest_dirinfo = $this->getUploadDestDirInfo();
        $dest_dir = $dest_dirinfo['abs_dir'];
        $dest_filename = $this->getUploadDestFileName($ext);
        $dest_url = $dest_dirinfo['url'].$dest_filename;
        $dest_abs_path = $dest_dir.$dest_filename;

        file_put_contents($dest_abs_path, $str);


        $data = array(
            'src_name' => 'paste_img',
            'dest_name' => str_replace($ext, '', $dest_filename),
            'url' => $dest_url,
            'extension' => 'png',
            'type' => 'image/png',
            'abs_path' => $dest_abs_path,
            'filesize' => $filesize,
        );

        return make_result(0, 'succ', $data);
    }

    /**
     * 执行音乐上传
     *
     * 图片默认会被上传到 uploads/{$year}/{$month}/下，
     *
     * @param String $name, 上传文件的控件的name <input type="file" name="$name" />
     */
    public function doUploadMusic($name) {

        // 判断上传文件名不能为空
        if (empty($name)) {
            return make_result(1, '缺少文件上传参数');
        }

        // 判断$_FILES是否有此名称的上传
        if (!isset($_FILES[$name])) {
            return make_result(1, '上传文件错误');
        }

        if (!is_uploaded_file($_FILES[$name]['tmp_name'])) {
            return make_result(1, '上传的音乐不存在');
        }

        $file = $_FILES[$name];

        // 判断文件大小是否在允许范围内
        if ($file['size'] > ($this->max_size*1024)) {
            return make_result(1, '上传文件超出规定大小');
        }

        //判断文件上传类型是否在允许范围内
        if (!in_array($file['type'], $this->uptypes)) {
            return make_result(1, '上传文件类型不合法');
        }



        // 判断文件后缀是否合法
        $pinfo = pathinfo($file["name"]);
        $ext = '.'.strtolower($pinfo['extension']);
        if (!in_array($ext, $this->upexts)) {
            return make_result(1, '上传文件后缀不合法');
        }

        // 到这里，表示合法了，可以上传啦
        $dest_dirinfo = $this->getUploadDestDirInfo();
        $dest_dir = $dest_dirinfo['abs_dir'];
        $dest_filename = $this->getUploadDestFileName($ext);
        $dest_url = $dest_dirinfo['url'].$dest_filename;
        $dest_abs_path = $dest_dir.$dest_filename;

        $image_size = getimagesize($file['tmp_name']);

        if(!move_uploaded_file($file['tmp_name'], $dest_abs_path)) {
            return make_result(1, '移动文件出错');
        }

        $data = array(
            'src_name' => $pinfo['filename'],
            'dest_name' => str_replace($ext, '', $dest_filename),
            'url' => $dest_url,
            'extension' => $pinfo['extension'],
            'type' => $file['type'],
            'abs_path' => $dest_abs_path,
            'filesize' => $file['size'],
        );

        return make_result(0, 'succ', $data);
    }

    /**
     * 得到上传文件存放的目标目录，如果不存在，则创建
     *
     * @return array array('abs_dir' => $abs_dir, 'url' => $url);
     */
    private function getUploadDestDirInfo() {

        $subdir = date('Y').'/'.date('m').'/';
        $dir = UPLOAD_PATH.$subdir;

        if(!is_dir($dir)){
            mkdir($dir, 0775, true);
        }

        return array(
            'abs_dir' => $dir,
            'url' => $subdir,
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