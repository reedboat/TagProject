<?php
class ArticleTags extends CActiveRecord
{
    protected $primaryKey = array('site', 'id');
    protected $delimiter  = ';';


    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return '{{article_tags}}';
    }

    public function rules(){
        return array(
            array( 'site, id, tags', 'required' ),
        );
    }

    protected function fireEvent($event, $data) {
        $sites = array(
            'news'=>1,
            'ent' =>2,
        );
        switch($event) {
            case 'addTag' :
                $name = $data;
                $data = array('site_id'=>$sites[$this->site], 'news_id'=>$this->id, 'tag'=>$name);
                call_user_func(array($this, 'onArticleAddTag'), $data);
                break;
            case 'removeTag':
                $name = $data;
                $data = array('site_id'=>$sites[$this->site], 'news_id'=>$this->id, 'tag'=>$name);
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

    protected function onArticleAddTag($data){
        $tag = Tag::fetch($data['tag']);
        if ($tag){
            $tag->increment(1);
        }
        else {
            $tag = new Tag();
            $tag->setName($data['tag']);
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

    public function saveTags($tags){
        $this->tags = $this->arr2str($tags);
        $res = $this->save();
        if (!$res) {
            return false;
        }

        foreach($tags as $tag){
            $this->fireEvent('addTag', $tag);
        }
        return true;
    }

    public function updateTags($new_tags){
        $old_tags = $this->str2arr($this->tags);
        $this->tags = $this->arr2str($new_tags);
        $res = $this->save();

        if (!$res) {
            return false;
        }

        $common_tags = array_intersect($old_tags, $new_tags);
        foreach(array_diff($old_tags, $common_tags) as $tag){
                $this->fireEvent('removeTag', $tag);
        }
        foreach(array_diff($new_tags, $common_tags) as $tag) {
                $this->fireEvent('addTag', $tag);
        }

        return true;

    }

    public function changeTag($from, $to){
        $from = trim($from);
        $to   = trim($to);
        $tags_arr = $this->str2arr($this->tags);
        if (!in_array($to, $tags_arr) && in_array($from, $tags_arr)) {
            $this->tags = str_replace($from, $to, $this->tags);
            $this->save();
            $this->fireEvent('checkNewTag', $to);
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

}
