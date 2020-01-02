<?php
header("Content-type:text/html;charset:utf-8;");
date_default_timezone_set('Asia/Shanghai');
/* 
 * 咪咕云视频接口
 */
define("DEBUG",0);
define("TEST",1);

include('getToken.php');

if(TEST){
    include('conf.php');
    $apiHost = 'http://test.migucloud.com';
}else{
    include('conf_pro.php');
    $apiHost = 'http://api.migucloud.com';
}

$mod = isset($_GET['mod']) ? $_GET['mod'] : '';
$API = new miguApi();

if($mod == 'apiId'){
    $url = $apiHost.'/internalApi';
    $param = array('uid'=>$conf['uid']);
    $res = $API->getApiId($url,$param);
    echo json_encode($res);
    exit;
}
if($mod == 'created'){   
    $res = $API->createLive($_POST['url'],$_POST['formData']);
    echo json_encode($res);
    exit;
}
if($mod == 'createpef'){      
    //POST数据
    $dataPost = file_get_contents('php://input');
    $url = $apiHost.'/l2/live/createChannel?';
    $urlToken = getTokenUrl($url,$conf);
    $res = $API->createLive($urlToken,$dataPost);
    echo json_encode($res);
    exit;
}
//直播列表
if($mod == 'list'){
    $dataPost = file_get_contents('php://input');
    $url = $apiHost.'/l2/live/listChannel?';
    $urlToken = getTokenUrl($url,$conf);
    $res = $API->liveList($urlToken,$dataPost);
    echo json_encode($res);
    exit;
}
//推流地址
if($mod == 'push'){
    $channel_id = $_GET['channel_id'];
    $url = $apiHost.'/l2/addr/getPushUrl?';
    $urlToken = getTokenUrl($url,$conf,array('channel_id'=>$channel_id));
    $urlToken .= '&channel_id='.$channel_id;
    $res = $API->livePushUrl($urlToken);
    echo json_encode($res);
    exit;
}
if($mod == 'play'){
    $channel_id = $_GET['channel_id'];
    $url = $apiHost.'/l2/addr/getPullUrl?';
    $urlToken = getTokenUrl($url,$conf,array('channel_id'=>$channel_id));
    $urlToken .= '&channel_id='.$channel_id;
    $res = $API->livePlayUrl($urlToken);
    echo json_encode($res);
    exit;
}
if($mod == 'update'){
    $dataPost = file_get_contents('php://input');
    $url = $apiHost."/l2/live/updateChannel?";
    $urlToken = getTokenUrl($url,$conf);
    $res = $API->updateLive($urlToken,$dataPost);
    echo json_encode($res);
    exit;
}
if($mod == 'del'){
    $dataPost = file_get_contents('php://input');
    $url = $apiHost."/l2/live/removeChannel?";
    $urlToken = getTokenUrl($url,$conf);
    $res = $API->delLive($urlToken,$dataPost);
    echo json_encode($res);
    exit;
}
if($mod == 'playPull'){
    $channelId = $_GET['channelId'];
    $url = $apiHost.'/l2/ingest/startIngest?';
    $urlToken = getTokenUrl($url,$conf,array('channelId'=>$channelId));
    $urlToken .= '&channelId='.$channelId;
    $res = $API->playPull($urlToken);
    echo json_encode($res);
    exit;
}
if($mod == 'stopPull'){
    $channelId = $_GET['channelId'];
    $url = $apiHost.'/l2/ingest/stopIngest?';
    $urlToken = getTokenUrl($url,$conf,array('channelId'=>$channelId));
    $urlToken .= '&channelId='.$channelId;
    $res = $API->stopPull($urlToken);
    echo json_encode($res);
    exit;
}
if($mod == 'info'){
    $channelId = $_GET['channelId'];
    $url = $apiHost.'/l2/live/getChannel?';
    $urlToken = getTokenUrl($url,$conf,array('id'=>$channelId));
    $urlToken .= '&id='.$channelId;
    $res = $API->info($urlToken);
    echo json_encode($res);
    exit;
}

class miguApi {
    //获取apiId
    public function getApiId($url,$param){
        $apiPar = array('mod'=>'Cas','act'=>'UserPermission','uid'=>$param['uid']);
        $res = curlPost($url,$apiPar);
        return $res;
    }
    //创建直播
    public function createLive($url,$param){
        if(DEBUG){
            echo $url."\r\n\r\n";
            echo $param."\r\n\r\n";
            exit;
        }
        $res = curlPost($url,$param,true);
        //$res = curlHttp($url,$param);
        return $res;
    }
    public function updateLive($url,$param){
        $res = curlPost($url,$param,true);
        return $res;
    }
    public function liveList($url,$param){
        $res = curlPost($url,$param,true);
        return $res;
    }
    public function livePushUrl($url){        
        $res = curlGet($url);
        return $res;
    }
    public function livePlayUrl($url){
        $res = curlGet($url);
        return $res;
    }
    public function delLive($url,$param){
        $res = curlPost($url,$param,true);
        return $res;
    }
    public function playPull($url){
        $res = curlGet($url);
        return $res;
    }
    public function stopPull($url){
        $res = curlGet($url);
        return $res;
    }
    public function info($url){
        $res = curlGet($url);
        return $res;
    }
}
//获取Token URL
function getTokenUrl($url,$conf,$extra = array()){
    $confUrlGet = array(
        'uid' => $conf['uid'],
        'secretId' => $conf['secretId'],
        'secretkey' => $conf['secretkey'],
        'currentTimeStamp' => getMillisecond(0),            
        'random' => '12345678',
        'apiId' => $_GET['apiId'],
        'grant_type' => $conf['grantType'],
        'expired' => getMillisecond(120)
    );
    if(!empty($extra)){
        foreach($extra AS $key => $val){
            $confUrlGet[$key] = $val;
        }
    }
    $token = new getToken();
    $tokenInfo = $token ->getSign($confUrlGet);
    if($tokenInfo['ret'] != 0){
        exit($tokenInfo['msg']);
    }  
    //构建请求URL    
    $col = array('uid'=>'uid','currentTimeStamp'=>'currentTimeStamp','expired'=>'expired',
        'grant_type' =>'grant_type','secretId'=>'secretId','random'=>'random','apiId'=>'apiId');    
    foreach($col AS $key => $val){
        $url .= $key.'='.$confUrlGet[$val].'&';
    }
    $url .= 'token='.$tokenInfo['result']['token'];
    return $url;
}
//发送POST请求
function curlPost($url,$post_data,$json = false){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);    
    // 关闭SSL验证
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);    
    //post提交的数据
    if($json){        
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json;charset="utf-8"'));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    }else{
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    }        
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    if($data == false){
        return array('curlErr'=>1,'msg'=>curl_error($curl));
    }else{
        return json_decode($data,true);
    }
}

function curlGet($url){        
    //$url = "{$url}?" . http_build_query ( $params );
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_HEADER, 0);
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
    curl_setopt ( $ch, CURLOPT_TIMEOUT, 12 );
    $data = curl_exec ( $ch );
    curl_close ( $ch );
    return json_decode($data,true);
}
