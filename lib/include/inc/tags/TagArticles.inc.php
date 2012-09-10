<?php
/**
 * 
 **/
class TagArticles extends WF_Table
{
    public $primaryKey = 'id';
    protected $_columns   = array('id', 'tag_id', 'site_id', 'news_id', 'time');

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'tbl_tag_articles';
	}

    public function rules(){
        return array(
            array('tag_id, site_id, news_id, time', 'required'),
        );
    }

    public function search($tag_id, $site_id=0, $len=20, $offset=0){
        $criteria = array();
        $criteria['order']     = 'time desc';
        $criteria['condition'] = 'tag_id = :tag_id';
        $criteria['params']    = array(':tag_id' => $tag_id);
        if ($site_id > 0){
            $criteria['condition'] .= ' AND site_id = :site_id';
            $criteria['params'][':site_id'] = $site_id;
        }

        $criteria['limit']     = "$offset, $len";
        return $this->findAll($criteria, array('model'=>false));
    }

    public function index($tag_id, $data){
        $className = __CLASS__;
        $item = new $className();
        $item->setAttributes(array(
            'tag_id'  => $tag_id,
            'site_id' => $data['site_id'],
            'news_id' => $data['news_id'],
            'time'    => isset($data['time']) ? $data['time'] : time(),
        ));
        return $item->save();
    }

    public function removeIndex($tag_id, $data){
        $site_id = $data['site_id'];
        $news_id = $data['news_id'];
        $conditions = array('tag_id'=>$tag_id, 'site_id'=>$site_id, "news_id"=>$news_id);
        return self::model()->deleteAll($conditions);
    }
}
