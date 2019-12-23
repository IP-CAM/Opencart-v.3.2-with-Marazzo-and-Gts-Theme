<?php
// Version
define('VERSION', '3.0.2.0');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: ../install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('admin');


function debug($arr, $var = 1 , $die = 0){
    echo '<pre>';
    if($var == 1){
        var_dump($arr);
    }else{
        print_r($arr);
    }
    echo '</pre>';
    if($die){
        die();
    }
}