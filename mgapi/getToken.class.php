<?php
header("Content-type:text/html;charset:utf-8;");
date_default_timezone_set('Asia/Shanghai');
define("DEBUG",0);
//-----------php v2.0鉴权token生成的demo
$token = new getToken();
$token ->getSign();

class getToken
{
    protected static $_res = array('ret' => 0, 'msg' => '', 'result' => '');
    public function getSign()
    {
        $getdata = empty($_GET) ? [] : $_GET;
           //-----------判断请求方式
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {            
            //----------接收能力api业务参数
            $data_input = file_get_contents('php://input');
            if ($data_input && $this->isJson($data_input)) {
                //-----------------将请求的能力接口参数通过rawurlencode进行编码
                $postdata['body'] =rawurlencode($data_input);
            } else {
                self::$_res['ret'] = 1;
                self::$_res['msg'] = '请求数据不是json格式';
                $this->ajax_return(self::$_res);
            }
        }
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $postdata = [];
            $getdata['body'] = '%5B%5D';//如是get方式请求body字段值为%5B%5D（固定值）
        }
        $alldata = array_merge($getdata, $postdata);//合并数组
        $res=$this->_validate($alldata);//参数验证
            if ($res['ret'] == 0) {
                $paramdata=$res['result'];
                //---------生成签名
                $sign_array = $paramdata;
                //----------将不参数生成签名的元素进行销毁
                unset($sign_array['atoken']);
                unset($sign_array['ftoken']);
                unset($sign_array['api_path']);
                unset($sign_array['secretkey']);
                ksort($sign_array);//-----键按字典升序排列
                $sign_string = '';
                //------------拼接成字符串
                foreach ($sign_array as $k => $v) {
                    if (is_array($v)) {
                        $sign_string .= '&' . $k . '=' .json_encode($v);
                    } elseif (is_object($v)) {
                        $sign_string .= '&' . $k . '=' .json_encode($v);
                    } else {
                        if ($v === true) {
                            $a = 'true';
                        } elseif ($v === false) {
                            $a = 'false';
                        } else {
                            $a = $v;
                        }
                        $sign_string .= '&' . $k . '=' . $a;
                    }
                }
                if(DEBUG){
                    echo $sign_string."\r\n\r\n";
                    echo $paramdata['secretkey']."\r\n\r\n";
                }
                $sign_string = trim($sign_string, '&');
                $sign = hash_hmac('SHA256', $sign_string,$paramdata['secretkey'] );//加密方式
                self::$_res['result']['token']=$sign;
            }
           $this->ajax_return(self::$_res);

        }
    private function _validate ($params)
    {
        try {
            /* 校验参数 */
            if ( empty($params['secretId'])) {
                throw new \Exception('参数 secretId 未提供');
            }
            if (empty($params['currentTimeStamp'])) {
                throw new \Exception('参数 currentTimeStamp 未提供');
            }
            if (empty($params['expired'])) {
                throw new \Exception('参数 expired 未提供');
            }
            if (empty($params['random'])) {
                throw new \Exception('参数 random 未提供');
            }
            if (empty($params['apiId'])) {
                throw new \Exception('参数 apiId 未提供');
            }
            if (empty($params['grant_type'])) {
                throw new \Exception('参数 grant_type 未提供');
            }
            if(empty($params['uid'])){
                throw new \Exception('未获取uid');
            }
            if(!preg_match("/^\d*$/",trim($params['uid']))){
                throw new \Exception('uid无效');
            }
            if(empty($params['secretkey'])){
                throw new \Exception('未获取secretkey');
            }
        } catch (\Exception $e) {
            self::$_res['ret'] = 1;
            self::$_res['msg'] = $e->getMessage();
        }
        self::$_res['result'] = $params;
        return self::$_res;
    }
    public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    public function ajax_return($param){
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode( $param));
    }

}


