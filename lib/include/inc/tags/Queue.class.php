<?php
class WF_Queue {
    public function add($item){
        return true;
    }
    public function fetch(){
        return $item;
    }
    public function len(){
        return $len;
    }
}
