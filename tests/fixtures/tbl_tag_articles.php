<?php
    $data =  array(
        array(
            'tag_id'  => 4, //iphone
            'site_id' => TagSite::getSiteId('news'),//news
            'news_id' => util_genId(1),
            'type'    => 0,
            'time'    => util_time(9),
        ), 
        array(
            'tag_id'  => 4,//iphone
            'site_id' => TagSite::getSiteId('ent'),//ent
            'news_id' => util_genId(1),
            'type'    => 1,
            'time'    => util_time(8),
        ), 
        array(
            'tag_id'  => 3,//ipad
            'site_id' => TagSite::getSiteId('news'),
            'news_id' => util_genId(1),
            'type'    => 0,
            'time'    => util_time(9),
        ), 
    );
return $data;
?>
