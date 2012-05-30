<!-- BDP: country -->
<table class="country_tabl">
    <tr>
        <td {CLASS_ACTIVE_RU} ><a {CLASS_ACTIVE_RU} href="/events?country=ru"><div class="t_l_b">Россия</div></a></td>
        <td {CLASS_ACTIVE_UA} ><a {CLASS_ACTIVE_UA} href="/events?country=ua"><div class="t_l_b">Украина</div></a></td>
        <td class="select_ev">
            <form action="" method="get">
                Выберите тип семинара: <select name="filter_type" onchange="this.form.submit();">{FILTER_TYPE_OPTIONS}</select>
            </form>
        </td>
    </tr>
    <tr><td colspan="3" class="country_line"></td></tr>
</table>
<!-- EDP: country -->
<!-- BDP: filter -->
<link rel="stylesheet" type="text/css" media="all" href="/css/calendar-blue.css" title="winter" />
<script type="text/javascript" src="/js/calendar.js"></script>
<script type="text/javascript" src="/js/calendar-ru.js"></script>
<script type="text/javascript" src="/js/calendar-setup.js"></script>

<!--<div class="type_block tops_new">
    <form action="" method="get">
    <p>
        Тип: <select name="filter_type" onchange="this.form.submit();">{FILTER_TYPE_OPTIONS}</select>

        <input type="submit" class="new_b" value="Найти">
    </p>
    </form>
</div>-->


<!-- EDP: filter -->

<!-- BDP: list -->

<div class="catalog_list">
    <ul>
    	<!-- BDP: list_row -->
        <li>
            <span><a href="/viewevent/{EVENT_ID}">{EVENT_NAME}</a></span>
            <a href="/viewevent/{EVENT_ID}">{EVENT_PIC}</a>
            
            <div class="desc">
                <p>Дата:<strong>{EVENT_DATE}{EVENT_DATE2}</strong></p>
                <p>Город:<strong>{EVENT_CITY}</strong></p>
                <p>Адрес:<strong>{EVENT_ADRES}</strong></p>
                {EVENT_TIME}
                <!-- <p>Вид мероприятия:<strong>{EVENT_TYPE}</strong></p> -->
                <strong><a href="/viewevent/{EVENT_ID}">Подробнее »</a></strong>
            </div>
            
            <div class="clear"></div>
            
            {EVENT_PREVIEW}<br />
            
            <div class="clear"></div>
        </li>
        <!-- EDP: list_row -->
    </ul>
</div>
<!-- EDP: list -->

<!-- BDP: detail -->
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>


<div class="catalog_list catalog_info">
    <ul class="noborder">
        <li>
            {EVENT_PIC}
            <div class="desc">
                <p>Дата:<strong>{EVENT_DATE}{EVENT_DATE2}</strong></p>
                <p>Город:<strong>{EVENT_CITY}</strong></p>
                <p>Адрес:<strong>{EVENT_ADRES}</strong></p>
               {EVENT_TIME}
                <!-- <p>Вид мероприятия:<strong>{EVENT_TYPE}</strong></p> -->
                <p><a href="/partners/viewevent/{EVENT_ID}" class="mypart_e">Мои партнеры на этом семинаре</a></p>
            </div>
            <div class="clear"></div>
        </li>
    </ul>

    {EVENT_PREVIEW}

    <h3>Подробное описание семинара</h3>

    {EVENT_BODY}
   
    <h3>{HALL_PIC_H3}</h3>

    {HALL_PIC}

    {HALL_PREVIEW}
   
    
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>  
<script type="text/javascript" > 
$(function() {  
    $(".view-code").click(function(evt) { 
        var d = $(this).attr("toolDiv"); 
        $(".tool_block[toolDiv=" + d + "], .view-code[toolDiv=" + d + "]").toggle(); 
    });  
}); 
</script> 

<div {HIDE_CLASS}>


<div style="display: none;">
<a href="#" class="view-code" toolDiv="1" >Показать доступные места</a> 
<a href="#" class="view-code" toolDiv="1" style="display:none">Показать все</a> <br  /> 
</div>
<br clear="all" />

<div>
        {BTT}
    <script type="text/javascript" src="/js/jsForEvent/jquery.js"></script>
<script type="text/javascript" src="/js/jsForEvent/jquery.cookie.js"></script>
<script type="text/javascript">
var ticketCount = 0;
$(document).ready(function() {
	$('ul#my-menu ul').each(function(i) { // Check each submenu:
		if ($.cookie('submenuMark-' + i)) {  // If index of submenu is marked in cookies:
			$(this).show().prev().removeClass('collapsed').addClass('expanded'); // Show it (add apropriate classes)
		}else {
			$(this).hide().prev().removeClass('expanded').addClass('collapsed'); // Hide it
		}
		$(this).prev().addClass('collapsible').click(function() { // Attach an event listener
			var this_i = $('ul#my-menu ul').index($(this).next()); // The index of the submenu of the clicked link
			if ($(this).next().css('display') == 'none') {
				$(this).next().slideDown(200, function () { // Show submenu:
					$(this).prev().removeClass('collapsed').addClass('expanded');
					cookieSet(this_i);
				});
			}else {
				$(this).next().slideUp(200, function () { // Hide submenu:
					$(this).prev().removeClass('expanded').addClass('collapsed');
					cookieDel(this_i);
					$(this).find('ul').each(function() {
						$(this).hide(0, cookieDel($('ul#my-menu ul').index($(this)))).prev().removeClass('expanded').addClass('collapsed');
					});
				});
			}
		return false; // Prohibit the browser to follow the link address
		});
	});
});
function cookieSet(index) {
	$.cookie('submenuMark-' + index, 'opened', {expires: null, path: '/'}); // Set mark to cookie (submenu is shown):
}
function cookieDel(index) {
	$.cookie('submenuMark-' + index, null, {expires: null, path: '/'}); // Delete mark from cookie (submenu is hidden):
}
function reserveTickets(sector, row, loc, j, event, id_sector) {
    //var color = $('#loc'+j).css("background-color");
    
    //alert ($('#loc'+j).attr("class"));
    
    if ($('#loc'+j).attr("class")=='free') {
        //alert ('Вы выбрали '+sector+', ряд '+row+', место '+loc+'.\n Нажмите еще раз для отмены.');
        $('#loc'+j).attr("class","buy");
        //$('#loc'+j).css('background-color', '#FFA500');
        ticketCount++;
        $.ajax({
            type: "POST",
            url: "/buyTickets.php",
            data: "sector="+sector+"&row="+row+"&loc="+loc+"&event="+event+"&id_sector="+id_sector,
            success: function(msg){
            $('#cost_tickets_a').html(msg);
            //alert( "Данные успешно сохранены: " + msg );
            }
        });
        
        
    } else {
        //$('#loc'+j).css('background-color', '#FFFFFF');
        ticketCount--;
        $('#loc'+j).attr("class","free");
        $.ajax({
            type: "POST",
            url: "/buyTickets.php",
            data: "sector="+sector+"&row="+row+"&loc="+loc+"&event="+event+"&id_sector="+id_sector+"&cancel=1",
            success: function(msg){
            $('#cost_tickets_a').html(msg);
            //alert( "Данные успешно сохранены: " + msg );
            }
        });

    }
    return false;
}
function checkTickets(f){
if (ticketCount==0){ alert('Выберите место в зале и нажмите на кнопку \"Купить\"'); return false;} else { f.submit();}}
</script>

      <ul id="my-menu" class="sample-menu">

           <!-- BDP: print_sectors -->
    {SECTOR_LOCATION}
    
    
    <!-- EDP: print_sectors -->
    
    
      </ul>    
    <div id="cost_tickets_a">&nbsp;</div>
<form method="POST" action="/buytickets/">
<input class="button" name="BuyTickets" type="submit" onclick="checkTickets(this); return false;" style="float: right;" value="Купить">
</form>
</div>
</div>
</div>
<!-- EDP: detail -->