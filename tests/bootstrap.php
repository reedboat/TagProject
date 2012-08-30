<?php

// change the following paths if necessary

require "inc/tags/DbTable.class.php";
require "inc/tags/DbTestCase.class.php";

require "AppData/Tags/Tag.inc.php";
require "AppData/Tags/TagArticles.inc.php";
require "AppData/Tags/ArticleTags.inc.php";

$db = new PDO('sqlite:data/blog-test.db'); 
DbTable::setDbConnection($db);
unset($db);
DbTestCase::setBasePath(__DIR__ . "/fixtures");
