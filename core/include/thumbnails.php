<?php    
  
    $pic_name=date("dMYHis");    
  
    // 生成图片的宽度    
    $pic_width=$_POST['width'];   
  
    // 生成图片的高度    
    $pic_height=$_POST['length'];    
  
    function ResizeImage($im,$maxwidth,$maxheight,$name){    
        //取得当前图片大小   
        $width = imagesx($im);    
        $height = imagesy($im);    
        //生成缩略图的大小   
        if(($width > $maxwidth) || ($height > $maxheight)){    
            $widthratio = $maxwidth/$width;        
            $heightratio = $maxheight/$height;     
            if($widthratio < $heightratio){    
                $ratio = $widthratio;    
            }else{    
                $ratio = $heightratio;    
            }    
            $newwidth = $width * $ratio;    
            $newheight = $height * $ratio;    
           
            if(function_exists("imagecopyresampled")){    
                $newim = imagecreatetruecolor($newwidth, $newheight);    
                imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);    
            }else{    
                $newim = imagecreate($newwidth, $newheight);    
                imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);    
            }    
            ImageJpeg ($newim,$name . ".jpg");    
            ImageDestroy ($newim);    
        }else{    
            ImageJpeg ($im,$name . ".jpg");    
        }    
    }    
  
    if($_FILES['image']['size']){    
        //echo $_FILES['image']['type'];   
        if($_FILES['image']['type'] == "image/pjpeg"||$_FILES['image']['type'] == "image/jpg"||$_FILES['image']['type'] == "image/jpeg"){    
            $im = imagecreatefromjpeg($_FILES['image']['tmp_name']);    
        }elseif($_FILES['image']['type'] == "image/x-png"){    
            $im = imagecreatefrompng($_FILES['image']['tmp_name']);    
        }elseif($_FILES['image']['type'] == "image/gif"){    
            $im = imagecreatefromgif($_FILES['image']['tmp_name']);    
        }   
		
        if($im){    
            if(file_exists($pic_name.'.jpg')){    
                unlink($pic_name.'.jpg');    
            }  
			echo $im;
            ResizeImage($im,$pic_width,$pic_height,$pic_name);    
            ImageDestroy ($im);    
        }    
    }    
/* print "<script language='javascript'>parent.document.getElementById('$this->PicName_Id').value='$this->Pic_Path';location.replace('index.php?PicName_Id=$this->PicName_Id&Iframe=$this->Iframe&Is_ReSet=2&PicNames=$this->Pic_Path');</script>";*/
?>   
<img src="<?php echo $pic_name.'.jpg'; ?>"><br><br>    
<form enctype="multipart/form-data" method="post" action="thumbnails.php">    
<br>    
<input type="text" name="oo" size="50" value="1">
<input type="file" name="image" size="50" value="浏览"><p>    
生成缩略图宽度：<input type="text" name="width" size="5"  value="100"><p>   
生成缩略图长度：<input type="text" name="length" size="5" value="100"><p>   
<input type="submit" value="上传图片">    
</form>  