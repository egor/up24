<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL);

define("PATH", $_SERVER['DOCUMENT_ROOT']."/");

/*set_include_path('.'.PATH_SEPARATOR.PATH.'Zend'  
    .PATH_SEPARATOR.get_include_path());*/

require_once PATH . 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

/*Zend_Barcode::render(
    'Ean13',
    'image',
    array(
        'text' => 1234567890123,
        'font' => 3
    )
);*/

/*$barcodeOptions = array('text' => 999999999999);

$bc = Zend_Barcode::factory(
    'ean13',
    'image',
    $barcodeOptions,
    array()
);

$res = $bc->draw();
$filename = tempnam(PATH . 'barCode', 'image').'.png';
imagepng($res, $filename);*/

Zend_Barcode::render(
    'ean13',
    'image',
    array(
        'text' => '999999999999',
        'font' => 3,
        'imagetype' => 'jpeg'
    )
);