<?php
echo round(memory_get_usage()/(1024*1024),3);
exit;
//error_reporting(0);
include('function.php');
$col = array("星星",'区域');
$data = array(
    array('name' => '地球','area' => '太阳系'),
    array('name' => '太阳系','area' => '银河系'),
);
exportCsv($data,$col,'星系');
?>