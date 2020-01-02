<?php
header("Content-type:text/html;charset=utf-8;");
/**
 * 字母+数字的验证码生成
 */
// 开启session
session_start();
$img_w = 130;
$img_h = 40;
//1.创建黑色画布
$image = imagecreatetruecolor($img_w,$img_h);

//2.为画布定义(背景)颜色
$bgcolor = imagecolorallocate($image, 255, 255, 255);

//3.填充颜色
imagefill($image, 0, 0, $bgcolor);

// 4.设置验证码内容

//4.1 定义验证码的内容
$content = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghjklmnopqrstuvwxyz23456789";
$mod1 = array('12','减','30','=');
$mod2 = array('你','再','西','洞');
//4.1 创建一个变量存储产生的验证码数据，便于用户提交核对
$captcha = "";
for ($i = 0; $i < 4; $i++) {
    // 字体大小
    $fontsize = 10;
    // 字体颜色
    $fontcolor = imagecolorallocate($image, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120));
    // 设置字体内容
    //$fontcontent = substr($content, mt_rand(0, strlen($content)), 1);
	$fontcontent = $mod2[$i];
    $captcha .= $fontcontent;
    // 显示的坐标
    $x = ($i * $img_w / 4) + mt_rand(5, 10);
    $y = mt_rand(5, 10);
    // 填充内容到画布中
    //imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
	imagettftext($image, mt_rand(14,18), mt_rand(30,80), (30*$i+20), mt_rand(20,30), $fontcolor,'STXINWEI.TTF', $fontcontent);  
}
$_SESSION["captchaimg"] = $captcha;

//4.3 设置背景干扰元素
for ($$i = 0; $i < 200; $i++) {
    $pointcolor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
    imagesetpixel($image, mt_rand(1,$img_w), mt_rand(1,$img_h), $pointcolor);
}


//4.4 设置干扰线
for ($i = 0; $i < 3; $i++) {
    $linecolor = imagecolorallocate($image, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
    imageline($image, mt_rand(1, $img_w), mt_rand(1, $img_h), mt_rand(1, $img_w), mt_rand(1, $img_h), $linecolor);
}

//5.向浏览器输出图片头信息
header('content-type:image/png');

//6.输出图片到浏览器
imagepng($image);

//7.销毁图片
imagedestroy($image);　

?>