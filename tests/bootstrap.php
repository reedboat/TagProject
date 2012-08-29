<?php

// change the following paths if necessary

require "../lib/DbTable.php";
require "../lib/DbTestCase.php";

require "../Tag.php";
require "../TagArticles.php";
require "../ArticleTags.php";

$db = new PDO('sqlite:data/blog-test.db'); 
DbTable::setDbConnection($db);
unset($db);
DbTestCase::setBasePath(__DIR__ . "/fixtures");
