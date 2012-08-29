<?php
class Db {
    private $db = null;

    private static $instance;
    
    public static function instance(){
        if (self::$instance == null){
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    public function getDBConnection(){
        return self::$db;
    }
    public function setDBConnection($db){
        self::$db = $db;
    }

    public static function makeConnection($dsn, $username='', $password='', $driver_options=array()){
        return new PDO($dsn, $username, $password, $driver_options);
    }

    public function selectDB($dbname){
        if ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME)== 'mysql'){
            $this->db->exec("use " . PDO::quote($dbname));
        }
    }

    public function connect($dsn, $username='', $password='', $driver_options=array()){
        $this->db = new PDO($dsn, $username, $password, $driver_options);
    }


    public function connectMysql($server, $username='', $password='', $dbname=''){
        $dsn = '';
        $this->connect($dsn, $username, $password);
    }

    public function execute($sql, $params){
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function query($sql, $params){
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function close(){
        unset($this->db);
    }
}
