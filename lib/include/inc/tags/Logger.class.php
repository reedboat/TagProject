<?php
class WF_Logger{
    private $backend;
    private static $instance = null;

    public function __construct($clog=null){
        $this->backend = $clog;
    }

    public static function instance($clog = null){
        if (self::$instance == null){
            $className = __CLASS__;
            self::$instance = new $className($clog);
        }
        return self::$instance;
    }

    public function log($msg, $level){
        if ($this->backend instanceof Until_Log_Web){
            $this->backend->w('info', $msg);
        }
        elseif ($this->backend == null){
            error_log(date('r'). " Logger $level $msg");
        }
        else {
            $this->backend->log($msg, $level);
        }
    }

    public function debug($msg){
        $this->log($msg, 'DEBUG');
    }

    public function info($msg){
        $this->log($msg, 'INFO');
    }

    public function error($msg){
        $this->log($msg, 'ERROR');
    }

    public function warn($msg){
        $this->log($msg, 'WARN');
    }
}
?>
