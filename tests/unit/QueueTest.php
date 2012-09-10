<?php
/**
 * WF_Queue TestCase
 **/
class QueueTest extends PHPUnit_Framework_TestCase
{
    public function testQueue() {
        $queue = new WF_Queue();
        $this->assertEquals(0, $queue->len());
        
        $item1 = 'item1';
        $item2 = 'item2';
        $this->assertTrue($queue->add($item1));
        $this->assertTrue($queue->add($item2));
        $this->assertEquals(2, $queue->len());

        $this->assertEquals($item1, $queue->fetch());
        $this->assertEquals($item2, $queue->fetch());
        $this->assertEquals(0, $queue->len());
    }   
}
?>
