<?php
header("Content-type:text/html;charset=utf-8");

//$cache = CacheFile::cache('test','ok',180);
var_dump(CacheFile::cache('test'));

class CacheFile{
    //文件存储位置
    public static $filePath = S_PATH.'/data/data_cache/';
    //运行时实例
    public static $instance;
    //运行时内存式缓存
    public $runCache = '';    

    /**
     * 文件缓存
     * $cache_key 索引	
     * $cache_val 值 （此值空则读取缓存）
     * $expire 过期时间秒
     */
    public static function cache($cache_key, $cache_val='', $expire=3600) {
        $file = self::$filePath . $cache_key .'.json';
        //读取
        if ($cache_val == '') {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $cache = json_decode($content, true);
                if (!isset($cache['expire']) || time() > $cache['expire']) {                   
                    return '';
                }else{
                    return $cache['data'];
                }
            } else {
                return '';
            }
        } else {
            $info = array('expire' => time() + $expire, 'data' => $cache_val);            
            return file_put_contents($file, json_encode($info));
        }
    }
    /**
     * DB缓存
     * $db 数据库连接	
     * $sql 查询的sql
     * $func 查询使用的方法 getAll getOne getCol getRows
     * $index 使用getAll等式使用的索引
     * $expire 过期时间秒
     */
    public static function sql($db,$sql,$func='getAll',$index='',$expire=3600){
        $key = md5($sql);
        $cache = self::cache($key);
        if($cache == ''){
            $cache = $db->$func($sql,$index);
            self::cache($key,$cache,$expire);
        }
        return $cache;
    }
    /**
     * 单例模式	
     */
     public static function single(){
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
     }
    /**
     * $db 数据库连接	
     * $sql 查询的sql
     * $func 查询使用的方法 getAll getOne getCol getRows
     * $index 使用getAll等式使用的索引
     */
    public function RAMsql($db,$sql,$func='getAll',$index=''){
        $key = md5($sql);
        $cache = $this->runCache[$key];
        if($cache == ''){
            $cache = $db->$func($sql,$index);
            $this->runCache[$key] = $cache;
        }
        return $cache;
    }
}

?>
