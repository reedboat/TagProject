<?php
error_reporting(1);
ini_set('display_errors', E_ALL);

require_once 'header/web.inc.php';
require_once 'inc/tags/interface.inc.php';
require_once 'inc/tags/tags.inc.php';

class TagsPrjTest extends WF_DbTestCase {

	public $fixtures=array(
		'tag'          => 'Tag',
		'article_tags' => 'ArticleTags',
		'tag_articles' => 'TagArticles',
        'article_mini' => 'ArticleMini',
	);

    public function __construct(){
        $this->prj = new Tags_project('tcms.interface.tags');
        WF_Registry::set('db', $this->prj->CData->CDb->r);
        $this->tagsController = $this->prj->rs->data->ext['tags'];
        $redis = WF_Registry::get('redis');
        $redis->flushAll();
    }

    public function testGetTags(){
        $tagsController = $this->tagsController;
        $site = 'news';
        $news_id = ArticleTags::genId(1);
        $tags = $tagsController->getNewsTags($site, $news_id);
        $this->assertEquals(array('iphone', 'ipad'), $tags);
    }

    public function testSetTags(){
        $tagsController = $this->tagsController;
        $site = 'news';
        $news_id = ArticleTags::genId(1);
        $tags = array('iphone','mobile');
        $type = 0;
        $time = time();
        $result = $tagsController->setNewsTags($site, $news_id, $tags, $type, $time);
        $this->assertTrue($result);

        $tags2 = $tagsController->getNewsTags($site, $news_id);
        $this->assertEquals($tags, $tags2);

        //还原
        $tags = array('iphone','ipad');
        $result = $tagsController->setNewsTags($site, $news_id, $tags);
        $this->assertTrue($result);
    }

    public function testSuggestByKeywords(){
        $tagsController = $this->tagsController;
        $keywords = array('腾讯', '微博');
        $result = $tagsController->suggestByKeywords($keywords);
        $tags = array_keys($result);
        $this->assertEquals(4, count($tags));
        $this->assertEquals('腾讯微博', $tags[0]);
        $this->assertEquals('腾讯QQ', $tags[1]);
        $this->assertEquals('新浪微博', $tags[2]);
    }

    public function testSuggestByInput(){
        $tagsController = $this->tagsController;
        $input = "腾";
        $result = $tagsController->suggestByInput($input);
        $tags = array_keys($result);
        $this->assertEquals(3, count($tags));
        $this->assertEquals('腾讯QQ', $tags[0]);
        $this->assertEquals('腾讯微博', $tags[1]);
        $this->assertEquals('腾讯网', $tags[2]);
    }

    public function testGetArticle(){
        $site = 'ent';
        $news_id = util_genId(1);
        $article = $this->tagsController->getArticle($site, $news_id);
        $this->assertEquals($article['Ftitle'] , '娱乐-标题2');
        $this->assertEquals($article['Ftype'] , 0);
        $this->assertEquals($article['Fpub_time'] , util_datetime(8));
    }

    public function testAddArticle(){
        $news_id = util_genId(20);
        $title= '科技-标题20';
        $result = $this->tagsController->addArticle('tech', $news_id, $title, 1, time(), array('key1'=>'val1'));
        $this->assertTrue($result);

        $article = $this->tagsController->getArticle('tech', $news_id);
        $meta    = json_decode($article['Fmeta'], true);
        $this->assertEquals($title, $article['Ftitle']);
        $this->assertEquals('val1', $meta['key1']);
    }

    public function testListArticles(){
        $tagsController = $this->tagsController;
        $tag = 'iphone';
        $articles = $tagsController->listArticles($tag, 0);
        $this->assertEquals(2, count($articles));
        $this->assertTrue(!!$articles[0]);
        $this->assertTrue(!!$articles[1]);
        
        $site = 'news';
        $news_id = ArticleTags::genId(1);
        $tags = array('iphone', 'apple');
        $result = $tagsController->setNewsTags($site, $news_id, $tags);
        $this->assertTrue($result);

        $articles = $tagsController->listArticles($tag, 0);
        $this->assertEquals(2, count($articles));
        $this->assertTrue(!!$articles[0]);
        $this->assertTrue(!!$articles[1]);
    }
}
?>
