<?php
header("Content-type:text/html;charset=utf-8");
include('RsaService.php');

$rsa = new RsaService();
$data = "ok php";
$priStr = $rsa->privateEncrypt($data);
$rsa = new RsaService(false);
echo $rsa->publicDecrypt($priStr);
?>
