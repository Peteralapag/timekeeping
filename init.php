<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
ini_set('default_mimetype', 'text/html');
ini_set('default_charset', 'utf-8');
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
require ROOT_PATH ."/Sources/db_config.php";
define('DB_HOST', $dbhost);
define('DB_USER', $dbuser);
define('DB_PASSWORD', $dbpass);
define('DB_NAME', $dbname);
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require ROOT_PATH . "/Classes/Class.encrypted_password.php";
require ROOT_PATH . "/Classes/Class.themes.php";
require ROOT_PATH . "/Classes/Class.main_app_config.php";
require ROOT_PATH . "/Classes/Class.access.php";
require ROOT_PATH . "/Classes/Class.functions.php";
$checkAppAccess = new AppAccess;
$encpass = new Password;
$Themes = new Themes;
$Main = new Main;
$functions = new TKFunctions;
?>