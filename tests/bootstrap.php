<?php

// change the following paths if necessary


require_once 'inc/tags/lib/Loader.class.php';
WF_Loader::registerAutoload();

require_once "inc/tags/TagArticles.inc.php";
require_once "inc/tags/Tag.inc.php";
require_once "inc/tags/ArticleTags.inc.php";
require_once "inc/tags/ArticleMini.inc.php";
require_once "inc/tags/TagsKey.inc.php";
require_once "inc/tags/TagSite.inc.php";

require __DIR__ . '/fixtures/util.func.php';

$conf = require __DIR__ . '/config/test.conf.php';

define("ROOT_DIR", __DIR__);

WF_Registry::set('logger', new WF_Logger());
$instance = WF_Db::instance($conf['db']); 
WF_Registry::set('db', $instance);
unset($instance);

WF_DbTestCase::setBasePath(ROOT_DIR. "/fixtures");
WF_Event::bind('addArticleTag', array(TagArticles::model(), 'onAddArticleTag'));
WF_Event::bind('removeArticleTag', array(TagArticles::model(), 'onRemoveArticleTag'));

$redis = new Redis();
$redis_config = $conf['redis'];
$redis->connect($redis_config['host'], $redis_config['port']);
$redis->flushAll();


WF_Registry::set('cache', $redis);
WF_Registry::set('redis', $redis);
WF_Config::set('tagitem_use_cache', true);

TagSite::refresh(__DIR__ . '/data/sites.dat');
$id = TagSite::getSiteId('news');
