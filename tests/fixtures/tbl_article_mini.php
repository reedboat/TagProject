<?php
$rows = array(
    array(
        'Fsite_id'    => TagSite::getSiteId('news'),
        'Farticle_id' => util_genId(1),
        'Ftitle'      => '新闻-标题1',
        'Ftype'       => 0,
        'Fpub_time'   => util_datetime(9),
    ),
    array(
        'Fsite_id'    => TagSite::getSiteId('ent'),
        'Farticle_id' => util_genId(1),
        'Ftitle'      => '娱乐-标题2',
        'Ftype'       => 0,
        'Fpub_time'  => util_datetime(8),
    ),
    array(
        'Fsite_id'       => TagSite::getSiteId('news'),
        'Farticle_id' => util_genId(3),
        'Ftitle'      => '新闻-标题2',
        'Ftype'       => 0,
        'Fpub_time'  => util_datetime(7),
    ),
);
return $rows;
