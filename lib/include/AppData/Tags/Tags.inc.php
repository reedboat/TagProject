<?php
/**
 * 
 **/
class AppData_Tags_Tags extends Base_TClass
{
    public function genKey($name)
    {
        $args = func_get_args();
        $name = array_shift($args);

        switch($name)
        {
        case 'NewsTags':
            $site    = $args[0];
            $news_id = $args[1];
            $key     = 'NewsTags_'  . $site . '_' . $news_id;
            break;
        case 'TagArticles':
            $tag     = $args[0];
            $site_id = $args[1];
            $page    = $args[2];
            $len     = $args[3];
            $key     = "${name}_${tag}_${site_id}_${page}_${len}";
            break;

        case 'suggestByKeyword':
            $keyword = $args[0];
            $key="${name}_${keyword}";
            break;
        case 'suggestByInput':
            $input = $args[0];
            $key="${name}_${input}";
            break;

        default:
            $key = '__INVALID_KEY';
        }
        return $key;
    }

    /**
     * 根据文章ID，获取文章的Tags
     * @param string $site
     * @param string $news_id
     * @return array $tags
     **/
    public function getNewsTags($site, $news_id)
    {
        $key  = $this->genKey('NewsTags', $site, $news_id);

        $util = new WF_CacheUtil($this->CP);
        /*main begin*/
        /*main end*/
        $tags = $util->load(array('key'=>$key, 'lifetime'=>3600), array($this, '_getNewsTags'), array($site, $news_id));
        return $tags;
    }

    public function _getNewsTags($site, $news_id){
        $site_id = TagsSite::getSiteId($site);
        $record = ArticleTags::model()->findByPk(array($site_id, $news_id));
        if (!$record) $tags = array();
        else{
            $tags = $record->getTags();
        } 
        return $tags;
    }


    /**
     * 根据文章ID，获取文章的Tags
     * @param string $site
     * @param string $news_id
     * @param array  $tags
     * @return boolean 设置成功与否 
     **/
    public function setNewsTags($site, $news_id, $tags)
    {
        $key  = $this->genKey('NewsTags', $site, $news_id);

        $site_id = TagsSite::getSiteId($site);
        $model = ArticleTags::model()->findByPk(array($site_id, $news_id));

        if ($model == null)
        {
            $model = new ArticleTags();
            $model->site_id = $site_id;
            $model->news_id = $news_id;
            $result = $model->saveTags($tags);
        }
        else {
            $result = $model->updateTags($tags);
        }

        if ($result)
        {
            $cache  = $this->CP->CData->CCache;
            $cache->set($key, json_encode($tags), 3600);
        }
        return $result;
    }

    /**
     * 获取建议的Tags，按照关键词和tags的加权权重排序
     * 
     * @return array $tags
     * @access 
     **/
    public function suggestByKeywords($keywords, $limit=10)
    {
        $result = array();
        $func = array(Tag::model(), 'suggestByKeyword');
        foreach($keywords as $keyword)
        {
            $key = $this->genKey('suggestByKeyword', $keyword);
            $params = array('key'=>$key, 'lifetime'=>86400);
            $util = new WF_CacheUtil($this->CP);
            $tags = $util->load($params, $func, array($keyword));
            foreach($tags as $tag => $weight){
                if (isset($result[$tag])){
                    $result[$tag] += $weight;
                } else {
                    $result[$tag] = intval($weight);
                }
            }
        }
        arsort($result);
        return $result;
    }

    public function suggestByInput($input, $limit=50)
    {
        $key    = $this->genKey('suggestByInput', $input, $limit);
        $func   = array(Tag::model(), 'suggestByPrefix');
        $args   = array($input);
        $params = array('key'=>$key, 'lifetime'=>3600);

        $util = new WF_CacheUtil($this->CP);
        $tags = $util->load($params, $func, array($input));
        return $tags;
    }

    public function removeTag($tag)
    {
    }

    public function mergeTags($to, $from)
    {
    }

    public function findRelatedTags()
    {
    }

    public function _listByTag($tag, $site_id='all', $page=1, $len=20)
    {
        $news_list = array();
        do{
            $record     = Tag::fetch($tag);
            if (!$record) { break; }

            $len     = intval($len);
            $page    = intval($page)>0 ? intval($page) : 1;
            $offset  = ($page -1) * $len;
            $rows = TagArticles::model()->search($record->id, $site_id, $len, $offset);
            if (empty($rows)){ break; }

            $pks = array();
            foreach($rows as $row)
            {
                $site = TagsSite::getSite($row['site_id']);
                $pks[] = array($site, $row['news_id']);
            }
            $news_list = ArticleMini::model()->findArticles($pks);
        }while(0);

        return $news_list;
    }

    public function listByTag($tag, $site='all', $page=1, $len=20)
    {
        $site_id = TagsSite::getSiteId($site);
        $key = $this->genKey('TagArticles', $tag, $site_id,$page, $len);

        $util = new WF_CacheUtil($this->CP);
        $params = array(
            'key'            => $key,
            'lifetime'       => 120,
            'data_lifetime'  => 60,
            'mutex_key'      => $key . "_mutex",
            'mutex_lifetime' => 10,
            'sleep_time'     => 0.1,
        );
        $articles = $util->callByComplexCache($params, array($this,"_listByTag"),  $tag, $site_id, $page, $len);
        return $articles;
    }

    public function addArticle($site, $news_id, $title, $pub_time, $abstract='', $thumbnail=''){
        return ArticleMini::model()->addArticle($site, $news_id, $title, $pub_time, $abstract, $thumbnail);
    }
}
?>
