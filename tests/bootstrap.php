<?php

// change the following paths if necessary

require_once "inc/tags/Table.class.php";
require_once "inc/tags/Db.class.php";
require_once "inc/tags/DbTestCase.class.php";
require_once "inc/tags/Logger.class.php";
require_once "inc/tags/Registry.class.php";

require_once "inc/tags/TagArticles.inc.php";
require_once "inc/tags/Tag.inc.php";
require_once "inc/tags/ArticleTags.inc.php";
require_once "inc/tags/ArticleMini.inc.php";

require __DIR__ . '/fixtures/util.func.php';

define("ROOT_DIR", __DIR__);

WF_Registry::set('logger', new WF_Logger());
$instance = WF_Db::instance('sqlite:'.ROOT_DIR.'/data/tags.db'); 
WF_Registry::set('db', $instance);
unset($instance);

WF_DbTestCase::setBasePath(ROOT_DIR. "/fixtures");
