<?php 
session_start();
echo toDB ("<script type=\"text/javascript\">
    $(document).ready(function() {
        $('.requiredField').live('blur',function() {
            if($(this).val() != ''){
                if($(this).hasClass('email')) {
                    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                    if(!emailReg.test(jQuery.trim($(this).val()))) {
                        $(this).parent().find('.error').html('E-Mail адрес не правильный.');
                    }else{
                        $(this).parent().find('.error').remove();
                    }
		}else{
                    $(this).parent().find('.error').remove();
		}
            }else{
	       $(this).parent().find('.error').show();
            }
        });
        $('#buttons').live('click',function() {
            $('#contactForm .error').remove();
            var hasError = false;
            $('.requiredField').each(function() {
                if(jQuery.trim($(this).val()) == '') {
                    var labelText = $(this).prev('label').text();
                    $(this).parent().append('<span class=\"error\">Заполните поле \"'+labelText.replace(':','')+'\"</span>');
                    hasError = true;
		} else if($(this).hasClass('email')) {
                    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                    if(!emailReg.test(jQuery.trim($(this).val()))) {
                        $(this).parent().append('<span class=\"error\">E-Mail адрес не правильный.</span>');
                        hasError = true;
                    }
		}
            });
            if(hasError) {
                return false;
            } else {
                send_data_save();
		$('#mask, .window_ed').hide();
		$(this).hide();
                //live('click',function() {
		//$('#info_city').hide();
                //});
                del_div_info();
                return false;
            }   
	});
    });
    function send_data_save() {
        var adr_name = $('#adr_name').val();
        var index = $('#index').val();
        var city = $('#city').val();
        var adr = $('#adr').val();
        var phone = $('#phone').val();
        var fio = $('#fio').val();
        var passport_data = $('#passport_data').val();
        var add_info = $('#add_info').val();
        var user_id = $('#global_user_id').val();
        var form = $('#global_form').val();
        var method = $('#method').val();
        var id_add = $('#id_add').val();
        var slot_id = $('#slot_id').val();
        
        $(\"#adr_md_\"+slot_id).append('Загрузка...');
        $.ajax({
            type: \"POST\",
            url: \"/save_my_data.php\",
            data: 'user_id='+user_id+
                '&adr_name='+adr_name+
                '&index='+index+
                '&city='+city+
                '&adr='+adr+
                '&phone='+phone+
                '&fio='+fio+
                '&passport_data='+passport_data+
                '&add_info='+add_info+
                '&id_add='+id_add+
                '&method='+method+
                '&form='+form+
                '&slot_id='+slot_id,
            
            success: function(html) {
                
                $(\"#adr_md_\"+slot_id).empty();
                $(\"#adr_md_\"+slot_id).append(html);
                
            }
        });
    }
    function delete_info(uid, slot) {
        if (!confirm(\"Адрес удалится безвозвратно!\")) {
            return false;
        }
        var ht = '<a href=\"#\" onClick=\"slot('+slot+', \'new\'); return false;\" name=\"\">'+
            '<div class=\"ms_add_adr\">'+
            '<div class=\"ms_add_adr_inner_text\">'+
            '<div class=\"ms_add_adr_img\"><img src=\"/img/add_adr.gif\"></div>'+
            '<div class=\"ms_add_adr_text\">Добавить адрес</div>'+
            '</div>'+
            '</div>'+
            '</a>';
        $(\"#adr_md_\"+slot).append('Загрузка...');
        $.ajax({
            type: \"POST\",
            url: \"/save_my_data.php\",
            data: 'id_delete='+uid+'&slot='+slot,
            success: function(html) {
                $(\"#adr_md_\"+slot).empty();
                $(\"#adr_md_\"+slot).append(ht);
            }
        });
        check_slot(0);
        del_div_info();
        $('#mask, .window_ed').hide();
        $(this).hide();
        $('.window_ed').hide(); 
        return false;
    }
</script>
    
<script type=\"text/javascript\">
$(document).ready(function(){


function liFormat (row, i, num) {
	var result = row[0];
	return result;
}
function selectItem(li) {
var sValue = li.selectValue;        
document.getElementById('search_form').submit();
}

$(\"#city\").autocomplete(\"/live_search.php\", {
	delay:1,
	minChars:2,
        
	matchSubset:1,
	autoFill:false,
	matchContains:1,
	cacheLength:1,
	selectFirst:false,
	formatItem:liFormat,
	maxItemsToShow:15,
	onItemSelect:selectItem
}); 

});
</script>
");

//------------------------------------------------------------------------------
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
//------------------------------------------------------------------------------
if ((isset($_GET['print_id']) and $_GET['print_id']!=0)){
    $user_id = $_GET['print_id']; $slot =$_GET['slot_id'];
    $row = $db->fetchRow("SELECT * FROM `user_adr` WHERE (`user_id`='".$user_id."' AND `slot`='".$slot."')");
    $rowCity = $db->fetchRow("SELECT * FROM `info_delivery` WHERE (`city`='".$row['city']."')");
    if (isset($rowCity['city'])){
        if ($rowCity['country']=='ru'){
            $v = 'руб.';
        } else {
            $v = 'грн.';
        }
    echo toDB('<div class="text_adr_d">Информация о стоимости доставки. Доставка оплачивается при получении.</div>
   <table>
       <tr class="t_head_ic">
           <td>Город</td><td>от 1 до 199 шт.</td><td>от 200 до 399 шт.</td><td>от 400 до 599 шт.</td>
       </tr>
           <tr class="t_body_ic">
               <td>'.$rowCity['city'].'</td><td><b>'.$rowCity['f2'].' '.$v.'</b></td><td><b>'.$rowCity['f3'].' '.$v.'</b></td><td><b>'.$rowCity['f4'].' '.$v.'</b></td>
       </tr>
   </table>');
    }
    exit;
}
if ($_GET['method']=='new') {
echo toDB('


<form action="#" id="contactForm" method="post">
<h2 class="mw_h2">Добавить адрес</h2>    
<input type="hidden" value="'.$_GET['method'].'" id="method" name="method" >
<input type="hidden" value="'.$_GET['user_id'].'" id="user_id" name="user_id" >
<input type="hidden" value="'.$_GET['no'].'" id="slot_id" name="slot_id" >    

    
      <ul>
	 <li>
             <label for="contactName"><p class="mw_name">Название адреса:</p></label>
	      <input type="text" name="adr_name" id="adr_name"  value="Адрес '.$_GET['no'].'" onblur="if(this.value==\'\'){this.value=\'Адрес '.$_GET['no'].'\';} " onfocus="if(this.value==\'Адрес '.$_GET['no'].'\'){this.value=\'\';}" class="textinput requiredField" />
              <br clear="all">
	</li>
	
        <li>
	      <label for="contactName"><p class="mw_name">Индекс:</p></label>
	      <input type="text" name="index" id="index" value="" class="textinput requiredField" />
              <br clear="all">
	</li>
        <li>
	      <label for="contactName"><p class="mw_name">Город:</p></label>
	      <input type="text" name="city" id="city" value="" class="textinput requiredField" />
              <br clear="all">
	</li>
        <li>
	      <label for="contactName"><p class="mw_name">Адрес:</p></label>
	      <input type="text" name="adr" id="adr" value="" class="textinput requiredField" />
              <br clear="all">
	</li>
        <li>
	      <label for="contactName"><p class="mw_name">Телефон:</p></label>
	      <input type="text" name="phone" id="phone" value="" class="textinput requiredField" />
              <br clear="all">
	</li>
        <li>
	      <label for="contactName"><p class="mw_name">ФИО получателя:</p></label>
	      <input type="text" name="fio" id="fio" value="" class="textinput requiredField" />
              <br clear="all">
	</li>
        <li>
	      <label for="contactName"><p class="mw_name_ta1">Паспортные данные<br />
(серия, номер, кем<br/>
и когда выдан):</p></label>
	      <textarea name="passport_data" style="height: 60px;" id="passport_data" class="textinput requiredField"></textarea>
              <br clear="all">
	</li>
        <li>
	      <label for="contactName"><p class="mw_name_ta2">Дополнительная<br />
информация:</p></label>
	      <textarea name="add_info" style="height:100px;" id="add_info" class="textinput"></textarea>
              <br clear="all">
	</li>

	<li>
		<input type="hidden" name="submitted" id="submitted" value="true" />
		<button id="buttons" class="mw_submit" type="submit">Сохранить адрес</button>
	</li>
   </ul>
</form>
');
} else {
    $user_id = $_GET['user_id'];
    $slot = $_GET['no'];
    $row = $db->fetchRow("SELECT * FROM `user_adr` WHERE (`user_id`='".$user_id."' AND `slot`='".$slot."')");
    //echo "SELECT * FROM `user_adr` WHERE `user_id`='".$user_id."' AND `slot`='".$slot."'";
    //var_dump($row);
    
echo toDB('
<form action="#" id="contactForm" method="post">
<h2 class="mw_h2">Редактировать адрес</h2>    
<input type="hidden" value="'.$_GET['method'].'" id="method" name="method" >
<input type="hidden" value="'.$user_id.'" id="user_id" name="user_id" >
<input type="hidden" value="'.$slot.'" id="slot_id" name="slot_id" >    

      <ul>
	 <li>
             <label for="contactName"><p class="mw_name">Название адреса:</p></label>
	      <input type="text" name="adr_name" id="adr_name" value="'.$row['adr_name'].'" class="textinput requiredField" />
              <br clear="all">
	</li>
	
        <li>
	      <label for="contactName"><p class="mw_name">Индекс:</p></label>
	      <input type="text" name="index" id="index" value="'.$row['index'] .'" class="textinput requiredField" />
              <br clear="all">
	</li>
        <li>
	      <label for="contactName"><p class="mw_name">Город:</p></label>
	      <input type="text" name="city" id="city" value="'.$row['city'] .'" class="textinput requiredField" />
              <br clear="all">
	</li>
        <li>
	      <label for="contactName"><p class="mw_name">Адрес:</p></label>
	      <input type="text" name="adr" id="adr" value="'.$row['adr'] .'" class="textinput requiredField" />
              <br clear="all">
	</li>
        <li>
	      <label for="contactName"><p class="mw_name">Телефон:</p></label>
	      <input type="text" name="phone" id="phone" value="'.$row['phone'] .'" class="textinput requiredField" />
              <br clear="all">
	</li>
        <li>
	      <label for="contactName"><p class="mw_name">ФИО получателя:</p></label>
	      <input type="text" name="fio" id="fio" value="'.$row['fio'] .'" class="textinput requiredField" />
              <br clear="all">
	</li>
        <li>
	      <label for="contactName"><p class="mw_name_ta1">Паспортные данные<br />
(серия, номер, кем<br/>
и когда выдан):</p></label>
	      <textarea name="passport_data" style="height: 60px;" id="passport_data" class="textinput requiredField">'.  $row['passport_data'] .'</textarea>
              <br clear="all">
	</li>
        <li>
	      <label for="contactName"><p class="mw_name_ta2">Дополнительная<br />
информация:</p></label>
	      <textarea name="add_info" style="height:100px;" id="add_info" class="textinput">'.$row['add_info'] .'</textarea>
              <br clear="all">
	</li>

	<li>
		<input type="hidden" name="submitted" id="submitted" value="true" />
                <div class="delete_adr"><a onClick="delete_info('.$user_id.', '.$slot.'); return false;"><img src="/img/delete_adr.png" class="d_mw_i"></a>&nbsp;<a onClick="delete_info('.$user_id .', '. $slot .'); return false;">Удалить адрес</a></div> <button id="buttons" class="mw_submit" type="submit">Сохранить адрес</button>
	</li>
   </ul>
</form>');

}
function toDB($value){
    //$value = iconv('utf-8', 'windows-1251', $value);
    $value = iconv( 'windows-1251','utf-8', $value);
    return $value;
}