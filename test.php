<?php
require "./DbTable.php";
require "./Tag.php";
require "./TagArticles.php";
require "./ArticleTags.php";

$db = new PDO('sqlite:blog-test.db'); 
//$db = new PDO('sqlite:../data/blog-test.db'); 

DbTable::$db = $db;
#$tag = Tag::fetch('iphone');
$tag = Tag::fetch(3);
assert($tag instanceof Tag);
assert(false === $tag->isNewRecord());
$tags = Tag::model()->findAll();
assert(count($tags) == 10);
assert($tags[0] instanceof Tag);
echo "\n";
#var_dump(Tag::model()->findByPk(4));
#var_dump(Tag::model()->findByAttributes(array('name'=>'ipad')));
#var_dump(Tag::suggest('ip', 10));

$tag = new Tag();
$tag->setAttributes(array('name'=>'lamp'));
$result = $tag->saveIfNotExist();

$indexer = new TagArticles();
$tag_id = 4;
$rows = $indexer->search($tag_id);
assert(count($rows)>1);
$rows = $indexer->searchSite(4, 3);
assert(1==count($rows));

$site = 'news';
$id   = "20120824" . sprintf("%07s", '1');
$predefined_pk = array(
    'site' => $site,
    'id'   => $id,
);
$article = ArticleTags::model()->findByPk($predefined_pk);
assert($article != null);
assert($article->tags == "iphone;ipad");

$site = 'news';
$id   = date("Ymd") . sprintf("%07s", '11');
$tags = array('iphone', 'ipad');
$pk = array('site'=>$site, 'id'=>$id);

ArticleTags::model()->deleteAll($pk);

$article = new ArticleTags();

$article->setAttributes($pk);
assert($article->saveTags($tags));
$article2 = ArticleTags::model()->findByPk($pk);
assert($article2 != null);
assert(implode(';', $tags) == $article2->tags);


$tags = array('iphone', 'ipad');
$site = 'news';
$id   = date("Ymd") . sprintf("%07s", '51');
$pk   = array(
    'site' => $site, 
    'id'   => $id,
);
ArticleTags::model()->deleteAll($pk);

$article = new ArticleTags();
$article->setAttributes($pk);
$article->saveTags($tags);

$indexer = new TagArticles();
$rows = $indexer->search(Tag::fetch('iphone')->id, 1);
assert(1 == count($rows));
assert($id == $rows[0]->news_id);
