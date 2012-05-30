<?php

//ini_set(default_charset,"UTF-8");

# include data base
//require "../../mysql.inc.php";
define("PATH", $_SERVER['DOCUMENT_ROOT']."/");
require_once PATH . 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

$config = array();
$config = array(
    'database' => array(
        'adapter'   => 'PDO_MYSQL',
        'params'    => array(
            'host'              => 'localhost',
            'username'          => 'upline24ru',
            'password'          => '4HUYAgxx',
            'dbname'            => 'upline24ru',
            'driver_options'    => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),
            'profiler'          => false
        )
    ),
    'language' => array(
        'defaultLanguage'   => 'ru',
        'allowLanguage'     => array(
            'ru' => 'ru',
            //'en' => 'en',
            //'de' => 'de',
            //'fr' => 'fr',
            //'it' => 'it',
        ),
        'useDefLangPath'    => false
    )
);


$config = new Zend_Config($config, true);

      
    	
		try {
			$database = Zend_Db::factory($config->database);
    		$database->getConnection();
		} catch (Zend_Db_Adapter_Exception $e) {
			throw new Exception('возможно, неправильные параметры соединения или СУРБД не запущена');
		} catch (Zend_Exception $e) {
    		throw new Exception('возможно, попытка загрузки требуемого класса адаптера потерпела неудачу');
		}
    	
		$zendSessionNameSpace = new Zend_Session_Namespace('Zend_Auth');		
  
$weight = (int)$_POST['count']*60;
if ($weight<500) {
    $weight='weight1';
} elseif ($weight>=500 AND $weight<1000) {
    $weight='weight2';
} elseif ($weight>=1000 AND $weight<1500) {
    $weight='weight3';
} else {
    $weight='weight4';
}

switch ($_POST['action']){
                
        case "showRegionForInsert":
       /*         echo '<select size="1" name="region" onchange="javascript:selectCity();">';
                $rows = $database->fetchAll('SELECT * FROM `calculate_shipping` WHERE id>"'.$_POST['id_country'].'" LIMIT 2');
                
                foreach ($rows as $row) {
                
                    echo '<option value="'.$row['id'].'">'.$row['punkt'].'</option>';
                };
                
                echo '</select>';*/
                $rows = $database->fetchAll('SELECT * FROM `calculate_shipping` WHERE id>"'.$_POST['id_country'].'" LIMIT 2');
            echo '<div>&nbsp;</div>
<div>&nbsp;</div><div>&nbsp;</div>                
<table>';
                foreach ($rows as $row) {
            echo '<tr><td style="width:150px;">'.$row['punkt'].'</td><td  style="width:150px;">'.$row['period'].'</td><td  style="width:150px;">'.$row[$weight].' руб.</td></tr>';
                }
                echo '</table>';
                break;
                
        case "showCityForInsert":
            $rows = $this->db->fetchAll('SELECT * FROM `calculate_shipping` WHERE type=1');
                foreach ($rows as $row) {
            echo $row['punkt'].' '.$row['period'].' '.$row['weight1'];
                }
/*                echo '<select size="1" name="city" >';
             
                $rows = $this->db->fetchAll('SELECT * FROM `calculate_shipping` WHERE type=1');
                foreach ($rows as $row) {
                        echo '<option value="'.$row['id'].'">'.$row['punkt'].'</option>';
                };
                echo '</select>';*/
                break;        
    
                
};

?>
