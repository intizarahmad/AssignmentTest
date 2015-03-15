<?php
date_default_timezone_set('UTC');
if (DEVELOPMENT_ENVIRONMENT == true) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 'Off');
}

// Define the REPOSITORY_URL;
define('REPOSITORY_URL','https://api.github.com/repos/');

//Include the system files 
include_once 'app/base/Service.class.php';
include_once 'app/parser/IParser.php';
include_once 'app/parser/JSONParser.class.php';
include_once 'app/parser/XMLParser.class.php';
include_once 'app/base/Factory.class.php';

