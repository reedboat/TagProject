<?php

class DbTable {
    protected $db = null;
    private static $_models = array();

    protected $_attributes = array();
    protected $_pk;
    public $primaryKey = 'id';
    protected $_columns   = array();
    protected $_columnsDefault   = array();
    protected $_new = false;

	public function __construct($scenario='insert')
	{
        if ($scenario != 'insert'){
            return ;
        }

        if (!empty($this->_columnDefaults)){
            $this->_attributes = $this->_columnDefaults;
        }
        $this->_new = true;
		$this->init();
	}

    public function init(){
    }

    public static function db(){
        if ($this->db == null) {
            $this->db = Db::instance()->getDbConnection();
        }
        return $this->db;
    }

    public function setDbConnection($db){
        $this->db = $db;
    }

    public function instantiate($attributes){
		$class=get_class($this);
		$model=new $class(null);
		return $model;
    }

	public function getPrimaryKey()
	{
		if(is_string($this->primaryKey))
			return $this->{$this->primaryKey};
		else if(is_array($this->primaryKey))
		{
			$values=array();
			foreach($this->primaryKey as $name)
				$values[$name]=$this->$name;
			return $values;
		}
		else
			return null;
	}

    public function __set($name, $value){
        if(in_array($name, $this->_columns)){
            $this->_attributes[$name]=$value;
        }
    }

    public function __isset($name){
        if(in_array($name, $this->_columns) && isset($this->_attributes[$name])){
            return true;
        }
        return false;
    }

    public function __get($name){
        if(in_array($name, $this->_columns) && isset($this->_attributes[$name])){
            return $this->_attributes[$name];
        }
    }

    public static function model($className=__CLASS__){
        if(isset(self::$_models[$className]))
            return self::$_models[$className];
        else
        {
            $model=self::$_models[$className]=new $className(null);
            return $model;
        }
    }

    public function isNewRecord(){
        return $this->_new;
    }

    public function deleteAll($attributes){
        $sql = "DELETE FROM " . $this->tableName() . " WHERE ";
        $conditions = array();
        foreach($attributes as $col => $value){
            $conditions[] = "$col=?";
        }
        $sql .= implode(" AND ", $conditions);
        return $this->execute($sql, array_values($attributes));
    }

    public function save(){
        if (method_exists($this, 'beforeSave')){
            $this->beforeSave();
        }
        if ($this->isNewRecord()) {
            $sql = "insert into " . $this->tableName() . '(';
            $sql .= implode(',', array_keys($this->_attributes));
            $sql .= ') values (';
            $sql .= implode(',', array_pad(array(), count($this->_attributes), '?'));
            $sql .= ')';
            $result = $this->execute($sql, array_values($this->_attributes));
            if ($result){
                $id = $this->db()->lastInsertId();
                $primaryKey = $this->primaryKey;
                if (is_string($primaryKey)) {
                    if (!isset($this->_attributes[$primaryKey])){
                        $this->_attributes[$primaryKey] = $id;
                    }
                }

                if ($this->_pk != null){
                    $this->_pk = $this->getPrimaryKey();
                }
                $this->_new = false;
            }
            return $result;
        }
        else {
            $sql  = 'UPDATE ' . $this->tableName() . " SET ";
            $attrs = $this->_attributes;
            $primaryKey = $this->primaryKey;
            if (is_string($primaryKey)){
                $pk = array($attrs[$primaryKey]);
                unset($attrs[$primaryKey]);
            }
            else if (is_array($primaryKey)){
                $pk = array();
                foreach($primaryKey as $name){
                    $pk[] = $attrs[$name];
                    unset($attrs[$name]);
                }
            }
            $sql .= implode('=?,', array_keys($attrs)) . '=? ';
            $sql .= "WHERE ";
            if (is_string($primaryKey)){
                $sql .= $this->primaryKey . ' = ?';
            }
            else if (is_array($primaryKey)){
                $conditions = array();
                foreach($primaryKey as $name){
                    $conditions[] = "$name = ?";
                }
                $sql .= implode(" AND ", $conditions);
            }
            return $this->execute($sql, array_merge(array_values($attrs), $pk));
        }
    }

    public function findByPk($id){
        if (is_array($this->primaryKey)){
            return $this->findByAttributes($id);
        }
        $sql  = 'select * from ' . $this->tableName() . " WHERE {$this->primaryKey}=?";
        $stmt = $this->execute($sql, array($id));
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $this->populateRecord($data);
    }

    public function findByAttributes($attributes, $params=array()){
        $sql = "SELECT * FROM " . $this->tableName() . " WHERE ";
        $conditions = array();
        foreach($attributes as $col => $value){
            $conditions[] = "$col=?";
        }
        $sql .= implode(" AND ", $conditions);
        $sql .= ' LIMIT 1';
        $stmt = $this->execute($sql, array_values($attributes));
        $data = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        return $this->populateRecord($data);
    }

    public function findAllByAttributes($attributes, $params=array()){
        $sql = "SELECT * FROM " . $this->tableName() . " WHERE ";
        $conditions = array();
        foreach($attributes as $col => $value){
            $conditions[] = "$col=?";
        }
        $sql.= implode(" AND ", $conditions);
        $stmt = $this->execute($sql, array_values($attributes));
        $data = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        return $this->populateRecord($data);
    }


    public function findAll($condition=array()){
        $sql = "select * from " .$this->tableName() . " ";
        if (isset($condition['condition'])){
            $sql .= " WHERE {$condition['condition']}";
        }
        if (isset($condition['order'])) {
            $sql .=" ORDER BY {$condition['order']}"; 
        }
        if (isset($condition['limit'])){
            $sql .=" LIMIT {$condition['limit']}";
        }
        $params = isset($condition['params']) ? $condition['params'] : array();
        $stmt = $this->execute($sql, $params);
        $data = $stmt? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;
        return $this->populateRecords($data);
    } 

    public function findAllBySql($sql, $data){
        $stmt = $this->execute($sql, $data);
        $data = $stmt? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;
        return $this->populateRecords($data);
    }

    public function execute($sql, $attrs){
        $stmt = $this->db()->prepare($sql);
        $this->logger->log("sql execute '$sql (" . implode(', ', $params) . ")'", "DEBUG");
        if (!$stmt){
            $this->logger->log("ERROR: sql prepare failed '$sql'", "ERROR");
            return false;
        }

        $result = $stmt->execute($params);
        if (!$result) {
            $this->logger->log("ERROR: sql execute failed '$sql (" . implode(', ', $params) . ")'", "ERROR");
            return null;
        }

        if (strtolower(substr($sql, 0, 6)) == 'select') {
            return $stmt;
        }
        return true;
    }

    public function populateRecords($data,$index=null)
    {
        $records=array();
        foreach($data as $attributes)
        {
            if(($record=$this->populateRecord($attributes))!==null)
            {
                if($index===null)
                    $records[]=$record;
                else
                    $records[$record->$index]=$record;
            }
        }
        return $records;
    }

    public function populateRecord($attributes){
        if($attributes!==false)
        {
            $record=$this->instantiate($attributes);
            $record->init();
            $record->setAttributes($attributes);
            $record->_pk=$record->getPrimaryKey();
            return $record;
        }
        else
            return null;
    }

    public function getAttributes(){
        return $this->_attributes;
    }
    public function setAttributes($attributes){
        foreach($attributes as $name=>$value)
        {
            if(property_exists($this,$name))
                $this->$name=$value;
            //else if(isset($this->schema['columns'][$name]))
            else if(in_array($name, $this->_columns))
                $this->_attributes[$name]=$value;
        }
    }

    public function setAttribute($name, $value){
        if(property_exists($this,$name))
            $this->$name=$value;
        else if(in_array($name, $this->_columns))
            $this->_attributes[$name]=$value;
    }

    protected function beforeSave(){
        return true;
    }
}

