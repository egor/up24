<?php

if (!isset($_POST) || empty($_POST)) {
    //setError('empty POST Data\n\n');
	header('Location: /');
}

/*$post = '';
foreach ($_POST as $key => $value) {
    $post .= $key.' : '.$value.'\n\n';
}*/

if (!isset($_POST['operation_xml']) || empty($_POST['operation_xml'])) {
    //setError('No set "operation_xml" variable\n\n');
	header('Location: /');
}

if (!isset($_POST['signature']) || empty($_POST['signature'])) {
    //setError('No set "operation_xml" variable\n\n');
	header('Location: /');
} 

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
    //throw new Exception('возможно, неправильные параметры соединения или СУРБД не запущена');
    exit();
} catch (Zend_Exception $e) {
    //setError('Cannot load database class\n\n');
    //throw new Exception('возможно, попытка загрузки требуемого класса адаптера потерпела неудачу');
    exit();
}

$operation_xml = $_POST['operation_xml'];
//$operation_xml = "PHJlcXVlc3Q+ICAgICAgCgkJCTx2ZXJzaW9uPjEuMjwvdmVyc2lvbj4KCQkJPG1lcmNoYW50X2lkPmk4NTg0MDUyOTc1PC9tZXJjaGFudF9pZD4KCQkJPHJlc3VsdF91cmw+aHR0cDovL3VwbGluZTI0LmNvbS51YS9idXlzdWNlc3M8L3Jlc3VsdF91cmw+CgkJCTxzZXJ2ZXJfdXJsPmh0dHA6Ly91cGxpbmUyNC5jb20udWEvYnV5Y29uZmlybS5waHA8L3NlcnZlcl91cmw+CgkJCTxvcmRlcl9pZD5PUkRFUl8xNTEyMTAxMjIxMDkxMjwvb3JkZXJfaWQ+CgkJCTxhbW91bnQ+MTA8L2Ftb3VudD4KCQkJPGN1cnJlbmN5PlVBSDwvY3VycmVuY3k+CgkJCTxkZXNjcmlwdGlvbj48L2Rlc2NyaXB0aW9uPgoJCQk8ZGVmYXVsdF9waG9uZT48L2RlZmF1bHRfcGhvbmU+CgkJCTxwYXlfd2F5PmNhcmQ8L3BheV93YXk+CgkJCTwvcmVxdWVzdD4=";

$xml = base64_decode($operation_xml);

$merc_sign = "X5bP9JQJauTARvht83xMKNLXY2qF";
$sign = base64_encode(sha1($merc_sign.$xml.$merc_sign,1));

if ($_POST['signature'] !== $sign) {
    setError('Retrive variable no compare origin');
	exit();
}

//print_r($xml);
$xmlObj = new SimpleXMLElement($xml);
//print_r($xmlObj);
//echo $xmlObj->version;
$orderId = null;
$transactionId = 0;

verifyXML($xmlObj);

$data = array(
    'date' => mktime(),
    'order_id' => $xmlObj->order_id,
    'amount' => $xmlObj->amount,
    'status' => $xmlObj->status,
    'code' => $xmlObj->code,
    'transaction_id' => $xmlObj->transaction_id,
    'sender_phone' => $xmlObj->sender_phone
);

$db->insert('transactions', $data);

if (!$orderId || $transactionId < 1) {
    //setError('Invalid xml variables\n\n');
    setError('Invalid xml variables');
	exit();
}

$isOrder = $db->fetchRow("SELECT * FROM `order_disc` WHERE `id` = '$orderId'");

if (!$isOrder) {
    setError('NO ORDER');
	exit();
}

//$summ = (float) $db->fetchOne("SELECT SUMM(`summ`) FROM `events`, `event_types` WHERE `events`.`type_id` = `event_types`.`id` AND `events`.`id` = '".$isOrder['event_id']."'");

//$total_cost = (float) ((int) $isOrder['count'] * $cost);

// TODO
// Проблема с суммой заказа
// Придумать обработку!!!
//if ($total_cost != (float) $xmlObj->amount) {
    //setError('Invalid payment summ');
    //setError('Invalid payment summ\n\n');
	//exit();
//}

$data = array(
	'status' => '2'
);

$db->update('order_disc', $data, "`id` = '$orderId'");

exit();

function verifyXML($xml) {
	global $orderId, $transactionId;
	
	@$version = $xml->version;
	@$merchant_id = $xml->merchant_id;
	@$order_id = $xml->order_id;
	@$cost = $xml->amount;
	@$pay_way = $xml->pay_way;
	@$status = $xml->status;
	@$code = $xml->code;
	@$transaction_id = $xml->transaction_id;
	
	if (!$version || !$merchant_id || !$order_id || !$cost || !$pay_way || !$status || !$code || !$transaction_id) {
	    //setError('Invalid XML structure\n\n');
	    setError('Invalid XML structure');
		exit();
	}
	
	if ($version != '1.2') {
	    //setError('Liqpay version is invalid\n\n');
	    setError('Liqpay version is invalid');
		exit();
	}
	
	if ($merchant_id != 'i8453807395') {
	    //setError('Merchant ID is invalid\n\n');
	    setError('Merchant ID is invalid');
		exit();
	}
	
	if ($pay_way != 'card') {
	    //setError('Payment type is invalid\n\n');
	    setError('Payment type is invalid');
		exit();
	}
	
	// TODO
	// Добавить проверки и обработку других статусов
	if ($status != 'success') {
	    //setError('Pay status is invalid: '.$status.'\n\n');
	    setError('Pay status is invalid: '.$status);
		exit();
	}
	
	// TODO
	// Добавить обработчик кодов ошибок
	if ($code != '') {
	    //setError('Error code: '.$code.'\n\n');
	    setError('Error code: '.$code);
		exit();
	}
	
	if (!$transaction_id) {
	    //setError('Invalid Transaction ID: '.$transaction_id.'\n\n');
	    setError('Invalid Transaction ID: '.$transaction_id);
		exit();
	}
	
	$transactionId = (int) $transaction_id;
	
	$order = explode('_', $order_id);
	
	if (!isset($order[1]) || empty($order[1])) {
	    //setError('Invalid ORDER ID variable #1\n\n');
	    setError('Invalid ORDER ID variable #1');
		exit();
	}
	
	if ($order[0] !== 'ORDER') {
	    //setError('Invalid ORDER ID variable #2\n\n');
	    setError('Invalid ORDER ID variable #2');
		exit();
	}
	
	if (!ctype_digit($order[1])) {
	    //setError('Invalid ORDER ID variable #3\n\n');
	    setError('Invalid ORDER ID variable #3');
		exit();
	}
	
	$orderId = $order[1];
}

function setError($message) {
    global $db, $operation_xml, $xml;
    
    $data = array(
        'date' => date('Y-m-d H:i:s'),
        'operation_xml' => $operation_xml,
        'xml' => $xml,
        'message' => $message
    );
    
    $db->insert('payment_error', $data);
    /*$fp = fopen('errorBuy.txt', 'w+');
    
    if (!$fp) {
        return;
    }
    
    @fwrite($fp, $message);
    @fclose($fp);
    @chmod('errorBuy.txt', 0777);*/
}