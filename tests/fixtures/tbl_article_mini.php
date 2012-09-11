<?php
$rows = array(
    array(
        'Fsite'       => 'news',
        'Farticle_id' => util_genId(1),
        'Ftitle'      => '新闻-标题1',
        'Ftype'       => 0,
        'Fpub_time'   => util_datetime(9),
    ),
    array(
        'Fsite'       => 'ent',
        'Farticle_id' => util_genId(1),
        'Ftitle'      => '娱乐-标题2',
        'Ftype'       => 0,
        'Fpub_time'  => util_datetime(8),
    ),
    array(
        'Fsite'       => 'news',
        'Farticle_id' => util_genId(3),
        'Ftitle'      => '新闻-标题2',
        'Ftype'       => 0,
        'Fpub_time'  => util_datetime(7),
    ),
);
return $rows;
