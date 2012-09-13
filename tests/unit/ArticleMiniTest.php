 <?php
class ArticleTest extends WF_DbTestCase
{
    public $fixtures = array(
        'article_mini'  => 'ArticleMini',
    );

    public function testfindArticles(){
        $pk_list = array(
           array(TagSite::getSiteId('ent'),  util_genId(1)),
           array(TagSite::getSiteId('news'), util_genId(1)),
        );
        $articles = ArticleMini::model()->findArticles($pk_list);
        $this->assertEquals(count($pk_list), count($articles));
        $this->assertNotNull($articles[0]);
        $this->assertNotNull($articles[1]);

        $this->assertEquals($pk_list[0][1], $articles[0]['Farticle_id']);
        $this->assertEquals($pk_list[1][1], $articles[1]['Farticle_id']);

        $site_id = TagSite::getSiteId('news');
        $id_list = array(
            ArticleTags::genId(3),
            ArticleTags::genId(1),
        );
        $articles = ArticleMini::model()->findArticles($site_id, $id_list);
        $this->assertEquals(count($pk_list), count($articles));

        $this->assertEquals($id_list[0], $articles[0]['Farticle_id']);
        $this->assertEquals($id_list[1], $articles[1]['Farticle_id']);
    }

    public function testAddArticle(){
        $site_id = TagSite::getSiteId('tech');
        $news_id = util_genId(4);
        $title   = '科技-标题4';
        $type    = 0;
        $pub_time = time();
        $result = ArticleMini::model()->addArticle($site_id, $news_id, $title, $type, $pub_time);
        $this->assertTrue($result);
        $article = ArticleMini::model()->findByPk(array($site_id, $news_id));
        $this->assertNotNull($article);

        $this->assertEquals($site_id, $article->Fsite_id);
        $this->assertEquals($news_id, $article->Farticle_id);
        $this->assertEquals($title, $article->Ftitle);
    }
}
 ?>
