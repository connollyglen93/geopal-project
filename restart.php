<?php
/**
 * Created by PhpStorm.
 * User: glen.connolly
 * Date: 07/09/2020
 * Time: 15:36
 */
if (session_status() == PHP_SESSION_ACTIVE) {
    session_destroy();
}
session_start();