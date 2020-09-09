<?php
/**
 * Created by PhpStorm.
 * User: glen.connolly
 * Date: 07/09/2020
 * Time: 19:00
 */
include_once 'logging.php';

set_error_handler(function(){
    $error = error_get_last();
    if($error !== NULL){

        $info = "[SHUTDOWN] file:".$error['file']." | ln:".$error['line']." | msg:".$error['message'] .PHP_EOL;

        writeLog($info);

    }
});