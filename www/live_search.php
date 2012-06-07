<?php
session_start();
define("PATH", $_SERVER['DOCUMENT_ROOT']."/");
require_once PATH . 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

$config = array();

require_once PATH . 'config/config.php';

$config = new Zend_Config($config);
$config = $config->toArray();

try {
    $db = Zend_Db::factory($config['database']['adapter'], $config['database']['params']);
    $db->getConnection();
} catch (Zend_Db_Adapter_Exception $e) {
    exit();
} catch (Zend_Exception $e) {
    exit();
}
$q = iconv('utf-8', 'windows-1251',trim($_GET['q']));
//print $q."\n";
$sq = $db->fetchAll("SELECT * FROM `info_delivery` WHERE `city` LIKE '$q%' AND `country`='".$_SESSION['countryDName']."'");
        //print "SELECT * FROM `info_delivery` WHERE `city` LIKE '$q%'\n";
if (!empty($sq)){
    foreach ($sq as $key) {
        //var_dump($key);
        print toDB($key['city']."\n");
    }
}

function toDB($value){
    //$value = iconv('utf-8', 'windows-1251', $value);
    $value = iconv( 'windows-1251','utf-8', $value);
    return $value;
}
/*
        echo $row['author_ru']."|\n";
*/
//for ($i=0;$i<10;$i++)
//print "1\n";
?>