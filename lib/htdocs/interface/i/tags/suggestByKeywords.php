<?php
/**
 * 提供文章的分词结果Top3，来搜索相近的Tags
 */
require_once 'header/web.inc.php';
require_once 'inc/tags/interface.inc.php';
require_once 'inc/tags/tags.inc.php';

$prj = new Tags_Project('tcms.interface.tags');

project_init_params($prj);

$len          = io_get('len');
#$category_id = io_get('c');
$keywords_str = trim(io_get('keywords'));
$input        = io_get('input');

$errmsgs = array(
    10 => '关键词为空哦亲',
    50 => '接口调用出错啦',
);

try {
    if (empty($keywords_str))
    {
        throw new CmsInterfaceException(10);
        _log('输入关键词为空', 'DEBUG');
    }

    $len  = $len > 0 ? $len : 20;

    $prefix = project_convert_input($prj, $keywords_str);

    $controller = $prj->rs->data->ext['tags'];
    $db = $prj->CData->CDb->r;
    WF_Registry::set('db', $db);
    $keywords = explode(';', $keywords_str);
    $tags = $controller->suggestByKeywords($keywords);

    if (!is_array($tags))
    {
        throw new CmsInterfaceException(50);
        _log("suggest 接口调用出错");
    }

    $data = project_convert_output($prj, $tags);
    $prj->fw->interface->out(0, 'success', '', $data);
}
catch (CmsInterfaceException $e){
    project_output_error($prj, $e, $msgs);
}
catch (Exception $e){
    $this->fw->interface->out($e->getCode(), $e->getMessage(), '', '');
}


function _log($msg, $level='ERROR'){ global $prj; $prj->CLog->w($level, $msg);}
