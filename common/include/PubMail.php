<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/1
 * Time: 20:58
 */

require_once(dirname(dirname(__FILE__)).'/libs/phpmailer/PHPMailerAutoload.php');

class PubMail{

    private $mail;

    public  function __construct(){

    $this->mail  = new PHPMailer;

    }

    public function send($toemail,$msg){

            $this->mail->IsSMTP();                            // 设定使用SMTP服务
            $this->mail->CharSet    ="UTF-8";                 //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置为 UTF-8
            $this->mail->SMTPAuth   = true;                   // 启用 SMTP 验证功能
            $this->mail->SMTPSecure = "ssl";                  // SMTP 安全协议
            $this->mail->Host       = "smtp.qq.com";       // SMTP 服务器
            $this->mail->Port       = 465;                    // SMTP服务器的端口号
            $this->mail->Username   = "524106731@qq.com";  // SMTP服务器用户名
            $this->mail->Password   = "ouminghai";        // SMTP服务器密码
            $this->mail->SetFrom("524106731@qq.com", '524106731@qq.com');    // 设置发件人地址和名称
            $this->mail->AddReplyTo("524106731@qq.com","524106731@qq.com");
            // 设置邮件回复人地址和名称
            $this->mail->Subject    = '点击链接继续验证账号';                     // 设置邮件标题
            $this->mail->AltBody    = "为了查看该邮件，请切换到支持 HTML 的邮件客户端";
            // 可选项，向下兼容考虑
            $this->mail->MsgHTML($msg);                         // 设置邮件内容
            $this->mail->AddAddress($toemail, $toemail);

            if (!$this->mail->send()) {
               return false;
            } else {
               return true;
            }

    }


}



