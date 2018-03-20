<?php   
     
     if($dumpResults){  
        ob_start();
        var_dump($result);
        $debug = ob_get_clean();
        array_push($query_msg, $debug . NEWLINE);
      }
    
    if($showQueries){
        if(is_bool($result)) {
            array_push($query_msg,  $query . ';' . NEWLINE);
            
            if( mysqli_errno($db) > 0 ) {
                array_push($error_msg,  'Error# '. mysqli_errno($db) . ": " . mysqli_error($db));
            }
        } else {
            
                 if($showCounts){
                    array_push($query_msg,  $query . ';');
                    array_push($query_msg,  "Result Set Count: ". mysqli_num_rows($result). NEWLINE);
                 } else {
                     array_push($query_msg,  $query . ';'. NEWLINE);
                 }
            } 
        }
?>