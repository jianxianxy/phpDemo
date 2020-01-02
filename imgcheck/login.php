<?php
/**
 * 接受用户登陆时提交的验证码
 */
session_start();
//1. 获取到用户提交的验证码
$captcha = $_POST["captcha"];
$aaa=$_SESSION["captchaimg"];
echo "提交的:$captcha<br>";
echo "session里面的:$aaa<br>";
//2. 将session中的验证码和用户提交的验证码进行核对,当成功时提示验证码正确，并销毁之前的session值,不成功则重新提交
if(strtolower($aaa) == strtolower($captcha)){
    echo "验证码正确!";
    $_SESSION["captcha"] = "";
}else{
    echo "验证码提交不正确!";
}

?>