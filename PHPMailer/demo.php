<?php
header("Content-type:text/html;charset=utf-8;");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$pop3 = array('server'=>'pop.qq.com','port'=>995);
$stmp = array('server'=>'smtp.qq.com','port'=>[465,587]);

require './src/Exception.php';
require './src/OAuth.php';
require './src/PHPMailer.php';
require './src/POP3.php';
require './src/SMTP.php';


$mail = new PHPMailer(true);

try {
    //Server配置
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host       = 'smtp.qq.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = '***@x.com';
    $mail->Password   = 'khpwjphnpetscbae';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
	$mail->CharSet    = 'utf-8';

    //收发信息
    $mail->setFrom('***@x.com', '无双');
    $mail->addAddress('reveive@126.com', '用户');     
    $mail->addReplyTo('***@x.com', '回复');


    //附件
    //$mail->addAttachment('/var/tmp/file.tar.gz');        
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');

    //内容
    $mail->isHTML(true);
    $mail->Subject = '激活';
    $mail->Body    = '<b><a href="http://www.baidu.com">链接</a></b>';

    $mail->send();
    echo '发送成功';
} catch (Exception $e) {
    echo "发送失败: {$mail->ErrorInfo}";
}
?>