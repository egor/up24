<!-- BDP: discs_list -->
<!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>-->

<script>
    var items = new Array();


function selectRegion(){
    
        var count = 0;
    
    for (var i=0; i<items.length; i++) {
        count += parseInt(items[i][1]);
    }
    
        
        var id_country = $('select[name="country"]').val();
        if(!id_country){
                $('div[name="selectDataRegion"]').html('');
                $('div[name="selectDataCity"]').html('');
        }else{
                $.ajax({
                        type: "POST",
                        url: "/ajax.base.php",
                        data: { action: 'showRegionForInsert', id_country: id_country, count: count },
                        cache: false,
                        success: function(responce){ $('div[name="selectDataRegion"]').html(responce); }
                });
        };
};

function selectCity(){
        var id_region = $('select[name="region"]').val();
        $.ajax({
                type: "POST",
                url: "/ajax.base.php",
                data: { action: 'showCityForInsert', id_region: id_region },
                cache: false,
                success: function(responce){ $('div[name="selectDataCity"]').html(responce); }
        });
};


</script>


<table class="country_tabl country_tabl_disc">
    <tr>
        <td {CLASS_ACTIVE_RU} ><a {CLASS_ACTIVE_RU} href="/disc?country=ru"><div class="t_l_b">Россия</div></a></td>
        <td {CLASS_ACTIVE_UA} ><a {CLASS_ACTIVE_UA} href="/disc?country=ua"><div class="t_l_b">Украина</div></a></td>
        <td class="select_ev country_tabl_disc_ev">
            <div class="container_di{ACTIVE_ALL}"><a href="/disc?show=all">Подробно</a></div>
            <div class="container_dis{ACTIVE_DESC}"><a href="/disc?show=desc">Кратко</a></div>
        </td>
    </tr>
    <tr><td colspan="3" class="country_line"></td></tr>
</table>


<!--<div style="overflow: hidden; margin-bottom: 30px;">Ваша страна: <a {CLASS_ACTIVE_RU} href="/disc?country=ru">Россия</a>&nbsp;&nbsp;&nbsp;<a {CLASS_ACTIVE_UA} href="/disc?country=ua">Украина</a></div>-->


 <div class="clear"></div>
<form action="/disc" method="post" name="disc_form" id="disc_form">
<div class="disc_list">
   <ul>
    <!-- BDP: discs_list_row -->
    <input type="hidden" name="disc[{DISC_ID}][cost]" value="{DISC_COST}" />
    <li>
     <a href="/viewdisc/{DISC_ID}">{DISC_PIC}</a>
     <div class="desc">
      <p><a href="/viewdisc/{DISC_ID}" class="short_link">{DISC_NAME}</a> <p class="art">Артикул: {DISC_ARTICUL}</p>{DISC_PREVIEW}<strong><a href="/viewdisc/{DISC_ID}">Подробнее »</a></strong></p>
     </div>
     <div class="busket">
     
      <p>Цена:<strong>{DISC_COST} {MONEY3}</strong></p>
      <p>Количество:<input type="text" name="disc[{DISC_ID}][count]" value="{DISC_COUNT}" id="count_{DISC_ID}" onChange="getItemSumm({DISC_ID}, {DISC_COST}, 'yes', '{MONEY3}');" value="0" onblur="if(this.value==''){this.value='0';} " onfocus="if(this.value=='0'){this.value='';}"/></p>
      <p>Сумма:<strong class="bl" id="summ_{DISC_ID}"><script>getItemSumm({DISC_ID}, {DISC_COST}, 'no', '{MONEY3}');</script></strong></p>
     </div>
     <div class="clear"></div>
    </li>
    <!-- EDP: discs_list_row -->
    <input type="hidden" id="c_slots" name="delivery[c_slots]" value="0"/>   
   </ul>
    <br>
    <div class="info_by_disc">Итого заказано дисков <b id="total_count"><script>getTotalCount();</script></b> <b>шт.</b> на сумму <span id="total_summ"><script>getTotalSumm();</script> </span> <span>{MONEY3}.</span></div>
   <!--<p class="total"><b class="otst">Общее количество:</b><span id="total_count"><script>getTotalCount();</script></span> <strong>шт.</strong></p>
   <p class="total"><b class="otst">Общая сумма заказа:</b><span id="total_summ"><script>getTotalSumm();</script></span> <strong>{MONEY3}.</strong></p>
   <p class="total"><b class="otst">Общий вес:</b><span id="total_weight"><script>getTotalWeight();</script></span> <strong>кг.</strong></p>-->
   <div class="text_adr_d">Выберите адрес, по которому будет осуществлена доставка или добавьте новый.</div>
    {GLOBAL_FORM_ID}
    {GLOBAL_USER_ID}
<table class="ms_tbl">
    <tr>
        <td>{ADR_1}</td>
        <td>{ADR_2}</td>
        <td>{ADR_3}</td>
    </tr>
</table>
    <div class="info_city" id="info_city">  
   
   </div>
    {DOSTAVKA_I}
    <table>
        <tr>
            <td><div class="dost_di">{DOSTAVKA_O}</div></td>
            <td><input type="button" value="Оплата картой" class="button" style="width:150px;" name="p_card" onclick="testCount('card');" /> </td>
            <td><input type="button" name="p_bank" value="Оплата через банк" style="width:150px;" class="button" onclick="testCount('bank');" /></td>
        </tr>
    </table>

  
 


   
   
{PRINT_REG}
<input type="hidden" id="buy_type" name="buy_type" value=""/>

   <!--<div class="count">Количество: <strong>4</strong> Регион: <select><option>&nbsp;</option></select>Примерная стоимость доставки:<span>26 грн</span><input type="submit" value="Пересчитать" class="button" /></div>-->
  </div>
  </form>
<!-- EDP: discs_list -->


<!-- BDP: discs_list_short -->
<!--<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>-->

<script>
    var items = new Array();


function selectRegion(){
    
        var count = 0;
    
    for (var i=0; i<items.length; i++) {
        count += parseInt(items[i][1]);
    }
    
        
        var id_country = $('select[name="country"]').val();
        if(!id_country){
                $('div[name="selectDataRegion"]').html('');
                $('div[name="selectDataCity"]').html('');
        }else{
                $.ajax({
                        type: "POST",
                        url: "/ajax.base.php",
                        data: { action: 'showRegionForInsert', id_country: id_country, count: count },
                        cache: false,
                        success: function(responce){ $('div[name="selectDataRegion"]').html(responce); }
                });
        };
};

function selectCity(){
        var id_region = $('select[name="region"]').val();
        $.ajax({
                type: "POST",
                url: "/ajax.base.php",
                data: { action: 'showCityForInsert', id_region: id_region },
                cache: false,
                success: function(responce){ $('div[name="selectDataCity"]').html(responce); }
        });
};


</script>
    <div class="short_none">
        <img src="/img/disc_icon/p_hover.gif">
        <img src="/img/disc_icon/p.gif">
        <img src="/img/disc_icon/k.gif">
        <img src="/img/disc_icon/k_hover.gif">
    </div>
<table class="country_tabl country_tabl_disc">
    <tr>
        <td {CLASS_ACTIVE_RU} ><a {CLASS_ACTIVE_RU} href="/disc?country=ru"><div class="t_l_b">Россия</div></a></td>
        <td {CLASS_ACTIVE_UA} ><a {CLASS_ACTIVE_UA} href="/disc?country=ua"><div class="t_l_b">Украина</div></a></td>
        <td class="select_ev country_tabl_disc_ev">
            <div class="container_di{ACTIVE_ALL}"><a href="/disc?show=all">Подробно</a></div>
            <div class="container_dis{ACTIVE_DESC}"><a href="/disc?show=desc">Кратко</a></div>
        </td>
    </tr>
    <tr><td colspan="3" class="country_line"></td></tr>
</table>
        <br clear="all">
<div class="short_kol">Количество:</div>


<div class="clear"></div>
<form action="/disc" method="post" name="disc_form" id="disc_form">

<div class="disc_list">
    <ul style="padding: 0;">
    <!-- BDP: discs_list_row_short -->
    <input type="hidden" name="disc[{DISC_ID}][cost]" value="{DISC_COST}" />
    <li class="short">
     <a href="/viewdisc/{DISC_ID}"></a>
     
     <p class="p_short" style=""><strong style="color:#3b5998; overflow: hidden;"><b style="width:90px; float: left; margin: 0; padding: 0;">{DISC_ARTICUL}</b></strong><span style="font: italic bold 15px Georgia;">&nbsp;&nbsp;&nbsp;&nbsp;<a href="/viewdisc/{DISC_ID}" >{DISC_NAME}</span>
     
     
     </p> 
      <input type="text" style="width: 40px; text-align: center; float: right; margin-top: 4px; margin-bottom: 4px; margin-right: 15px;" name="disc[{DISC_ID}][count]" value="{DISC_COUNT}" id="count_{DISC_ID}" onChange="getItemSumm({DISC_ID}, {DISC_COST}, 'yes');" value="0" onblur="if(this.value==''){this.value='0';} " onfocus="if(this.value=='0'){this.value='';}" />
      <div class="short_none">
      <p>Цена:<strong>{DISC_COST} {MONEY3}</strong></p>
      <p>Сумма:<strong class="bl" id="summ_{DISC_ID}"><script>getItemSumm({DISC_ID}, {DISC_COST}, 'no');</script></strong></p>
      </div>
      <div class="clear"></div>
    </li>
    <!-- EDP: discs_list_row_short -->
    
    
    
    
 <input type="hidden" id="c_slots" name="delivery[c_slots]" value="0"/>   
   </ul>
    <br>
    <div class="info_by_disc">Итого заказано дисков <b id="total_count"><script>getTotalCount();</script></b> <b>шт.</b> на сумму <span id="total_summ"><script>getTotalSumm();</script> </span> <span>{MONEY3}.</span></div>
   <!--<p class="total"><b class="otst">Общее количество:</b><span id="total_count"><script>getTotalCount();</script></span> <strong>шт.</strong></p>
   <p class="total"><b class="otst">Общая сумма заказа:</b><span id="total_summ"><script>getTotalSumm();</script></span> <strong>{MONEY3}.</strong></p>
   <p class="total"><b class="otst">Общий вес:</b><span id="total_weight"><script>getTotalWeight();</script></span> <strong>кг.</strong></p>-->
     <div class="text_adr_d">Выберите адрес, по которому будет осуществлена доставка или добавьте новый.</div>
   
    {GLOBAL_FORM_ID}
    {GLOBAL_USER_ID}
<table class="ms_tbl">
    <tr>
        <td>{ADR_1}</td>
        <td>{ADR_2}</td>
        <td>{ADR_3}</td>
    </tr>
</table>
    <div class="info_city" id="info_city">  
   
   </div>
   {DOSTAVKA_I}
    <table>
        <tr>
            <td><div class="dost_di">{DOSTAVKA_O}</div></td>
            <td><input type="button" value="Оплата картой" class="button" style="width:150px;" name="p_card" onclick="testCount('card');" /> </td>
            <td><input type="button" name="p_bank" value="Оплата через банк" style="width:150px;" class="button" onclick="testCount('bank');" /></td>
        </tr>
    </table>

  
 


   
   
{PRINT_REG}
<input type="hidden" id="buy_type" name="buy_type" value=""/>


  </div>
  </form>
<!-- EDP: discs_list_short -->


<!-- BDP: discs_detail -->
<div class="info_block">
   {DISC_PIC}
   <p><strong>Код: <span>{DISC_ARTICUL}</span></strong></p>
   <p>{DISC_PREVIEW}</p>
   <p class="price">Цена: <strong><span>{DISC_COST}</span> {MONEY3}</strong> <!--<span class="right">Куплено дисков: <a href="#">{DISC_BUY_COUNT}</a></span></p>-->
   <div class="clear"></div>
   <h3>Подробная информация о диске</h3>
   {DISC_BODY}
   <form action="/disc" method="get">
   <p class="center"><input type="submit" value="Вернуться в каталог дисков" class="button"></p>
   </form>
  </div>


<!-- EDP: discs_detail -->

<!-- BDP: discs_list_empty -->
Дисков не найдено
<!-- EDP: discs_list_empty -->

<!-- BDP: discs_order_list -->
<div class="buy_tbl">
    <table>
        <tr>
            <th>Дата</th>
            <th class="center">Номер заказа</th>
            <th class="center">Сумма</th>
            <th class="center">Статус</th>
            <th></th>
        </tr>
        <!-- BDP: discs_order_list_row -->
        
        <tr>
            <td><a href="/vieworder/{ORDER_ID}" target="_blank">{ORDER_DATE}</a></td>
            <td class="center"><a href="/vieworder/{ORDER_ID}" target="_blank">{ORDER_NUMBER}</a></td>
            <td class="center"><strong>{ORDER_SUMM} {MONEY_COUNTRY}</strong></td>
            <td class="center"><strong class="{ORDER_STATUS_CLASS}">{ORDER_STATUS}</strong></td>
            <td class="right">
                
                <!--<form action="/vieworder/{ORDER_ID}" method="GET" target="_blank">
                    <input type="submit" value="Печать заказа" class="button bpl" />
                </form><br />-->
                    <div {CLASS_BY_H}>
                
                
                <form action="/getplatezhdisk" method="post" target="_blank">
                    <input type="hidden" name="diskId" value="{ORDER_ID}">
                    <input type="submit" value="Оплата через банк" class="button bpl" />
                </form>
                
                <br />
                
                <form action="https://www.liqpay.com/?do=clickNbuy" method="POST" name="buy" target="_blank" />
                    <input type="hidden" name="operation_xml" value="{ORDER_CARD_XML}" />
                    <input type="hidden" name="signature" value="{ORDER_CARD_SIGNATURE}" />
                    <input type="submit" value="Оплата картой" class="button bpl" />
		</form>
                    <br />
		</div>	
                <div class="p_o_i_l"><a href="/vieworder/{ORDER_ID}" target="_blank" class="p_o_i_l_i"><img src="/img/print_order_ico.gif" class="p_o_i"></a><a href="/vieworder/{ORDER_ID}" target="_blank" class="p_o_i_l_t">Распечатать заказ</a></div>
            </td>
        </tr>
        
        <!-- EDP: discs_order_list_row -->
        
        <!-- BDP: discs_order_list_empty -->
        <tr>
            <td colspan="5" class="center"><i>Покупок не найдено</i></td>
        </tr>
        <!-- EDP: discs_order_list_empty -->
    </table>
</div>
<!-- EDP: discs_order_list -->