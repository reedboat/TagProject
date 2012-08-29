<?php
/**
 * 
 **/
class TagArticles extends DbTable
{
    public $primaryKey = 'tag_id';
    protected $_columns   = array('tag_id', 'site_id', 'news_id', 'time');

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

    public function search($tag_id, $len=20){
        $criteria = array();
        $criteria['order']     = 'time desc';
        $criteria['condition'] = 'tag_id = :tag_id';
        $criteria['params']    = array(':tag_id' => $tag_id);
        $criteria['limit']     = $len;
        return self::model()->findAll($criteria);
    }

    public function searchSite($tag_id, $site_id, $len=20){
        $criteria = array();
        $criteria['order']     = 'time desc';
        $criteria['condition'] = 'tag_id         = :tag_id and site_id = :site_id';
        $criteria['params']    = array(':tag_id' =>$tag_id, ':site_id' =>$site_id);
        $criteria['limit']     = $len;
        return self::model()->findAll($criteria);
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
