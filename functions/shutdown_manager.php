<?php
/**
 * Created by PhpStorm.
 * User: glen.connolly
 * Date: 07/09/2020
 * Time: 19:00
 */

ini_set('display_errors', '1');

//PHP 5 error handling
set_error_handler(function () {
    $error = error_get_last();
    if ($error !== NULL) {

        $info = "[SHUTDOWN] file:" . $error['file'] . " | ln:" . $error['line'] . " | msg:" . $error['message'] . PHP_EOL;

        writeLog($info);
    }
});