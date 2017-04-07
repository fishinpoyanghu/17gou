<?php
/**
 * @since 2016-01-29
 */
class NcWxMod extends BaseMod{
	
	 public function dealwxqrcode(){  
	  	 
	 	if(!$_GET['opid']){
            echo json_encode(array('error_code'=>2));exit;
        }
        $nc_list=Factory::getMod('nc_list');
        $nc_list->setDbConf('main', 'wxuser');
        $where = array(
            'wx_openid' => $_GET['opid']    
        );
          
        $ret = $nc_list->getDataOne($where, array('uid'), array(), array(), false);
        if(!$ret){
            echo json_encode(array('error_code'=>3)); exit;
            //这里应该是直接新建账户
        }
        require_once COMMON_PATH.'libs/wxpay/Wx.Api.php';    
        $wxApi = new WxApi();   
        $image=$wxApi->getQrcode($ret['uid']);  
        if($image['errcode']=='40001'){
            $image=$wxApi->getQrcode($ret['uid'],1);  
        }

        // file_put_contents('/tmp/wxtext.log',  var_export(json_encode($image).'qrccccccccccccccc',true), FILE_APPEND); 
        $imgurl = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($image['ticket']); 
        $subdir = date('Y').'/'.date('m').'/';
        $dir = UPLOAD_PATH.'wxupload/'.$subdir; 
        if(!is_dir($dir)){
            mkdir($dir, 0775, true);
        }

        $imgpath=$dir.date('d').'-'.$ret['uid'].'.jpg'; 
        $this->dlfile($imgurl,$imgpath);  
        
        $bigImgPath = UPLOAD_PATH.'/piclib/haibao.jpg'; //合并的海报
        $cutpath = $imgpath;//要裁剪的二维码
        $qCodePath = $dir.'cut'.date('d').'-'.$ret['uid'].'.jpg';;//裁剪后的地址
        include_once CORE_ROOT.'/class/util/image/thumb.class.php';  
        $thumb = new ThumbHandler(); 
        $is = getimagesize($cutpath);  
        $thumb->setSrcImg($cutpath);
        $thumb->setImgDisplayQuality(60); // 降低图片质量，从而达到降低图片大小的目的
        $thumb->setDstImg($qCodePath);
        $thumb->createImg(240,240); 
        $bigImg = imagecreatefromstring(file_get_contents($bigImgPath)); 
        $qCodeImg = imagecreatefromstring(file_get_contents($qCodePath));
         
        list($qCodeWidth, $qCodeHight, $qCodeType) = getimagesize($qCodePath);
        // imagecopymerge使用注解
        imagecopymerge($bigImg, $qCodeImg, 198, 322, 0, 0, $qCodeWidth, $qCodeHight, 100);
         
        list($bigWidth, $bigHight, $bigType) = getimagesize($bigImgPath);
         
          
        switch ($bigType) {
            case 1: //gif
               // header('Content-Type:image/gif');
                $wximgpath=$dir.date('d').'-hb'.$ret['uid'].'.gif';
                imagegif($bigImg,$wximgpath);
                
                break;
            case 2: //jpg
               // header('Content-Type:image/jpg');
               $wximgpath=$dir.date('d').'-hb'.$ret['uid'].'.jpg'; 
               imagejpeg($bigImg,$wximgpath);
              
                break;
            case 3: //jpg
                //header('Content-Type:image/png');
                $wximgpath=$dir.date('d').'-hb'.$ret['uid'].'.png';
                imagepng($bigImg,$wximgpath);
                 
                break;
            default:
                # code...
                break;
        }
         
        imagedestroy($bigImg);
        imagedestroy($qcodeImg);

         
        $msg=$wxApi->uploadmedia('image',$wximgpath);
         
        echo $msg;exit;

	 }
	  private function dlfile($file_url, $save_to)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 0); 
        curl_setopt($ch,CURLOPT_URL,$file_url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $file_content = curl_exec($ch);
        curl_close($ch);
        $downloaded_file = fopen($save_to, 'w');
        fwrite($downloaded_file, $file_content);
        fclose($downloaded_file);
   }
}