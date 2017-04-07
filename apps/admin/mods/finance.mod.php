<?php
class FinanceMod extends BaseMod{
    private $data;
    public function __construct(){
        $this->data = Factory::getData('finance');
    }


    public function getFinance($start,$end){
        $start = empty($start) ? strtotime(date('Y-m-01')) : strtotime($start);
        $end = empty($end) ? time() : strtotime($end.'23:59:59');
        $info = $this->data->getFinance($start,$end);

        $time = array();
        $data = array();
        foreach($info as $val){
            $t = $val['t'] == date('Y-m-d') ? '今天' : $val['t'];
            $time[] = $t;
            $data[] = intval($val['total']);
        }
        $total = array_sum($data);
        $time = json_encode($time);
        $data = json_encode($data);
        return array(
            'total' => $total,
            'time' => $time,
            'data' => $data,
            'start' => date('Y-m-d',$start),
            'end' => date('Y-m-d',$end),
        );
    }

    /**
     * 消费记录
     * @param $start
     * @param $end
     * @param int $page
     * @param string $num
     * @param string $keyword
     * @return mixed
     */
    public function consume($start,$end,$page=1,$num='',$keyword=''){
        $start = empty($start) ? strtotime(date('Y-m-01')) : strtotime($start);
        $end = empty($end) ? time() : strtotime($end.'23:59:59');
        $info = $this->data->consume($start,$end,$page,$num,$keyword);
        $info['start'] = date('Y-m-d',$start);
        $info['end'] = date('Y-m-d',$end);
        include_once COMMON_PATH.'libs/LibIp.php';
        $ip = new LibIp();
        foreach($info['list'] as $val){
            $address = $ip->getlocation(long2ip($val['ip']));
            $val['address'] = $address['country'];
        }
        $info['total_money'] = empty($info['total_money']) ? 0 : $info['total_money'];
        return $info;
    }

    /**
     * 充值记录
     * @param string $start
     * @param string $end
     * @param int $page
     * @param string $num
     * @param string $keyword
     * @return mixed
     */
    public function recharge($start='',$end='',$page=1,$num='',$keyword=''){
        $start = empty($start) ? strtotime(date('Y-m-01')) : strtotime($start);
        $end = empty($end) ? time() : strtotime($end.'23:59:59');
        $info = $this->data->recharge($start,$end,$page,$num,$keyword);
        $info['start'] = date('Y-m-d',$start);
        $info['end'] = date('Y-m-d',$end);
        return $info;
    }


    public function download($list){
        $filename =  '消费记录('.date('Y-m-d').').xls';
        $headerArray = array( '消费内容', '价格','数量','用户','购买时间','幸运号') ;

        require_once  COMMON_PATH.'libs/excel/PHPExcel.php';
        if ( $filename && !empty( $headerArray ) ) {
            $excelObj = new PHPExcel;// 设置excel文档的属性
            $excelObj->getProperties()->setCreator( "wtf" )->setLastModifiedBy( "wtf" )->setTitle( "Microsoft Office Excel Document" )->setSubject( "Daily data" )->setDescription( "Daily data" )->setKeywords( "Daily data" )->setCategory( "Daily data result file" );
            // 开始操作excel表
            $excelObj->setActiveSheetIndex( 0 );// 操作第一个工作表
            $excelObj->getActiveSheet()->setTitle( iconv( 'gbk', 'utf-8', 'sheet_1' ) );// 设置工作薄名称
            $excelObj->getDefaultStyle()->getFont()->setName( iconv( 'gbk', 'utf-8', '宋体' ) ); // 设置默认字体和大小
            $excelObj->getDefaultStyle()->getFont()->setSize( 10 );
            // 设置表头
            $excelObj->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//设置A1 - I1 水平居中
            $excelObj->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);//A1到I1字体
            $excelObj->getActiveSheet()->getStyle('A1:F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $excelObj->getActiveSheet()->getStyle('A1:F1')->getFill()->getStartColor()->setARGB('00DBDBDB'); //A1到I1填充颜色

            $a = 'A';
            foreach( $headerArray as $v ) {
                if($a=='A'){
                    $width = 53;
                }elseif($a == 'D'){
                    $width = 40;
                }else{
                    $width = 25;
                }
                $excelObj->getActiveSheet()->getColumnDimension($a)->setWidth($width);
                $excelObj->getActiveSheet()->setCellValue( $a . '1', $v );
                $a++;
            }
            // 填充表单数据
            if ( !empty($list) && is_array($list) ) {
                $i = 2;
                foreach($list as $val) {
                    $j = 'A';
                    $excelObj->getActiveSheet()->getRowDimension($i)->setRowHeight(30);//行高
                    $excelObj->getActiveSheet()->getStyle($i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $excelObj->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $excelObj->getActiveSheet()->setCellValue( $j++ . $i, $val['title']);
                    $excelObj->getActiveSheet()->setCellValue( $j++ . $i, 1);
                    $excelObj->getActiveSheet()->setCellValue( $j++ . $i, $val['this_num']);
                    $excelObj->getActiveSheet()->setCellValue( $j++ . $i, $val['nick']);
                    $excelObj->getActiveSheet()->setCellValue( $j++ . $i, date('Y-m-d H:i:s',$val['rt']));
                    $excelObj->getActiveSheet()->setCellValue( $j . $i, $val['activity_num']);
                    $i++;
                }
            }
            // 从浏览器直接输出$filename
            $objWriter = PHPExcel_IOFactory::createWriter( $excelObj, 'Excel5' );// 设置导出文件格式为excel
            header( "Pragma: public" );
            header( "Expires: 0" );
            header( "Cache-Control:must-revalidate, post-check=0, pre-check=0" );
            header( "Content-Type:application/force-download" );
            header( "Content-Type: application/vnd.ms-excel;" );
            header( "Content-Type:application/octet-stream" );
            header( "Content-Type:application/download" );
            header( "Content-Disposition:attachment;filename=" . $filename );
            header( "Content-Transfer-Encoding:binary" );
            $objWriter->save( "php://output" );
        }
    }

    public function yijian($page,$num){
        return $this->data->yijian($page,$num);
    }

    public function notice($page,$num){
        return $this->data->notice($page,$num);
    }
    
    public function notice_info($id){
        if(!$id) return array();
        return $this->data->notice_info($id);
    }

    public function notice_save($data,$id){
        if(empty($data['title'])){
            return array('state' => 0,'msg' => '请输入标题');
        }
        if(empty($data['content'])){
            return array('state' => 0,'msg' => '请输入内容');
        }
        $data['time'] = time();

        $res = $this->data->notice_save($data,$id);
        if($res){
            return array('state' => $res,'msg' => '保存成功！');
        }else{
            return array('state' => $res,'msg' => '保存失败！');
        }

    }

    public function noticeZiding($id){
        if(!$id) return array();
        $res =  $this->data->noticeZiding($id);
        if($res){
            return array('state' => $res,'msg' => '操作成功！');
        }else{
            return array('state' => $res,'msg' => '操作失败！');
        }
    }

    public function noticeQuxiaoziding($id){
        if(!$id) return array();
        $res =  $this->data->noticeQuxiaoziding($id);
        if($res){
            return array('state' => $res,'msg' => '操作成功！');
        }else{
            return array('state' => $res,'msg' => '操作失败！');
        }
    }

    public function noticeShanchu($id){
        if(!$id) return array();
        $res =  $this->data->noticeShanchu($id);
        if($res){
            return array('state' => $res,'msg' => '操作成功！');
        }else{
            return array('state' => $res,'msg' => '操作失败！');
        }
    }
    /**
     * 退款记录
     * @param int $page
     * @param string $num
     * @param string $keyword
     * @return mixed
     */
    public function refundlist($page=1,$num='',$keyword=''){
        $info = $this->data->refundlist($page,$num,$keyword); 
        return $info;
    }

}