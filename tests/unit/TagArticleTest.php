<?php
class TagArticlesTest extends WF_DbTestCase { 
    // 本表比较大，尽量减小存储
    //
    public $fixtures = array(
        'tag_articles' => 'TagArticles',
        'article_tags' => 'ArticleTags',
        'tag'          => 'Tag',
    );

    public function testSearchAll() {
        $indexer = new TagArticles();
        $tag = 'iphone';
        $tag_id = Tag::fetch($tag)->id;
        $rows = $indexer->search($tag_id);
        $this->assertEquals(2 , count($rows));
        $row = $rows[0];
        $pk = array('site_id'=>$row['site_id'], 'news_id'=>$row['news_id']);
        $article = ArticleTags::model()->findByPk($pk);
        if ($article){
            $tags = $article->str2arr($article->tags);
            $this->assertTrue(in_array($tag, $tags));
        }
    }

    public function testSearchSite(){
        $name = 'iphone';
        $site_id = TagSite::getSiteId('ent');
        $tag_id = Tag::fetch($name)->id;
        $rows = TagArticles::model()->search($tag_id, $site_id);
        $this->assertEquals(1, count($rows));
        $news_id = util_genId(1);
        $this->assertEquals($news_id, $rows[0]['news_id']);
    }

    public function testSearchType(){
        $indexer = TagArticles::model();
        $name = 'iphone';
        $tag_id = Tag::fetch($name)->id;
        $site_id = TagSite::getSiteId('ent');
        $type = 1;

        $rows = $indexer->search($tag_id, $site_id, $type);
        $this->assertEquals(1, count($rows));
        $news_id = util_genId(1);
        $this->assertEquals($news_id, $rows[0]['news_id']);

        $site_id = 0;
        $rows = $indexer->search($tag_id, $site_id, $type);
        $this->assertEquals(1, count($rows));

        $site_id = 1;
        $rows = $indexer->search($tag_id, $site_id, $type);
        $this->assertEquals(0, count($rows));

    }

    public function testUpdate() {
        $tag = 'iphone';
        $pk = array(
            'site_id' => TagSite::getSiteId('ent'),
            'news_id' => util_genId(21),
        );
        $tag_id = Tag::fetch($tag)->id;
        $data = $pk + array('time' => util_time(10), 'type'=>0);
        $result = TagArticles::model()->index($tag_id, $data);
        //$this->assertTrue($result);

        $articles = TagArticles::model()->search($tag_id);
        $article = $articles[0];
        $this->assertEquals($pk['news_id'], $article['news_id']);
        $this->assertEquals($pk['site_id'], $article['site_id']);

        $result = TagArticles::model()->removeIndex($tag_id, $data);
        //$this->assertTrue($result);
    }

    public function testRemove(){
        $tag = 'iphone';
        $data = array(
            'site_id' => TagSite::getSiteId('ent'),
            'news_id' => ArticleTags::genId(1),
            'type'    => 1,
            'time'    => util_time(7),
        );
        $tag_id = Tag::model()->fetch($tag)->id;

        $count = TagArticles::model()->count($tag_id);
        $this->assertEquals(2, $count);
        $count = TagArticles::model()->count($tag_id, $data['site_id']);
        $this->assertEquals(1, $count);
        $count = TagArticles::model()->count($tag_id, 0, $data['type']);
        $this->assertEquals(1, $count);
        $count = TagArticles::model()->count($tag_id, $data['site_id'], $data['type']);
        $this->assertEquals(1, $count);

        $result = TagArticles::model()->removeIndex($tag_id, $data);
        //$this->assertTrue($result);

        $count = TagArticles::model()->count($tag_id);
        $this->assertEquals(1, $count);
        $count = TagArticles::model()->count($tag_id, $data['site_id']);
        $this->assertEquals(0, $count);
        $count = TagArticles::model()->count($tag_id, 0, $data['type']);
        $this->assertEquals(0, $count);
        $count = TagArticles::model()->count($tag_id, $data['site_id'], $data['type']);
        $this->assertEquals(0, $count);

        $result = TagArticles::model()->index($tag_id, $data);
        //$this->assertTrue($result);
    }

    public function testSearchPagination(){
    }
}
