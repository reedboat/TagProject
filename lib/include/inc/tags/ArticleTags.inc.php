<?php
class ArticleTags extends WF_Table
{
    public $primaryKey = array('site_id', 'news_id');
    protected $delimiter  = ';';
    protected $_columns   = array(
        'site_id', 'news_id', 'tags', 'create_time', 'update_time'
    );


    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        $prefix = WF_Registry::get('prefix', 'tbl_');
        return $prefix . "article_tags";
    }

    protected function fireEvent($event, $data) {
        switch($event) {
            case 'addTag' :
                $name = $data;
                $data = array('site_id'=>$this->site_id, 'news_id'=>$this->news_id, 'pub_time'=>$this->time, 'type'=>$this->type, 'tag'=>$name);
                call_user_func(array($this, 'onArticleAddTag'), $data);
                break;
            case 'removeTag':
                $name = $data;
                $data = array('site_id'=>$this->site_id, 'news_id'=>$this->news_id, 'tag'=>$name);
                call_user_func(array( $this, 'onArticleRemoveTag'), $data);
                break;
            case 'checkNewTag':
                $name = $data;
                call_user_func(array($this, 'onCheckNewTag'), array('tag' => $name));
                break;
            default:
                break;
        }
    }

    public function getTags(){
        return $this->str2arr($this->tags);
    }

    public function saveTags($tags){
        $this->tags = $this->arr2str($tags);
        $res = $this->save();
        if (!$res) {
            return false;
        }

        foreach($tags as $tag){
            WF_Event::fire('addArticleTag', array('tag'=>$tag), $this);
        }
        return true;
    }

    public function updateTags($new_tags){
        $this->setNewRecord(false);
        $old_tags = $this->str2arr($this->tags);
        $this->tags = $this->arr2str($new_tags);
        $res = $this->save();

        if (!$res) {
            return false;
        }

        $common_tags = array_intersect($old_tags, $new_tags);

        foreach(array_diff($old_tags, $common_tags) as $tag){
            WF_Event::fire('removeArticleTag', array('tag' => $tag), $this);
        }
        foreach(array_diff($new_tags, $common_tags) as $tag) {
            WF_Event::fire('addArticleTag',    array('tag' => $to), $this);
        }

        return true;
    }

    /**
     * 将Tag表中的
     * 
     * @return 
     * @access 
     **/
    public function changeTag($from, $to){
        $from = trim($from);
        $to   = trim($to);
        $tags_arr = $this->str2arr($this->tags);
        if (!in_array($to, $tags_arr) && in_array($from, $tags_arr)) {
            $this->tags = str_replace($from, $to, $this->tags);
            $this->save();
            WF_Event::fire('removeArticleTag', array('tag' => $from), $this);
            WF_Event::fire('addArticleTag',    array('tag' => $to), $this);
        }
    }

    public function str2arr($str) {
        return explode($this->delimiter, $str);
    }

    public function arr2str($arr) {
        return implode($this->delimiter, $arr);
    }

    public function beforeSave() {
        if (parent::beforeSave()){
            if ($this->create_time <= 0) {
                $this->create_time = $this->update_time = time();                
                return true;
            }
            $this->update_time = time();
            return true;
        }
        return false;
    }

    /**
     * id gen tool for unit test 
     * 
     * @param mixed $id 
     * @return void
     */
    public static function genId($id){
        return date("Ymd") . sprintf("%07s", $id);
    }

    protected function onArticleAddTag($data){
        $tag = Tag::fetch($data['tag']);
        if ($tag){
            $tag->increment(1);
        }
        else {
            $tag = new Tag();
            $tag->setName($tag);
            $tag->save();
        }

        $item = new TagArticles();
        unset($data['tag']);
        $item->index($tag->id, $data);

        return true;
    }

    protected function onArticleRemoveTag($data){
        $tag = Tag::fetch($data['tag']);
        if($tag) {
            $tag->increment(-1);
        }
        else {
            return false;
        }
        unset($data['tag']);
        $item = new TagArticles();
        $item->removeIndex($tag->id, $data);
        return true;
    }

    protected function onCheckNewTag($data) {
        $tag = new Tag();
        $tag->setName($data['tag']);
        $tag->saveIfNotExist();
    }
}
