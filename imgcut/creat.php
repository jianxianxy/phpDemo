<?php
header("Content-type:text/html;charset=utf-8;");
include 'ImageHandle.php';

$path = isset($_GET['path']) ? $_GET['path'] : '1.jpg';
$width = isset($_GET['w']) ? $_GET['w'] : 200;
$height = isset($_GET['h']) ? $_GET['h'] : 200;

$ic=new ImageHandle('base.jpg',$path);

//$ic->Cut(200,200,0,0); //图片裁切
//$ic->SaveAlpha();将补白变成透明像素保存
$ic->Crop($width,$height,2); //缩略图
$ic->SaveImage($path,75); //保存图片
$ic->OutImage(); //输出图片
$ic->destory(); //释放资源


