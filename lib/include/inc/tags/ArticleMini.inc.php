<?php
class ArticleMini extends WF_Table {
    public $primaryKey  = array('Fsite', 'Farticle_id');
    protected $_columns = array('Fsite', 'Farticle_id', 'Ftitle', 'Fpub_time', 'Fabstract', 'Fthumbnail');

    public function tableName(){
        return "tbl_article_mini";
    }

    public static function model($className = __class__){
        return parent::model($className);
    }


    public function addArticle($site, $article_id, $title, $pub_time=0, $abstract=Null, $thumbnail=Null){
        if ($pub_time == 0) {
            $pub_time = date("c");
        }
        $className = __CLASS__;
        $article = new $className;
        $article ->setAttributes(array(
            'Fsite'       => $site,
            'Farticle_id' => $article_id,
            'Ftitle'      => $title,
            'Fpub_time'   => $pub_time,
        ));
        if ($abstract) {
            $article->Fabstract = $abstract;
        }
        if ($thumbnail){
            $article_id->Fthumbnail = $thumbnail;
        }
        return $article->save();
    }

    /**
     * findArticles 根据ID 批量查找文章
     * 
     * @todo 是否做成批量查询?
     * @param mixed $pk_list 
     * @access public
     * @return void
     */
    public function findArticles($pk_list){
        //@todo check $pk_list
        $ret = array();
        if (is_string($pk_list)){
            $site = $pk_list;
            $args = func_get_args();
            if (count($args)!=2 || !is_array($args[1])){
                throw new InvalidArgumentException("ArticleMini::findArticles arguments erro");
            }
            $id_list = $args[1];
            #$sql = "select * from " . $this->tableName() . "where Fsite=? and Farticle_id in('". implode("','", $id_list) ."')";
            #$params = array($site);
            #return $this->findAllBySql($sql, $params);
            
            $pk_list = array(); 
            foreach($id_list as $news_id){
                $pk_list[] = array($site, $news_id);
            }
        }

        foreach($pk_list as $pk){
            $article = $this->findByPk($pk, array('model'=>false));
            array_push($ret, $article);
        }  
        return $ret;
    }
}
?>
