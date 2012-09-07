<?php
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
        $cache = $this->prj->CData->CCache;
        $cache->delete(AppData_Tags_Tags::genKey('news', ArticleTags::genId(1)));
        $cache->delete(AppData_Tags_Tags::genKey('TagArticles', 'iphone', 0, 1, 20));
        $cache->delete(AppData_Tags_Tags::genKey('suggestByKeyword', '腾讯'));
        $cache->delete(AppData_Tags_Tags::genKey('suggestByKeyword', '微博'));
        $cache->delete(AppData_Tags_Tags::genKey('suggestByInput', '腾'));
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
        $result = $tagsController->setNewsTags($site, $news_id, $tags);
        $this->assertTrue($result);

        $tags2 = $tagsController->getNewsTags($site, $news_id);
        $this->assertEquals($tags, $tags2);

        //还原
        $tags = array('iphone','ipad');
        $result = $tagsController->setNewsTags($site, $news_id, $tags);
        $this->assertTrue($result);
    }

    public function testSuggestByKeywords(){
        //$this->markTestSkipped();
        $tagsController = $this->tagsController;
        $keywords = array('腾讯', '微博');
        $result = $tagsController->suggestByKeywords($keywords);
        $tags = array_keys($result);
        $this->assertEquals(4, count($tags));
        $this->assertEquals('腾讯微博', $tags[0]);
        $this->assertEquals('腾讯QQ', $tags[1]);
        $this->assertEquals('新浪微博', $tags[2]);
    }

    //@skip
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

    //nocache
    public function test_ListByTag(){
        $tagsController = $this->tagsController;
        $tag = 'iphone';
        $articles = $tagsController->_listByTag($tag,0);
        $this->assertTrue(count($articles) == 2);
        $this->assertTrue(!!$articles[0]);
        $this->assertTrue(!!$articles[1]);
    }

    //with cache
    public function testListByTag(){
        $tagsController = $this->tagsController;
        $tag = 'iphone';
        // create item in cache
        $articles = $tagsController->listByTag($tag, 0);
        $this->assertEquals(2, count($articles));
        $this->assertTrue(!!$articles[0]);
        $this->assertTrue(!!$articles[1]);
        
        //get from cache
        $articles = $tagsController->listByTag($tag, 0);
        $this->assertEquals(2, count($articles));
        $this->assertTrue(!!$articles[0]);
        $this->assertTrue(!!$articles[1]);
    }
}
?>
