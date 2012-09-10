<?php
/**
 * Event类，用来分离部分逻辑
 **/
class WF_Event
{
    private $name; 
    private $target;
    private $data;

    public function __construct($name, $data, $target)
    {
        $this->name = $name;
        $this->data = $data;
        $this->target = $target;
    }

    public function __get($key){
        if ($key == 'name' || $key == 'target'){
            return $this->$key;
        }
        elseif (isset($this->data[$key])){
            return $this->data[$key];
        }
        return null;
    }
}

class WF_EventMain {
    private static $handlers;
    public static function bind($name, $callback){
        if (!is_array(self::$handlers[$name])){
            self::$handlers[$name] = array();
        }
        self::$handlers[$name][] = $callback;
    }

    public static function fire($name, $data = array(), $target = null) {
        $event = new WF_Event($name, $data, $target);
        if (isset(self::$handlers[$name])){
            foreach(self::$handlers[$name] as $callback){
                try {
                    $callback($event);
                }
                catch(Exception $e){
                }
            }
        }
    }

    public static function unbind($name, $callback) {
        if (isset(self::$handlers[$name])) {
            $key = array_search(self::$handlers[$name], $callback);
            if ($key != -1){
                unset(self::$handlers[$name]);
                return true;
            }
        }
        return false;
    }
 }
?>
