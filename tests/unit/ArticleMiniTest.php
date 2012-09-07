 <?php
class ArticleTest extends WF_DbTestCase
{
    public $fixtures = array(
        'article_mini'  => 'ArticleMini',
    );

    public function testfindArticles(){
        $pk_list = array(
           array('ent',  util_genId(1)),
           array('news', util_genId(1)),
        );
        $articles = ArticleMini::model()->findArticles($pk_list);
        $this->assertEquals(count($pk_list), count($articles));
        $this->assertNotNull($articles[0]);
        $this->assertNotNull($articles[1]);

        $this->assertEquals($pk_list[0][1], $articles[0]['Farticle_id']);
        $this->assertEquals($pk_list[1][1], $articles[1]['Farticle_id']);

        $site = 'news';
        $id_list = array(
            ArticleTags::genId(3),
            ArticleTags::genId(1),
        );
        $articles = ArticleMini::model()->findArticles($site, $id_list);
        $this->assertEquals(count($pk_list), count($articles));

        $this->assertEquals($id_list[0], $articles[0]['Farticle_id']);
        $this->assertEquals($id_list[1], $articles[1]['Farticle_id']);
    }

    public function testAddArticle(){
        $site    = 'tech';
        $news_id = util_genId(4);
        $title   = '科技-标题4';
        $result = ArticleMini::model()->addArticle($site, $news_id, $title, $desc=null, $thumbnail=null);
        $this->assertTrue($result);
        $article = ArticleMini::model()->findByPk(array($site, $news_id));
        $this->assertNotNull($article);

        $this->assertEquals($site, $article->Fsite);
        $this->assertEquals($news_id, $article->Farticle_id);
        $this->assertEquals($title, $article->Ftitle);
    }
}
 ?>
