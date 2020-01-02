<?php


/**
 * 文件缓存
 * $name 文件名称	
 * $cache_val 文件内数组值 （此值空则读取缓存）
 * $expire 过期时间秒
 * $append true追加模式 false替换模式
 */
function fileCache($name,$cache_val = '',$expire = 0,$append = false){    
    $file_dir = date('YhdH').'/';
    $file = $file_dir.$name.'.json';
    $dir_path = dirname($file);
    if(!is_dir($dir_path))
    {
        mkdir($dir_path, 0777, true);
    }
    //删除
    if($expire == -1){
        if(file_exists($file)){
            unlink($file);
        }
        return true;
    }
    //读取
    if($cache_val == ''){
        if(file_exists($file)){            
            $content = file_get_contents($file); 
            $info = json_decode($content,true);
            if(isset($info['expire']) && time() > $info['expire']){
                unlink($file);
                return '';
            }
            file_put_contents($file, json_encode($info));
            return $info['data'];
        }else{
            return '';
        }
    }else{
        if(file_exists($file)){
            $content = file_get_contents($file);
            $info = json_decode($content,true);
            if(is_array($info) && $append){                
                $info['time'] = time();
                if(is_array($info['data']) && $cache_val){
                    foreach($cache_val AS $key => $val){
                        $info['data'][$key] = $val;
                    }
                }else{
                    $info['data'] = $cache_val;
                }                
            }else{
                $info = array('time' => time(),'data' => $cache_val);
            }             
        }else{
            $info = array('time' => time(),'data' => $cache_val);   
        }
        if($expire){
            $info['expire'] = time() + $expire;
        }
        return file_put_contents($file, json_encode($info));
    }
}

/**
 * 文件缓存
 * $cache_key 索引	
 * $cache_val 值 （此值空则读取缓存）
 * $expire 过期时间秒
 */
function commCache($cache_key, $cache_val = '', $expire = 3600) {
    $file_dir = './';
    $file = $file_dir . 'commCache.json';
    $dir_path = dirname($file);
    if (!is_dir($dir_path)) {
        mkdir($dir_path, 0777, true);
    }
    //读取
    if ($cache_val == '') {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $info = json_decode($content, true);
            if(isset($info[$cache_key])){
                $cache = $info[$cache_key];
                if (isset($cache['expire']) && time() > $cache['expire']) {                   
                    return '';
                }else{
                    return $cache['data'];
                }
            }
            return '';
        } else {
            return '';
        }
    } else {
        $inArr = array('expire' => time() + $expire, 'data' => $cache_val);
        $info = '';
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $info = json_decode($content, true);
        }
        $info[$cache_key] = $inArr;
        return file_put_contents($file, json_encode($info));
    }
}


/**
 * 字符串加密解密
 * $string 待加密 或 解密的字符串	
 * $key 秘钥
 * $operation DECODE:解密,Other:加密
 * $expiry 过期时间秒
 */

function authSecretKey($string,$key='',$operation='',$expiry=3600) {
	$ckey_length = 4;   
	// 随机密钥长度 取值 0-32;
	// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
	// 取值越大，密文变动规律越大，密文变化为 16 的 $ckey_length 次方

	$key = md5($key == '' ? 'wushuang.app' : $key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	if($operation == 'DECODE'){
		$keyc = substr($string, 0, $ckey_length);   	
	}else{
		$keyc = substr(md5(microtime()), -$ckey_length);	
	}
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	if($operation == 'DECODE'){
		$string = base64_decode(substr($string, $ckey_length));	
	}else{
		$string = sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;	
	}

	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
			substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

/**
 * 获取客户端真实IP
 */
function getClientIp(){
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($arr AS $ip) {
                $ip = trim($ip);
                if ($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $realip = $_SERVER['REMOTE_ADDR'];
            } else {
                $realip = '0.0.0.0';
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
    return $realip;
}

/**
 * 导出数据到CSV文件
 * @param array $data  数据
 * @param array $title_arr 标题
 * @param string $file_name CSV文件名
 */
function exportCsv(&$data, $title_arr, $file_name = '') {   
    ini_set("max_execution_time", "3600");
    $csv_data = '';
    //标题
    $nums = count($title_arr);
    for ($i = 0; $i < $nums - 1; ++$i) {
        $csv_data .= '"' . iconv('utf-8','GBK',$title_arr[$i]) . '",';
    }
    if ($nums > 0) {
        $csv_data .= '"' . iconv('utf-8','GBK',$title_arr[$nums - 1]) . "\"\r\n";
    }
    //数据
    foreach ($data as $k => $row) {
        $row_values = array_values($row);
        for ($i = 0; $i < $nums - 1; ++$i) {    
            $row_values[$i] = str_replace("\"", "\"\"", iconv('utf-8','GBK',$row_values[$i]));
            $csv_data .= '"' . $row_values[$i] . '",';
        }

        $csv_data .= '"' . str_replace("\"", "\"\"", iconv('utf-8','GBK',$row_values[$nums - 1])) . "\"\r\n";
        unset($data[$k]);
    }

    $file_name = empty($file_name) ? date('Y-m-d') : $file_name;
    // 解决IE浏览器输出中文名乱码的bug
    if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) { 
        $file_name = urlencode($file_name);
        $file_name = str_replace('+', '%20', $file_name);
    }
    $file_name = $file_name . '.csv';
    header("Content-type:text/csv;");
    header("Content-Disposition:attachment;filename=" . $file_name);
    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    header('Expires:0');
    header('Pragma:public');
    echo $csv_data;
}

?>