<?php
error_reporting(0);
#prject util functions

function project_init_params($prj, $method='get', $default_encoding='utf-8') {
    $query = "io_" . $method;
    $of = $query('of') == 'xml' ? 'xml' : 'json';
    $oe = in_array($query('oe'), array('gbk', 'utf-8', 'gb2312')) ? $query('oe') : 'gbk';
    $ver = $query('ver');
    $prj->fw->interface->ie = $oe;
    $prj->fw->interface->oe = $oe;
    $prj->fw->interface->of = $of;
    $prj->fw->interface->de = $default_encoding;
}

function project_convert_input($prj, $data){
    $de = $prj->fw->interface->de; 
    $ie = $prj->fw->interface->ie; 
    if ($de != $ie){
        if (is_string($data)){
            $data = mb_convert_encoding($data, $de, $ie);
        }
        else if (is_array($data)){
            foreach($data as $key => $item){
                $data[$key] = project_convert_output($prj, $item);
            } 
        }
    }
    return $data;
}

function project_convert_output($prj, $data){
    $de = $prj->fw->interface->de; 
    $oe = $prj->fw->interface->oe; 
    if ($de != $oe){
        if (is_string($data)){
            $data = mb_convert_encoding($data, $oe, $de);
        }
        else if (is_array($data)){
            $result = array();
            foreach($data as $key => $item){
                $key = mb_convert_encoding($key, $oe, $de);
                $result[$key] = project_convert_output($prj, $item);
            } 
            return $result;
        }
    }
    return $data;
}

function project_output_error($prj, $e, $errors){
    $code = $e->getMessage();
    if ($code > 0){
        $msg = isset($errors[$code]) ? $errors[$code]:'';
    }
    else{
        $code = 99;
        $msg  = '未知异常';
    }
    $msg = project_convert_output($prj, $msg);
    $prj->fw->interface->out($code, $level='error', '', $msg);
}

class CmsInterfaceException extends Exception {}
