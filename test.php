<?php
require "./DbTable.php";
require "./Tag.php";

$db = new PDO('sqlite:../data/blog-test.db'); 
DbTable::$db = $db;
#$tag = Tag::fetch('iphone');
$tag = Tag::fetch(3);
var_dump($tag);
var_dump($tag->isNewRecord());
$tags = Tag::model()->findAll();
var_dump($tags);
echo "\n";
#var_dump(Tag::model()->findByPk(4));
#var_dump(Tag::model()->findByAttributes(array('name'=>'ipad')));
#var_dump(Tag::suggest('ip', 10));

$tag = new Tag();
$tag->setAttributes(array('name'=>'lamp'));
$result = $tag->saveIfNotExist();
