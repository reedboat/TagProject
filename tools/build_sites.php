<?php
require_once 'header/web.inc.php';
require_once "inc/tags/tags.inc.php";

$_SERVER['HTTP_PROXY']  = '';
$redis = new Redis();
$ret = $redis -> connect( '127.0.0.1', 6379);
var_dump($ret);

TagSite::setStore($redis);
$config = array(
    'host'     => '10.1.148.140',
    'port'     => '3307',
    'username' => 'kufazhang',
    'password' => 'kufazhang123456',
    'enCode'   => 'latin1',
);
$lines = file(__DIR__ . '/../tests/data/sites.dat');
$rows = array();
foreach($lines as $line){
    list($name, $id) = explode("\t", $line);
    $id = trim($id);
    $rows[$id] = $name;
}
TagSite::refresh($config, $rows);
$id = TagSite::getSiteId('news');
$site = TagSite::getSite($id);
echo $id,":", $site , "\n";
?>
