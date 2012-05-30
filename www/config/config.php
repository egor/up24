<?php

$config = array(
    'database' => array(
        'adapter'   => 'PDO_MYSQL',
        'params'    => array(
            'host'              => 'localhost',
            'username'          => 'root',
            'password'          => '',
            'dbname'            => 'upline24',
            'driver_options'    => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES cp1251'),
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


/*----------------------------------------------------------------------------*/
//Общие настройки
//название сайта
$site[0]['ru']='upline24.ru';
$site[0]['ua']='upline24.ru';
//Выводит при покупки liqpay
$site[1]['ru']='Tickets of Diamond Alliance Russia';
$site[1]['ua']='Tickets of Diamond Alliance Ukraine';
//Код $merc_sign
//$site[2]['ru']='X5bP9JQJauTARvht83xMKNLXY2qF';
//$site[2]['ua']='RQapztnuPjiQ0ddSn5mUu1BVhcvBQC';
$site[2]['ru']='HOk9G4GTkmyvJ7QrHciZiEcfwx1FeZLBf2CtH';
$site[2]['ua']='HOk9G4GTkmyvJ7QrHciZiEcfwx1FeZLBf2CtH';
//merchant_id
//$site[3]['ru']='i8453807395';
//$site[3]['ua']='i6069171598'; 
$site[3]['ru']='i5298950203';
$site[3]['ua']='i5298950203';

$site[4]['ru']='CD-discs of Diamond Alliance Russia';
$site[4]['ua']='CD-discs of Diamond Alliance Ukraine';
//facebook
$site[5]['ru']='UpLine24';
$site[5]['ua']='UpLine24';
//Подсчет доставки дисков 0 - выкл., 1 - вкл.
$site[6]['ru']=1;
$site[6]['ua']=0;
//Код страны
$site[7]['ru']='';
$site[7]['ua']='';
//Настройки валюты
$money[0]['ru']='рубль';
$money[1]['ru']='рубля';
$money[2]['ru']='рублей';
$money[3]['ru']='руб';
$money[4]['ru']='RUR';

$money[0]['ua']='гривна';
$money[1]['ua']='гривны';
$money[2]['ua']='гривен';
$money[3]['ua']='грн';
$money[4]['ua']='UAH';

/*
 * В файле init.php и logger.php все оставил как есть
 * с js все остается как есть
*/