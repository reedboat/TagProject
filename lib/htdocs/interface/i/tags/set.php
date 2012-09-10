<?php
require_once 'header/web.inc.php';
require_once 'inc/tags/interface.inc.php';
require_once 'inc/tags/tags.inc.php';

$prj = new Tags_Project('tcms.interface.tags');
$method = "post";
$query  = 'io_' . $method;
project_init_params($prj, $method);

$site = $query('site');
$id   = $query('id');
$tags_str = trim($query('tags'));

$errors = array(
    10 => '参数错误: site或Id未提供',
    11 => '参数错误：未指定标签',
    90 => '处理错误: 设定标签失败'
);

try {
    if (empty($site) || empty($id)){
        throw new CmsInterfaceException(10);
        _log("参数错误 site:$site;id:$id;tags_str:$tags_str", 'DEBUG');
    }
    if (empty($tags_str)){
        throw new CmsInterfaceException(11);
        _log("未指定标签, site:$site;id:$id", 'DEBUG');
    }

    //过滤输入, 转义输出
    $tags_str = strip_tags($tags_str);
    $tags_str = project_convert_input($prj, $tags_str);
    $tags = ArticleTags::model()->str2arr($tags_str);
    
    $controller = $prj->rs->data->ext['tags'];
    $db = $prj->CData->CDb->w;
    WF_Registry::set('db', $db);
    $result = $controller->setNewsTags($site, $id, $tags);

    if (!$result){
        throw new CmsInterfaceException(90);
        _log("标签设定失败, site:$site; id:$id; tags:$tags_str", 'ERROR');
    }
    $prj->fw->interface->out(0, 'success', '', true);
}
catch(CmsInterfaceException $e){
    project_output_error($prj, $e, $errors);
}
catch(Exception $e){
    $this->fw->interface->out($e->getCode(), $e->getMessage(), '', '');
}

function _log($msg, $level='ERROR'){ global $prj; $prj->CLog->w($level, $msg);}
