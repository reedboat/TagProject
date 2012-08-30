<?php
class Db {
    private $db = null;
    private $adapter = '';

    private static $instance;
    
    public function __construct($db_config=null){
        if (is_array($db_config) && (isset($db_config['host']) || isset($db_config['dsn']))){
            if (!isset($db_config['dsn'])) {
                if(!isset($db_config['adapter'])){
                    $this->adapter = 'mysql';
                }
                $dsn = $this->adapter . ":host=" . $db_config['host'] . ";port=" . $db_config['port'];
                if (isset($db_config['dbname']){
                    $dsn .= ";dbname=" . $db_config['dbname'];
                }
                $options = array();
                if ($this->adapter == 'mysql') {
                    $options = array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $db_config['enCode'],
                    );
                }
                $this->connect($dsn, $db_config['username'], $db_config['password'], $options);
            }
            else {
                $this->connect($db_config['dsn'], $db_config['username'], $db_config['password'], array());
            }
        }
    }
    
    public static function instance(){
        if (self::$instance == null){
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    public function getDBConnection(){
        return $this->db;
    }
    public function setDBConnection($db){
        $this->db = $db;
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
