<?php
require_once 'inc/tags/Db.class.php';
require_once 'inc/tags/Table.class.php';
require_once 'inc/tags/CacheUtil.class.php';
require_once 'inc/tags/Logger.class.php';
require_once 'inc/tags/Project.class.php';
require_once 'inc/tags/Registry.class.php';
require_once 'inc/tags/Tag.inc.php';
require_once 'inc/tags/TagArticles.inc.php';
require_once 'inc/tags/ArticleTags.inc.php';
require_once 'inc/tags/ArticleMini.inc.php';

class TagsSite {
    protected static $sites = array(
        'news' => 1,
        'ent'  => 3,
    );

    public function getSiteId($site){
        if (isset(self::$sites[$site])){
            return self::$sites[$site];
        }
        return 0;
    }

    public function getSite($site_id){ 
        $sites = array_flip(self::$sites);
        if (isset($sites[$site_id])){
            return $sites[$site_id];
        }
        return -1;
    }
}

