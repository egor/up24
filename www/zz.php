<?php

phpinfo();
die();


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

$tickets = $db->fetchAll("SELECT * FROM `tickets`");

if (!$tickets) {
    die('No tickets');
}

foreach ($tickets as $row) {
    if ($row['count'] > 1) {
        for ($i=0; $i<$row['count']; $i++) {
            $code = generateScannerCode($db);
        
            $data = array(
                'ticket_id' => $row['id'],
                'code' => $code
            );
            
            $db->insert('tickets_code', $data);
        }
    } else {
        $code = generateScannerCode($db);
        
        $data = array(
            'ticket_id' => $row['id'],
            'code' => $code
        );
        
        $db->insert('tickets_code', $data);
    }
    
    $code = generateCode($db);
    
    $data = array(
        'code' => $code
    );
    
    $db->update('tickets', $data, "id = '".$row['id']."'");
}

function generateCode($db)
{
    $code = mt_rand(10000,99999999);
    
    $count = $db->fetchOne("SELECT COUNT(`id`) FROM `tickets` WHERE `code` = '$code'");
    
    if ($count != 0) {
        return $this->generateCode($db);
    }
    
    return $code;
}

function generateScannerCode($db)
{
    $code = mt_rand(10000000,99999999999);
    
    $count = $db->fetchOne("SELECT COUNT(`id`) FROM `tickets_code` WHERE `code` = '$code'");
    
    if ($count != 0) {
        return $this->generateScannerCode($db);
    }
    
    return $code;
}

die('Done');

//$get = array('0' => '1', '1' => '2', '2' => '3');
//foreach ($get as $id) {
    //print_r($id);
//}

//echo mt_rand(10000000,99999999999).'<br />';

//echo microtime().'<br />';
//echo microtime().'<br />';
//echo microtime().'<br />';
//echo microtime().'<br />';
//echo microtime().'<br />';

//echo time();
//echo '<br />';
//echo date('d.m.Y H:i:s', 10700000000);
//echo '<br />';
//echo date('d.m.Y H:i:s', 9999999999);
//phpinfo();
exit();
//

//echo crypt('zaq12wsx', 'p3k272If');

//p39R/xNf7wYLQ

echo date("d.m.Y H:i:s", "1295969587");
echo '<br />';
echo date("d.m.Y H:i:s", "1296032171");
echo '<br />';
echo date("d.m.Y H:i:s", "1296034583");
echo '<br />';
echo date("d.m.Y H:i:s", "1296037395");
echo '<br />';
echo date("d.m.Y H:i:s", "1296037646");
echo '<br />';

/*ini_set('display_errors', 'on');
error_reporting(E_ALL);

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
    //setError('Cannot connect to database\n\n');
    throw new Exception('1');
    //exit();
} catch (Zend_Exception $e) {
    //setError('Cannot load database class\n\n');
    throw new Exception('2');
    //exit();
}

$isOrder = $db->fetchRow("SELECT * FROM `tickets` WHERE `number` = '260111113623636'"); 
print_r($isOrder);
$data = array(
	'payment' => '1'
);

echo $num = $db->update('tickets', $data, "id = '".$isOrder['id']."'");*/