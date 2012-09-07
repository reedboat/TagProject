<?php
require_once 'header/web.inc.php';
require_once 'inc/tags/interface.inc.php';
require_once 'inc/tags/tags.inc.php';

class TagsPrjTest extends DbTestCase {

	public $fixtures=array(
		'tag'          => 'Tag',
		'article_tags' => 'ArticleTags',
		'tag_articles' => 'TagArticles',
        'article_mini' => 'ArticleMini',
	);

    public function __construct(){
        $this->prj = new Tags_project('tcms.interface.tags');
        DB::setInstance($this->prj->CData->CDb->r);
        $this->tagsController = $this->prj->rs->data->ext['tags'];
        $cache = $this->prj->CData->CCache;
        $cache->delete('NewsTags_news_'.ArticleTags::genId(1));
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
    }

    public function testSuggestByKeywords(){
        $tagsController = $this->tagsController;
        $keywords = array('iphone','mobile');
        $tagsController->suggestByKeywords($keywords);
    }

    public function testSuggestByInput(){
        $tagsController = $this->tagsController;
        $input = "ip";
        $tagsController->suggestByInput($input);
    }

    public function testListRelatedArticles(){
        $tagsController = $this->tagsController;
        $tag = 'iphone';
        $articles = $tagsController->listByTag($tag);
        $this->assertTrue(count($articles) > 1);
    }
}
?>
