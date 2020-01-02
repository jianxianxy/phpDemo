<?php
header("Content-type:text/html;charset=utf-8");
include('conf_db.php');
$db = isset($_POST['db']) ? intval($_POST['db']) : 0;
$key = isset($_POST['key']) ? trim($_POST['key']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : 'string';
$index = isset($_POST['index']) ? trim($_POST['index']) : '';

if($key == ''){
    ajaxRet('无效请求');
}

$Redis = new Redis();  
$Redis->connect($conf['host'],$conf['port']);
$Redis->auth($conf['auth']);
$Redis->select($db);  

if($type == 'string'){
    $value = $Redis->get($key);
    ajaxRet($value);
}else if($type == 'hash'){
    if($index){
        $value = $Redis->hGet($key,$index);
    }else{
        $value = $Redis->hGetAll($key);
    }
    //nop_basetopar_ali_530  1_47912892
    ajaxRet($value);
}else if($type == 'list'){
    $start = !empty($_POST['start']) ? intval($_POST['start']) : 0;
    $end = !empty($_POST['end']) ? intval($_POST['end']) : -1;
    $len = $Redis->lLen($key);
    $value = $Redis->lrange($key, $start, $end);
    echo '长度:'.$len."\r\n";
    ajaxRet($value);
}else if($type == 'set'){
    $value = $Redis->sMembers($key);
    ajaxRet($value);    
}else if($type == 'zset'){
    $start = !empty($_POST['start']) ? intval($_POST['start']) : 0;
    $end = !empty($_POST['end']) ? intval($_POST['end']) : -1;
    $value = $Redis->zRange($key,$start,$end,true);
    ajaxRet($value);
}

function ajaxRet($val){ 
    if(is_array($val)){
        foreach($val AS $k => $v){
            echo $k." => ";
            ajaxRet($v);
        }
    }else{
        if(isJson($val)){
            $val = json_decode($val,true);
        }
        var_export($val);
        echo "\r\n";
    }
}

function isJson($str){ 
    return !is_null(json_decode($str));
}