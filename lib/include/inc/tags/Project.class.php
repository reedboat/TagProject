<?php
class Tags_Project extends TProject {
    private $pidFileName = '';
    private $lockFileName = '';
    public function __construct($idListString, $param = array()){
        $this->dbClassName = 'WF_Db';
        parent::__construct($idListString, $param);
    }

    public function init(){
        parent::init();
        $vars = get_object_vars($this->CData->CDb);
        $keys = array_keys($vars);
        $logger = new WF_Logger($this->CLog);
        WF_Registry::set('logger', $logger);
    }
}
