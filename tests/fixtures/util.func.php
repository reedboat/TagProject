<?php
function util_genId($id){
    return date("Ymd") . sprintf("%07s", $id);
}
function util_datetime($hour=null, $date=null){
    #return date("m/d/y g:i:s A");
    if ($hour !== null) {
        if ($date == 0) {
            $time = time() / 86400 * 86400 + $hour * 3600;
        } else {
            $time = strtotime($date) + $hour * 3600;
        }
        return date('c', $time);
    }
    else {
        return date("c");
    }
}
function util_time($hour = 0){
   $time_base = time() / 86400 * 86400 ;
   return $time_base + $hour * 3600;
}
?>
