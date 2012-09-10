<?php
require_once 'header/web.inc.php';
require_once 'inc/tags/interface.inc.php';
require_once 'inc/tags/tags.inc.php';

$profile_begin = microtime(true);

$prj = new Tags_Project('tcms.interface.tags');
$method ='post';
project_init_params($prj, $method);
$query = 'io_' . $method;

$site      = $query('site');
$news_id   = $query('news_id');
$title     = trim($query('title'));
$abstract  = trim($query('abstract'));
$pub_time  = $query('pub_time');
$thumbnail = $query('thumbnail');

$errors = array(
    10 => 'Invalid Arguments',
    50 => 'Add article failed',
);

try{
    if (empty($site) || empty($news_id) || empty($title) || empty($pub_time)){
        _log("params error in addArticle site:$site, $news_id, $news_id, title:$title", "ERROR");
        throw new CmsInterfaceException(10);
    }

    $controller = $prj->rs->data->ext['tags'];
    $db = $prj->CData->CDb->w;
    WF_Registry::set('db', $db);
    $title = project_convert_input($prj, $title);
    $abstract = project_convert_input($prj, strval($abstract));
    $result = $controller->addArticle($site, $news_id, $title, $pub_time, $abstract, $thumbnail);
    if ($result === false){
        throw new CmsInterfaceException(50);
    }
    $cost = microtime(true) - $profile_begin;
    $prj->fw->interface->out(0, 'success', $cost, $result);
}
catch(CmsInterfaceException $e){
    project_output_error($prj, $e, $errors);
}
catch(Exception $e){
    $this->fw->interface->out($e->getCode(), $e->getMessage(), '', '');
}
function _log($msg, $level='ERROR'){ global $prj; $prj->CLog->w($level, $msg);}
?>
