<?php
class Tag extends DbTable
{
    public $primaryKey = 'id';
    protected $_columns  = array('id', 'name', 'frequency', 'create_time');

    public function tableName()
    {
        return 'tbl_tag';
    }

    public static function model($className = __class__){
        return parent::model($className);
    }

    public static function suggest($keyword, $limit=10){
        $tags=self::model()->findAll(array(
            'condition'=>'name LIKE :keyword',
            'order'=>'frequency DESC, Name',
            'limit'=>$limit,
            'params'=>array(
                ':keyword'=>'%'.strtr($keyword,array('%'=>'\%', '_'=>'\_', '\\'=>'\\\\')).'%',
            ),
        ));
        $names=array();
        foreach($tags as $tag)
            $names[]=$tag['name'];
        return $names;
    }

    public static function fetch($identity) {
        if (is_integer($identity)) {
            $id = $identity;
            return self::model()->findByPk($id);
        }
        else {
            $name = $identity;
            return self::model()->findByAttributes(array( 'name' => $name));
        }
    }

    public function rename($new_name) {
        if ($this->id >0){
            $this->name = $new_name; 
            return $this->save();
        }
        else {
            throw new LogicException("tag to rename must exist");
        }
    }

    public function increment($weight=1) {
        $this->frequency += $weight;
        return $this->save();
    }

    public function saveIfNotExist(){
        if (!self::fetch($this->name)) {
            return $this->save();
        }
        return false;
    }

    public function setName($name){
        $this->name = $name;
    }

    protected function beforeSave(){
        if (parent::beforeSave()){
            if ($this->isNewRecord()){
                $this->setAttribute('create_time', time());
                $this->setAttribute('frequency', 1);
            }
            return true;
        }
        return false;
    }
}
