<?php
/**
 * 该接口可以传入前缀，而搜索相关的Tags
 * 或提供文章的分词结果Top3，来搜索相近的Tags
 */
require_once 'header/web.inc.php';
require_once 'inc/tags/interface.inc.php';
require_once 'inc/tags/tags.inc.php';

$prj = new Tags_Project('tcms.interface.tags');

project_init_params($prj);

$len          = io_get('len');
#$category_id = io_get('c');
$keywords_str = io_get('keywords');
$input        = io_get('input');

$errors = array(
    10 => '查询为空',
    50 => '接口调用出错',
);

try {
    if (empty($input))
    {
        throw new CmsInterfaceException(10);
        _log('输入前缀为空', 'DEBUG');
    }

    $len  = $len > 0 ? $len : 20;

    $prefix = project_convert_input($prj, $input);

    $controller = $prj->rs->data->ext['tags'];
    $db = $prj->CData->CDb->r;
    WF_Registry::set('db', $db);
    $tags = $controller->suggestByInput($input);

    if (!is_array($tags))
    {
        throw new CmsInterfaceException(50);
        _log("suggest 接口调用出错");
    }

    $data = project_convert_output($prj, $tags);
    $prj->fw->interface->out(0, 'success', '', $data);
}
catch (CmsInterfaceException $e){
    project_output_error($prj, $e, $errors);
}
catch (Exception $e){
    $this->fw->interface->out($e->getCode(), $e->getMessage(), '', '');
}


function _log($msg, $level='ERROR'){ global $prj; $prj->CLog->w($level, $msg);}
