<?php

class DbTable {
    public static $db;
    private static $_models = array();

    protected $_attributes = array();
    protected $_pk;
    protected $primaryKey = 'id';
    protected $_metaData = array(
        'primaryKey'     => 'id',
        'columns'        => array(),
        'columnsDefault' => array(),
    );

	public function __construct($scenario='insert')
	{
        if (isset($this->_metaData['columnDefaults'])){
            $this->_attributes = $this->_metaData['columnDefaults'];
        }
		$this->init();
	}

    public function init(){
    }

    public function setDbConnnection($db){
        if (self::$db != null){
            return self::$db;
        }
    }

    public function instantiate($attributes){
		$class=get_class($this);
		$model=new $class(null);
		return $model;
    }

	public function getMetaData()
	{
        return $this->_metaData;
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
        if(in_array($name, $this->_metaData['columns'])){
            $this->_attributes[$name]=$value;
        }
    }
    
    public function __get($name){
        return $this->_attributes[$name];
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
        $primaryKey = $this->_metaData['primaryKey'];
        if (!isset($this->_attributes[$this->primaryKey])){
            return true;
        }
        return false;
    }

    public function save(){
        if (method_exists($this, 'beforeSave')){
            var_dump('aaa');
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
                $id = self::$db->lastInsertId();
                $this->_attributes[$this->primaryKey] = $id;
                $this->_pk = $id;
            }
            return $result;
        }
        else {
            $sql  = 'UPDATE ' . $this->tableName() . " SET ";
            $attrs = $this->_attributes;
            unset($attrs[$this->primaryKey]);
            $sql .= implode('=?,', array_keys($attrs)) . '=? ';
            $sql .= "WHERE " . $this->primaryKey . ' = ?';
            return $this->execute($sql, array_values($attrs));
        }
    }
    
    public function findByPk($id){
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
            var_dump('ss');
            $sql .= " WHERE {$condition['condition']}";
        }
        if (isset($condition['sort'])) {
            $sql .=" ORDER BY {$condition['sort']}"; 
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
        $stmt = self::$db->prepare($sql);
        $result = $stmt->execute($attrs);
        if (!$result) return false;
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

    public function setAttributes($attributes){
        foreach($attributes as $name=>$value)
        {
            if(property_exists($this,$name))
                $this->$name=$value;
            //else if(isset($this->schema['columns'][$name]))
            else if(in_array($name, $this->_metaData['columns']))
                $this->_attributes[$name]=$value;
        }
    }

    public function setAttribute($name, $value){
            if(property_exists($this,$name))
                $this->$name=$value;
            else if(in_array($name, $this->_metaData['columns']))
                $this->_attributes[$name]=$value;
    }

    protected function beforeSave(){
        return true;
    }
}

