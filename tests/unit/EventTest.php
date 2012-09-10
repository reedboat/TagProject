<?php
class EventTest extends PHPUnit_Framework_TestCase {
    public function testBind(){
        function handler($event){
            echo 'handle:' . $event->name;
        }
        WF_Event::bind('click', handler);
        WF_Event::bind('click', handler);
    }

    public function 
}
?>
