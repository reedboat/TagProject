<?php
require_once 'header/web.inc.php';
require_once 'inc/tags/interface.inc.php';
require_once 'inc/tags/tags.inc.php';

class TagsPrjTest extends DbTestCase {
    public function __construct(){
        $this->prj = new Tags_project('tcms.interface.tags');
        DB::setInstance($this->prj->CData->CDb->r);
        $this->tagsController = $this->prj->rs->data->ext['tags'];
    }

    //with cache
    public function testListByTag(){
        //1
        $tagsController = $this->tagsController;
        $tag = 'iphone';
        // create item in cache
        $articles = $tagsController->listByTag($tag, 0);
    }
}
?>
