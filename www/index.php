<?php
/*
if ($_SERVER['REMOTE_ADDR']!='78.25.11.247') {
echo '<center><h1>Сайт временно недоступен</h1>
<h2>На сайте ведутся технические работы. Предположительное время окончания 15:00 МСК </h2></center>'; die;
}*/
list($usec, $sec) = explode(" ", microtime());
$time_start = ((float)$usec + (float)$sec);

ini_set('display_errors', 'off');
error_reporting(E_ALL);

define("PATH", $_SERVER['DOCUMENT_ROOT']."/");

/*set_include_path('.'.PATH_SEPARATOR.PATH.'Zend'  
    .PATH_SEPARATOR.get_include_path());*/

require_once PATH . 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

$config = array();

require_once PATH . 'config/config.php';

$config = new Zend_Config($config, true);

Zend_Session::start();
//Zend_Session::namespaceUnset('goods');
require_once PATH . 'library/Init.php';

new Init($config);

//echo mktime(date('H'), date('i'), date('s'), date('m')-1, date('d'), date('Y'));
//echo mktime();
//echo date('d.m.Y H:i:s', 0);
//echo crypt('temp', 'p3k272If').'<br />';
//echo crypt('gfyfvrf', 'tEXFVrqY').'<br />';

//echo stripslashes('fffdds');
//echo json_encode('123');

/*$str = '.'.PATH_SEPARATOR.PATH.'Zend'  
    .PATH_SEPARATOR.get_include_path();*/
//echo "<!-- $str -->";
//echo "<!-- ".PATH_SEPARATOR." -->";

list($usec, $sec) = explode(" ", microtime());
$time_end = ((float)$usec + (float)$sec);

$time = round(($time_end - $time_start), 4);

/*echo "<div align='center'>
    Time of Scripting: $time seconds
     
    </div>";*/

//date_default_timezone_set('America/Los_Angeles');


//echo '<!-- '.$script_tz.' -->';
/*if (strcmp($script_tz, ini_get('date.timezone'))){
    echo '<!-- Script timezone differs from ini-set timezone. -->';
} else {
    echo '<!-- Script timezone and ini-set timezone match. -->';
}*/


//echo crypt('Je5DBsz4', 'p3k272If');
//p39R/xNf7wYLQ
//echo '<br />';
//echo crypt('mK6f196r', 'p3k272If');