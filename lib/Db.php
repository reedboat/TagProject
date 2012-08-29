<?php
class Db {
    private $conn = null;

    private static $instance;
    
    public static function instance(){
        if (self::$instance == null){
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    public function getDBConnection(){
        return $this->conn;
    }
    public function setDBConnection($conn){
        $this->conn = $conn;
    }

    public function selectDB($dbname){
        if ($this->db->getAttribute(PDO::ATTR_DRIVER_NAME)== 'mysql'){
            return $this->db->exec("use " . PDO::quote($dbname));
        }
        return false;
    }

    public function connect($dsn, $username='', $password='', $driver_options=array()){
        $this->conn = new PDO($dsn, $username, $password, $driver_options);
    }

    public function execute($sql, $params){
        if (!$this->conn) {
            return false;
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        return $stmt->execute($params);
    }

    public function query($sql, $params){
        if (!$this->conn) {
            return false;
        }
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->execute($params);
        return $stmt;
    }

    public function close(){
        unset($this->conn);
    }
}
