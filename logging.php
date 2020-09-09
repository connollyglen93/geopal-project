<?php
/**
 * Created by PhpStorm.
 * User: glen.connolly
 * Date: 07/09/2020
 * Time: 19:09
 */
function writeLog($message){
    $logfile = "log/" . date('Y_m_d') . ".txt";

    $handle = fopen($logfile, 'a');

    if($handle) {
        fwrite($handle, $message);

        fclose($handle);
    }
}

