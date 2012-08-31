<?php
class TagArticlesTest extends DbTestCase { 
    // 本表比较大，尽量减小存储
    //
    public $fixtures = array(
        'tag_articles' => 'TagArticles',
        'article_tags' => 'ArticleTags',
        'tag'          => 'Tag',
    );

    public function __construct(){
        parent::__construct(); 
        $this->tags = $tags = array(
            'iphone' => 4,
            'ipad'   => 3,
        );
        $this->sites = $sites = array(
            'news' => 1,
            'tech' => 2,
            'ent'  => 3,
        );
        $this->sites_flip = array_flip($this->sites);
    }

    public function testSearchAll() {
        $indexer = new TagArticles();
        $tag = 'iphone';
        $tag_id = $this->tags[$tag];
        $rows = $indexer->search($tag_id);
        $this->assertEquals(2 , count($rows));
        $row = $rows[0];
        $pk = array('site_id'=>$this->sites_flip[$row['site_id']], 'id'=>$row['news_id']);
        $article = ArticleTags::model()->findByPk($pk);
        if ($article){
            $tags = $article->str2arr($article->tags);
            $this->assertTrue(in_array($tag, $tags));
        }
    }

    public function testSearchSite(){
        $indexer = new TagArticles();
        $name = 'iphone';
        $site_id = 3;//'ent';
        $tag_id = Tag::fetch($name)->id;
        $rows = $indexer->search($tag_id, $site_id);
        $this->assertEquals(1, count($rows));
        $news_id = date("Ymd", 1345795946) . sprintf("%07s", 1);
        $this->assertEquals($news_id, $rows[0]['news_id']);
    }

    public function testUpdate() {
        $indexer = new TagArticles();

        $tag = 'iphone';
        $pk = array(
            'site_id' => $this->sites['ent'],
            'news_id' => ArticleTags::genId(21),
        );
        $tag_id = $this->tags[$tag];
        $indexer->index($tag_id, $pk);
        $articles = $indexer->search($tag_id);
        $article = $articles[0];
        $this->assertEquals($pk['news_id'], $article['news_id']);
        $this->assertEquals($pk['site_id'], $article['site_id']);
    }

    public function testSearchPagination(){
    }
}
