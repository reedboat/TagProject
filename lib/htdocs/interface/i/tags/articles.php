<?php
/**
 * 根据Tag，查找文章列表 
 */
require_once 'header/web.inc.php';
require_once 'inc/tags/interface.inc.php';
require_once 'inc/tags/tags.inc.php';
$profile_begin = microtime(true);

$prj = new Tags_Project('tcms.interface.tags');
project_init_params($prj);
$tag  = io_get('tag');
$site = io_get('site');
$page = io_get('p');
$len  = io_get('l');

$errors = array(
    10 => '参数错误: 未指定标签',
);

try {
    if (empty($tag)){
        throw new CmsInterfaceException(10);
        _log("参数错误 tag", 'INFO');
    }

    $controller = $prj->rs->data->ext['tags'];
    $db = $prj->CData->CDb->r;
    WF_Registry::set('db', $db);
    $articles = $controller->listByTag($tag);

    $cost = microtime(true) - $profile_begin;
    $prj->fw->interface->out(0, 'success', $cost, $articles);
}
catch(CmsInterfaceException $e){
    project_output_error($prj, $e, $errors);
}
catch(Exception $e){
    $this->fw->interface->out($e->getCode(), $e->getMessage(), '', '');
}

function _log($msg, $level='ERROR'){ global $prj; $prj->CLog->w($level, $msg);}
