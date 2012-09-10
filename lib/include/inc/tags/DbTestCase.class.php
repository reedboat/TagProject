<?php
abstract class WF_DbTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * @var array a list of fixtures that should be loaded before each test method executes.
	 * The array keys are fixture names, and the array values are either AR class names
	 * or table names. If table names, they must begin with a colon character (e.g. 'Post'
	 * means an AR class, while ':post' means a table name).
	 * Defaults to false, meaning fixtures will not be used at all.
	 */
	protected $fixtures=false;
    protected $_records = array();
    protected $_rows = array();
    protected static $basePath = null;
    protected $db;

	/**
	 * PHP magic method.
	 * This method is overridden so that named fixture data can be accessed like a normal property.
	 * @param string $name the property name
	 * @return mixed the property value
	 */
	public function __get($name)
	{
		if(is_array($this->fixtures) && ($rows=$this->_rows($name))!==false)
			return $rows;
		else
			throw new Exception("Unknown property '$name' for class '".get_class($this)."'.");
	}

	/**
	 * PHP magic method.
	 * This method is overridden so that named fixture ActiveRecord instances can be accessed in terms of a method call.
	 * @param string $name method name
	 * @param string $params method parameters
	 * @return mixed the property value
	 */
	//public function __call($name,$params)
	//{
	//	//if(is_array($this->fixtures) && isset($params[0]) && ($record=$this->_records($name,$params[0]))!==false)
	//	//	return $record;
	//	//else
	//    //		throw new Exception("Unknown method '$name' for class '".get_class($this)."'.");
	//}

	/**
	 * @param string $name the fixture name (the key value in {@link fixtures}).
	 * @return array the named fixture data
	 */
	public function getFixtureData($name)
	{
		return $this->_rows($name);
	}

	/**
	 * @param string $name the fixture name (the key value in {@link fixtures}).
	 * @param string $alias the alias of the fixture data row
	 * @return CActiveRecord the ActiveRecord instance corresponding to the specified alias in the named fixture.
	 * False is returned if there is no such fixture or the record cannot be found.
	 */
	public function getFixtureRecord($name,$alias)
	{
		return $this->_records($name,$alias);
	}

	/**
	 * Sets up the fixture before executing a test method.
	 * If you override this method, make sure the parent implementation is invoked.
	 * Otherwise, the database fixtures will not be managed properly.
	 */
	public function setUp()
	{
		parent::setUp();
        if(is_array($this->fixtures)){
            foreach($this->fixtures as $fixtureName=>$modelClass)
            {
                $tableName=WF_Table::model($modelClass)->tableName();
                $this->resetTable($tableName);
                $rows=$this->loadFixtures($modelClass, $tableName);
                if(is_array($rows) && is_string($fixtureName))
                {
                    $this->_rows[$fixtureName]=$rows;
                    if(isset($modelClass))
                    {
                        foreach(array_keys($rows) as $alias)
                            $this->_records[$fixtureName][$alias]=$modelClass;
                    }
                }
            }
        }
    }

    protected function getDb(){
        if ($this->db == null) {
            $this->db = WF_Registry::get('db');
        }
        return $this->db;
    }

    public static function setBasePath($basePath){
        self::$basePath = $basePath;
    }

    protected function loadFixtures($modelClass, $tableName){
		$fileName=self::$basePath.DIRECTORY_SEPARATOR.$tableName.'.php';
		if(!is_file($fileName))
			return false;

		$rows=array();
        $data = require($fileName);
		foreach($data as $alias=>$row)
		{
            $model = new $modelClass;
            $model ->setAttributes($row);
            $model ->save();

            $primaryKey = $model->primaryKey;

            if(is_string($primaryKey) && !isset($row[$primaryKey])){
                $row[$primaryKey]=$model->getPrimaryKey();
            }
            $rows[$alias]=$row;
        }
        return $rows;
    }

    protected function resetTable($tableName){
        $db=$this->getDb();
        $db->execute('DELETE FROM '.$tableName);
        $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver == 'sqlite'){
            $sql = "delete from sqlite_sequence where name=" . $db->quote($tableName);
            $result = $db->execute($sql);
        }
        else if ($driver == 'mysql'){
            $db->execute("truncate table " . $db->quote($tableName));
        }
    }
}
