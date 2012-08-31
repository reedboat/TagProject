<?php
/**
 * 
 **/
class ArticleTagsTest extends DbTestCase
{
    public function setUp(){
        parent::setUp(); 
        $site_id = 1;
        $news_id   = date("Ymd") . sprintf("%07s", '1');
        $this->predefined_pk = array(
            'site_id' => $site_id,
            'news_id'   => $news_id,
        );
    }

    public $fixtures = array(
        'article_tags' => 'ArticleTags',
        'tag'          => 'Tag',
        'tag_articles' => 'TagArticles',
    );

    public function testFetch(){
        $article = ArticleTags::model()->findByPk($this->predefined_pk);
        $this->assertNotNull($article, 'Cannot find Artice and Tags');
        $this->assertEquals('iphone;ipad', $article->tags, "find wrong tags");
    }

    public function testGetTags(){
        $site_id = 1;//'news';
        $news_id   = ArticleTags::genId(1);
        $model = ArticleTags::model()->findByPk(array($site_id, $news_id));
        $tags = $model->getTags();
        $this->assertTrue(is_array($tags));
        $this->assertTrue(in_array('iphone', $tags));
    }

    public function testCreateOld(){
        $site_id = 1;//'news';
        $news_id = date("Ymd") . sprintf("%07s", '11');
        $tags = array('iphone', 'ipad');

        $article = new ArticleTags();
        $article->setAttributes( array(
            'site_id' => $site_id, 
            'news_id' => $news_id,
        ));
        $this->assertTrue($article->saveTags($tags), "save article tags failed");

        $pk = array('site_id'=>$site_id, 'news_id'=>$news_id);
        $article2 = ArticleTags::model()->findByPk($pk);
        $this->assertNotNull($article2);
        $this->assertEquals(implode(';', $tags), $article2->tags);
    }

    public function testCreateNew(){
        $site_id = 1;//'news';
        $news_id   = date("Ymd") . sprintf("%08s", '12');
        $new_tagname = 'nokia';
        $tags = array('iphone', $new_tagname);

        $article = new ArticleTags();
        $article->setAttributes( array(
            'site_id' => $site_id, 
            'news_id'   => $news_id,
        ));
        $this->assertTrue($article->saveTags($tags), "save article tags failed");

        $article2 = ArticleTags::model()->findByPk(array('site_id'=>$site_id, 'news_id'=>$news_id));
        $this->assertNotNull($article2);
        $this->assertEquals(implode(';', $tags), $article2->tags);
        
        $tag = Tag::fetch($new_tagname);
        $this->assertNotNull($tag);
    }

    public function testUpdateTags() {
        //change tags to old
        //change tags to new
        $new_tagname =  'windows mobile';
        $tags = array('iphone', $new_tagname);
        $article = ArticleTags::model()->findByPk($this->predefined_pk);
        $this->assertNotNull($article);
        $article->updateTags($tags);

        $article2 = ArticleTags::model()->findByPk($this->predefined_pk);
        $this->assertEquals(implode(';', $tags), $article2->tags);

        $tag = Tag::fetch($new_tagname);
        $this->assertNotNull($tag);
    }

    public function testChangeTag(){
        $from = 'ipad';
        $to   = 'android';
        
        $article = ArticleTags::model()->findByPk($this->predefined_pk);
    }

    public function testTagsIndex(){
        $tags = array('iphone', 'ipad');
        $site_id = 'news';
        $news_id   = date("Ymd") . sprintf("%07s", '51');
        $article = new ArticleTags();
        $article->setAttributes( array(
            'site_id' => $site_id, 
            'news_id'   => $news_id,
        ));
        $result = $article->saveTags($tags);
        $this->assertTrue($result);
        
        $indexer = new TagArticles();
        $rows = $indexer->search(Tag::fetch('iphone')->id, 0, 1);
        $this->assertEquals(1, count($rows));
        $this->assertEquals($news_id, $rows[0]['news_id']);
    }
}
