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
//Добавление адреса
if ($_POST['method']=='edit'){
        //echo '1';id_add
        $id_add         = toDB($_POST['id_add']);
        
        $slot_id        = toDB($_POST['slot_id']);
        $user_id        = toDB($_POST['user_id']);
        $form           = toDB($_POST['form']);
        $adr_name       = toDB($_POST['adr_name']);
        $index          = toDB($_POST['index']);
        $city           = toDB($_POST['city']);
        $adr            = toDB($_POST['adr']);
        $phone          = toDB($_POST['phone']);
        $fio            = toDB($_POST['fio']);
        $passport_data  = toDB($_POST['passport_data']);
        $add_info       = toDB($_POST['add_info']);

        $data = array(
            
            
            'adr_name'      => $adr_name,
            'index'         => $index,
            'city'          => $city,
            'adr'           => $adr,
            'phone'         => $phone,
            'fio'           => $fio,
            'passport_data' => $passport_data,
            'add_info'      => $add_info,
            'country'       =>  $_SESSION['countryDName'],
        );
        
     $db->update('user_adr', $data, '`user_id`="'.$user_id.'" AND `slot`="'.$slot_id.'" AND `country`="'.$_SESSION['countryDName'].'"');

     echo toDB2('<div id="adr_md_'.$slot_id.'">
                <a href="#" '.($form==1?'onClick="check_slot('.$slot_id.'); return false;"':'onClick="slot('.$slot_id.', \'edit\', 0)"').' name="">                    
                        <div class="ms_add_adr">
                            <div class="ms_add_adr_content">
                                <div class="ms_add_adr_name">'.substr($adr_name,0,25).'...</div>
                                <div class="ms_add_adr_fio">'.$fio.'</div>
                                <div class="ms_add_adr_adr">'.$city.', '.$adr.'</div>
                            </div>
                            <span href="#" onClick="slot('.$slot_id.', \'edit\', '.($form=='form'?'1':'0').'); return false;" name="modal">
                                <div class="ms_add_adr_inner_ed">
                                    <div class="ms_edit_adr_eadr"><img src="/img/edit_adr.gif"></div>
                                    <div class="ms_edit_adr_etext">Редактировать адрес</div>
                                </div>
                            </span>
                        </div>    
                    </a></div>');
     
}
elseif ($_POST['method']=='new') {
    $id = (int)($_POST['user_id_add']);
        $form           = toDB($_POST['form']);
        $user_id        = toDB($_POST['user_id']);
        $slot_id        = toDB($_POST['slot_id']);
        $adr_name       = toDB($_POST['adr_name']);
        $index          = toDB($_POST['index']);
        $city           = toDB($_POST['city']);
        $adr            = toDB($_POST['adr']);
        $phone          = toDB($_POST['phone']);
        $fio            = toDB($_POST['fio']);
        $passport_data  = toDB($_POST['passport_data']);
        $add_info       = toDB($_POST['add_info']);
        $data = array(
            'user_id'       => $user_id,
            'adr_name'      => $adr_name,
            'index'         => $index,
            'city'          => $city,
            'adr'           => $adr,
            'phone'         => $phone,
            'fio'           => $fio,
            'passport_data' => $passport_data,
            'add_info'      => $add_info,
            'slot'          => $slot_id,
            'country'       => $_SESSION['countryDName'],
        );
        $db->insert('user_adr', $data);

        echo toDB2('
                <a href="#" '.($form==1?'onClick="check_slot('.$slot_id.'); return false;"':'onClick="slot('.$slot_id.', \'edit\', 0)"').' name="">                    
                
                        <div class="ms_add_adr">
                            <div class="ms_add_adr_content">
                                <div class="ms_add_adr_name">'.substr($adr_name,0,25).'...</div>
                                <div class="ms_add_adr_fio">'.$fio.'</div>
                                <div class="ms_add_adr_adr">'.$city.', '.$adr.'</div>
                            </div>
                            <a href="#" onClick="slot('.$slot_id.', \'edit\'); return false;" name="">
                                <div class="ms_add_adr_inner_ed">
                                    <div class="ms_edit_adr_eadr"><img src="/img/edit_adr.gif"></div>
                                    <div class="ms_edit_adr_etext">Редактировать адрес</div>
                                </div>
                            </a>
                        </div>    
                    </a>');
}
if (isset($_POST['id_delete'])){
    $db->delete('user_adr', '`user_id`="'.$_POST['id_delete'].'" AND `slot`="'.$_POST['slot'].'" AND `country`="'.$_SESSION['countryDName'].'"');
    echo 'Deleted!'.$_POST['id_delete'];
}
function toDB($value){
    $value = iconv('utf-8', 'windows-1251', $value);
    return $value;
}
function toDB2($value){
    //$value = iconv('utf-8', 'windows-1251', $value);
    $value = iconv( 'windows-1251','utf-8', $value);
    return $value;
}