<?php
class editorMod extends BaseMod{

    public function editor($action){

        $config = array (
            // 上传图片配置
            'imageActionName' => 'uploadimage',
            'imageFieldName' => 'upfile',
            'imageMaxSize' => 2048000,
            'imageAllowFiles' => array ('.png', '.jpg', '.jpeg', '.gif', '.bmp'),
            'imageCompressEnable' => true,
            'imageCompressBorder' => 1600,
            'imageInsertAlign' => 'none',
            'imageUrlPrefix' => '',
            'imagePath' => UPLOAD_PATH,
            'imageUrl' =>  PIC_UPLOAD_URL,

            // 上传文件配置
            'fileActionName' => 'uploadfile',
            'fileFieldName' => 'upfile',
            'fileMaxSize' => 10240000,
            'fileUrlPrefix' => '',
            'fileAllowFiles' => array(
                ".png", ".jpg", ".jpeg", ".gif", ".bmp",
                ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
                ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
                ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
                ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
            ),
            'filePath' => UPLOAD_PATH,
            'fileUrl' =>  PIC_UPLOAD_URL,

            // 列出图片列表配置
            'imageManagerActionName'    => 'listimage',
            'imageManagerListPath'      => UPLOAD_PATH,
            'imageManagerUrl'           =>  PIC_UPLOAD_URL,
            'imageManagerListSize'      => 20,
            'imageManagerUrlPrefix'     => '',
            'imageManagerInsertAlign'   => 'none',
            'imageManagerAllowFiles'    => array(".png", ".jpg", ".jpeg", ".gif", ".bmp"),

            // 列出文件列表配置
            'fileManagerActionName'     => 'listfile',
            'fileManagerListPath'       => UPLOAD_PATH,
            'fileManagerUrl'            =>  PIC_UPLOAD_URL,
            'fileManagerListSize'       => 20,
            'fileManagerUrlPrefix'      => '',
            'fileManagerAllowFiles'     => array(
                ".png", ".jpg", ".jpeg", ".gif", ".bmp",
                ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
                ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
                ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
                ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
            ),

        );

        $result = false;

        if($action == 'config'){
            $result = $config;
        } else {
            $mission = '';

            switch($action){
                case 'uploadimage':
                    $mission = 'upload';
                    $config  = array(
                        "path"          => $config['imagePath'],
                        "url"           => $config['imageUrl'],
                        "maxSize"       => $config['imageMaxSize'],
                        "allowFiles"    => $config['imageAllowFiles']
                    );
                    break;
                case 'uploadfile':
                    $mission = 'upload';
                    $config  = array(
                        "path"          => $config['filePath'],
                        "url"           => $config['fileUrl'],
                        "maxSize"       => $config['fileMaxSize'],
                        "allowFiles"    => $config['fileAllowFiles']
                    );
                    break;
                case 'listfile':
                    $mission = 'list';
                    $config  = array(
                        "allowFiles"    => $config['fileManagerAllowFiles'],
                        "listSize"      => $config['fileManagerListSize'],
                        "path"          => $config['fileManagerListPath'],
                        "url"           => $config['fileManagerUrl'],
                    );
                    break;
                case 'listimage':
                    $mission = 'list';
                    $config  = array(
                        "allowFiles"    => $config['imageManagerAllowFiles'],
                        "listSize"      => $config['imageManagerListSize'],
                        "path"          => $config['imageManagerListPath'],
                        "url"           => $config['imageManagerUrl'],
                    );
                    break;
            }

            if($mission == 'upload'){
                /* 生成上传实例对象并完成上传 */
                $result = self::uploader($config);
            }else if($mission == 'list'){
                $result = self::getList($config);
            }
        }

        return $result;
    }

    private static function uploader($config){
        include_once COMMON_PATH.'libs/LibFile.php';
        //编辑器上传图片配置
        $size = $config['maxSize'];
        $ext = $config['allowFiles'];
        $title = '';
        $re = LibFile::upload('upfile', UPLOAD_PATH, PIC_UPLOAD_URL, $size, $ext);
        $state = "";
        $file = $_FILES['upfile']['name'];
        switch ($re['msg']) {
            case "EXT_ERR":
                $state = "不支持的图片类型！";
                break;
            case "SIZE_OVER":
                $state = "图片大小超出限制！";
                break;
            case "UPLOAD_ERROR":
                $state = "图片保存失败！";
                break;
            case "UPLOAD_SUCCESS":
                $state = "SUCCESS";
                break;
        }

        return array(
            'url'       => UPLOAD_URL.$re['url'],
            'title'     => $title,
            'original'  => $file,
            'state'     => $state
        );
        //exit(json_encode(array('url'=>isset($re['url'])?$re['url']:'','title'=>$title,'state'=>isset($re['state'])?$re['state']:'')));
    }

    // 获得稳健列表
    private static function getList($config){
        $allowFiles = substr(str_replace(".", "|", join("", $config['allowFiles'])), 1);

        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $config['listSize'];
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;


        /* 获取文件列表 */
        $path = $config['path'] . (substr($config['path'], strlen($config['path'])-1, 1) == "/" ? "":"/");
        $url  = $config['url'] . (substr($config['url'], strlen($config['url'])-1, 1) == "/" ? "":"/");
        $files = LibFile::fileManage($path, $url, $allowFiles);
        if (!count($files)) {
            return array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            );
        }

        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }
        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}

        /* 返回数据 */
        return array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        );
    }
}