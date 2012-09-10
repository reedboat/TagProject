<?php
class WF_CacheUtil {
    function __construct( $cache) {
        $this->cache = $cache;
    }

    private $default_params = array();

    public function lock($key, $lifetime=10){
        return $this->cache->add($mutex_key, 1, $lifetime);
    }

    public function unlock($key) {
        $this->cache->set($mutext_key, 1, -1);
    }

    public function mutextLoad($params, $func, $args, $options=array()){
        extract($params);
        if ($this->lock($mutex_key)){

            $ret = call_user_func_array($func, $args);
            if ($ret === null || $ret === false) {return null;}

            $value = array('data'=>$ret, 'expire'=>time()+$data_lifetime);
            $this->cache()->set($key, json_encode($value), $cache_lifetime);

            $this->unlock($mutex_key);
            return $ret;
        }
        else {
            return false;
        }
    }

    public function callByComplexCache($params, $func) {
        extract($params);
        $args = array_slice(func_get_args(), 2);
        $this->mutextLoad();
        while(true){
            $value_str = $this->cache->get($key);
            if ($value_str){
                $value = json_decode($value_str);
                if ($value['expire'] >= $now) {
                    break;
                }
                $value['expire'] += $value_lifetime;
                $this->cache->set($key, json_encode($data), $lifetime);

                $value = $this->mutextLoad($params, $func, $args);
                if ($value === false){
                    break;
                }
                usleep($sleep_time * 1000000);
            }
            else {
                $value = $this->mutextLoad($params, $func, $args);
                if ($value === false){
                    break;
                }
                usleep($sleep_time * 1000000);
            }
        }
        return $value['data'];
    }

}
?>
