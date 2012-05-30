<?php
session_start();
if ($_POST['cancel']==1) {
    
    foreach ($_SESSION['userBuy'] as $key => $value) {
        $arr = explode('|', $value);
        if ($arr[0]==$_POST['event'] AND $arr[1]==$_POST['row'] AND $arr[2]==$_POST['loc'] AND $arr[3]==$_POST['id_sector']) {
            unset ($_SESSION['userBuy'][$key]);
        }
    }
} else {
    $_SESSION['userBuy'][]=$_POST['event'].'|'.$_POST['row'].'|'.$_POST['loc'].'|'.$_POST['id_sector'].'|'.$_SESSION['countryDName'];
}
$text = 'Итого заказано билетов <b>'.count($_SESSION['userBuy']).' шт.</b> на сумму <span>'.number_format (($_SESSION['event_cost']*count($_SESSION['userBuy'])), 0, ',', ' ').' '.($_SESSION['countryDName']=='ua'?' грн.':' руб.').'</span>';
if (count($_SESSION['userBuy'])>0)
    //echo iconv('windows-1251', 'utf-8', $text);
    echo toDB($text);
else
    echo toDB('');

function toDB($value){
    //$value = iconv('utf-8', 'windows-1251', $value);
    $value = iconv( 'windows-1251','utf-8', $value);
    return $value;
}