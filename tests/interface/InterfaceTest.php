<?php
require_once 'header/web.inc.php';
require_once 'inc/tags/interface.inc.php';
require_once 'inc/tags/tags.inc.php';

class InterfaceTest extends WF_DbTestCase{

    public function __construct(){
        parent::__construct();
        $this->server = 'http://i.interface.webdev.com';
        $this->prj = new Tags_project('tcms.interface.tags');
        WF_Registry::set('db', $this->prj->CData->CDb->r);
        $_SERVER['http_proxy'] = '';
    }

    public $fixtures = array(
        'tag_articles' => 'TagArticles',
        'article_tags' => 'ArticleTags',
        'tag'          => 'Tag',
        'article_mini' => 'ArticleMini',
    );

    private function request($url, $params = array(), $method='get'){
        $ch = curl_init();
        $params['oe'] = 'utf-8'; 
        if ($method == 'get'){
            $url .= "?" . http_build_query($params);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, FALSE);
        curl_setopt($ch, CURLOPT_PROXY, '');
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode < 200 || $httpcode >= 400){
            var_dump($result);
            return false;
        }
        curl_close($ch);
        return $result;
    }

    public function testConnect(){
        $url = "/tags/get.php";
        $params = array();
        $result = $this->request($this->server . $url);
        $this->assertTrue($result !== false);

        $url = "/tags/set.php";
        $result = $this->request($this->server . $url, array(), 'post');
        $this->assertTrue($result !== false);
    }

    private function checkResult($result, $valid = true){
        $this->assertTrue($result !== false);
        $result = json_decode($result, true);
        $this->assertNotNull($result);
        if ($valid){
            $this->assertEquals(0, $result['response']['code']);   
            return $result['data'];
        }
        else {
            $this->assertNotEquals(0, $result['response']['code']);   
            return $result['response']['code'];
        }
    }

    public function testGet(){
        $url = '/tags/get.php';
        $params = array(
            'site' => 'news',
            'id'   => util_genId(1),
        );
        $result = $this->request($this->server . $url, $params);
        $data = $this->checkResult($result);
        $this->assertTrue(in_array('iphone', $data));

        $params['site'] = '';
        $result = $this->request($this->server . $url, $params);
        $code = $this->checkResult($result, false);
    }

    public function testSet(){
        $url = "/tags/set.php";
        $params = array(
            'site'   => 'news',
            'id'     => util_genId(10),
            'type'   => 0,
            'time'   => time(),
            'source' => 'web',
            'user'   => 'kufazhang',
            'tags'   => '腾讯;iphone;sina',
        );
        $result = $this->request($this->server . $url, $params, 'post');
        $data   = $this->checkResult($result);

        $url = "/tags/get.php";
        $result = $this->request($this->server . $url, $params);
        $data = $this->checkResult($result);
        $this->assertTrue(in_array('腾讯', $data));
    }

    public function testList(){
    }

    public function testSuggest(){
        $url = "/tags/suggest.php";
        $params = array(
            'input' => 'ip',
            'len'   => 20,
        );
        $result = $this->request($this->server . $url, $params);
        $data = $this->checkResult($result);
        $data = array_keys($data);
        $this->assertEquals('iphone', $data[0]);
        $this->assertEquals('ipad', $data[1]);
    }

    public function testSuggestByKeyword(){
        $url = "/tags/suggestByKeywords.php";
        $params = array(
            'keywords' => '微博;腾讯',
            'len'   => 20,
        );
        $result = $this->request($this->server . $url, $params);
        $data = $this->checkResult($result);
        $data = array_keys($data);
        $this->assertEquals('腾讯微博', $data[0]);
    }

    public function testAddArticle(){
        $url = "/tags/addArticle.php";
        $params = array(
            'site'    => 'ent',
            'news_id' => util_genId(5),
            'pub_time'    => time(),
            'type'    => 0,
            'title'   => '娱乐-标题5',
            'meta'    => array(
                'thumbnail' => 'http://ia.ibtimes.com/chinese/data/images/full/2012/09/13/16421.jpg',
            )
        );
        $result = $this->request($this->server . $url, $params, 'post');
        $data = $this->checkResult($result);

        $url = "/tags/getArticle.php";
        $params2 = array(
            'site'    => 'ent',
            'news_id' => util_genId(5),
        );
        $result = $this->request($this->server . $url, $params2);
        $data = $this->checkResult($result);
        $this->assertEquals($params['title'], $data['Ftitle']);
    }
}
?>
