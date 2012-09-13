<?php
$_SERVER['http_proxy']='';
require_once 'header/web.inc.php';
require_once 'inc/tags/interface.inc.php';
require_once 'inc/tags/tags.inc.php';

class InterfaceTest extends WF_DbTestCase{

    public function __construct(){
        parent::__construct();
        $this->server = 'http://i.interface.webdev.com';
        $this->prj = new Tags_project('tcms.interface.tags');
        WF_Registry::set('db', $this->prj->CData->CDb->r);
    }

    public $fixtures = array(
        'tag_articles' => 'TagArticles',
        'article_tags' => 'ArticleTags',
        'tag'          => 'Tag',
    );

    private function request($url, $params = array(), $method='get'){
        $ch = curl_init();
        if ($method == 'get'){
            $url .= "?" . http_build_query($params);
            return file_get_contents($url);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, FALSE);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo $url, "\n";
        if ($httpcode < 200 || $httpcode >= 400){
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
    }

    public function testGet(){
        $url = '/tags/get.php';
        $params = array(
            'site' => 'news',
            'id'   => util_genId(1),
        );
        $result = $this->request($this->server . $url, $params);
        $this->assertTrue($result !== false);
        $result = json_decode($result, true);
        $this->assertEquals(0, $result['response']['code']);
    }

    public function testSet(){
        $url = "/tags/set.php";
        $params = array(
            'site'=>'news',
            'id'  => util_genId(10),
            'type'=>0,
            'time'=>time(),
            'tags'=>'腾讯;iphone;sina',
        );
        $result = $this->request($this->server . $url, $params, 'post');
        $result = json_decode($result, true);
        $this->assertEquals(0, $result['response']['code']);

        $url = "/tags/get.php";
        $result = $this->request($this->server . $url, $params);
        $result = json_decode($result, true);
        $this->assertEquals(0, $result['response']['code']);
        $tags   = $result['data'];
        $this->assertEquals(explode(';', $params['tags']), $tags);
    }
}
?>
