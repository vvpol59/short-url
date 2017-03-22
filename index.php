<?php
/**
 *
 * User: vvpol
 * Date: 20.03.2017
 * Time: 21:19
 */

function err_proc($code, $msg, $file, $line) {
    $errorMessage = 'Error(err_proc) ' . $msg . ' [' . $code . '] on ' . $file . ' in line ' . $line;
    throw new Exception($errorMessage);
}

error_reporting( E_ALL );
ini_set( 'display_errors', 'On' );
ini_set( 'display_startup_errors', 1);
set_error_handler("err_proc");
date_default_timezone_set("Europe/Moscow");

require_once 'classes/core.php';
core::run();
