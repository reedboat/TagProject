<?php
require_once 'header/web.inc.php';
require_once 'inc/tags/interface.inc.php';
require_once 'inc/tags/tags.inc.php';

$prj = new Tags_Project('tcms.interface.tags');
project_init_params($prj);

$site = io_get('site');
$id   = io_get('id');
$errors = array(
    10 => '参数有误',
    50 => '获取Tags失败',
);
try {
    if (empty($site) || empty($id)){
        throw new CmsInterfaceException(10);
        _log("提供的参数有误", 'DEBUG');
    }

    $controller = $prj->rs->data->ext['tags'];
    $db = $prj->CData->CDb->r;
    WF_Registry::set('db', $db);
    $tags  = $controller ->getNewsTags($site, $id);

    //$tags = array('iphone', '苹果', '手机');
    if ($tags === false){
        throw new CmsInterfaceException(50);
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
