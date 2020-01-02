<?php
date_default_timezone_set('Asia/Shanghai');


echo getMillisecond(0);
echo '<br/>';
echo getMillisecond(120);
exit;

$file = 'E:/app/SDK1.0/yjs.mp4';
echo md5_file($file);


function getMillisecond($exps){
    list($msec, $sec) = explode(' ', microtime());
    if($exps < 1){
        $msectime =  (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }else{
        $msectime =  (float)sprintf('%.0f', (floatval($msec) + 60*$exps + floatval($sec)) * 1000);
    }
    $msectimes = substr($msectime,0,13);
    return $msectimes;
}
?>