<?php
    $data =  array(
        array(
            'tag_id'  => 4, //iphone
            'site_id' => 1,//news
            'time'    => 1345795946,
            'news_id' => date("Ymd", 1345795946) . sprintf("%07s", 1),
        ), 
        array(
            'tag_id'  => 4,//iphone
            'site_id' => 3,//ent
            'time'    => 1345795946,
            'news_id' => date("Ymd", 1345795946) . sprintf("%07s", 1),
        ), 
        array(
            'tag_id'  => 3,//ipad
            'site_id' => 1,
            'time'    => 1345795946,
            'news_id' => date("Ymd", 1345795946) . sprintf("%07s", 1),
        ), 
    );
return $data;
?>
