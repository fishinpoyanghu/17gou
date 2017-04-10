<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/14
 * Time: 10:38
 */

class PubQr{


    private $data;

    private $save_path;

    private $size;

    private $level;

    private $back_color;

    private $fore_color;

    private $type;


    public function __construct($config=null){

        $base_path = COMMON_PATH.'libs/phpqrcode/';
        include_once $base_path."qrlib.php";

        if(isset($config['save_path'])){
            $this->save_path = $config['save_path'];
        }else{
            $this->save_path = realpath(dirname(CORE_ROOT)).'/qr/';
        }

        if(isset($config['size'])){
            $this->size = $config['size'];
        }else{
            $this->size=5;
        }

        if(isset($config['level'])){

            if (in_array( $config['level'], array('L','M','Q','H'))){
                $this->level  =   min(max((int)$config['level'], 1), 10);;
            }else{
                $this->level = 'Q';
            }

        }else{
            $this->level = 'Q';
        }

        if(isset($config['back_color'])){
            $this->back_color = $config['back_color'];
        }else{
            $this->back_color=0xFFFFFF;
        }

        if(isset($config['fore_color'])){
            $this->fore_color['fore_color'] = $config['force_color'];
        }else{
            $this->fore_color = 0x000000;
        }

        if(isset($config['data'])){
            $this->data = $config['data'];
        }else{
            $this->data = C('SITE_DOMAIN');
        }

        if(isset($config['type'])){
            $this->type = $config['type'];
        }else{
            $this->type = 'png';
        }

    }


    public function generate(){
        $dest_dirinfo = $this->getUploadDestDirInfo();
        $dest_filename = $this->getUploadDestFileName('.png');
        $dest_url = $dest_dirinfo['url'].$dest_filename;
        $dest_abs_path = $dest_dirinfo['abs_dir'].$dest_filename;
        $filename = $dest_url;
        QRcode::png($this->data, $dest_abs_path, $this->level,$this->size, 2,false,$this->back_color, $this->fore_color);
        return $filename;
    }





    /**
     * 得到上传文件存放的目标目录，如果不存在，则创建
     *
     * @return array array('abs_dir' => $abs_dir, 'url' => $url);
     */
    private function getUploadDestDirInfo() {

        $subdir = date('Y').'/'.date('m').'/';
        $dir = realpath(dirname(CORE_ROOT)).'/qr/'.$subdir;

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