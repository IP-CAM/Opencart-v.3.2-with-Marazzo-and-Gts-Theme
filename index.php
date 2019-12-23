<?php
// Version
define('VERSION', '3.0.2.0');

// Configuration
if (is_file('config.php')) {
	require_once('config.php');
}

// Install
if (!defined('DIR_APPLICATION')) {
	header('Location: install/index.php');
	exit;
}

// Startup
require_once(DIR_SYSTEM . 'startup.php');

start('catalog');

function debug($arr, $die = 0, $print = 0){
    echo '<pre>';
    if($print){
        print_r($arr);
    }else{
        var_dump($arr);
    }
    echo '</pre>';
    if($die){
        die();
    }
}