<?php

// change the following paths if necessary

require_once "inc/tags/DbTable.class.php";
require_once "inc/tags/Db.class.php";
require_once "inc/tags/DbTestCase.class.php";

require_once "inc/tags/TagArticles.inc.php";
require_once "inc/tags/Tag.inc.php";
require_once "inc/tags/ArticleTags.inc.php";
require_once "inc/tags/ArticleMini.inc.php";

require __DIR__ . '/fixtures/util.func.php';

define("ROOT_DIR", __DIR__);

Db::instance('sqlite:'.ROOT_DIR.'/data/blog-test.db'); 
DbTestCase::setBasePath(ROOT_DIR. "/fixtures");

