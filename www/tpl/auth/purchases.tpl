<!-- BDP: list -->
<p>Неоплаченные заказы будут удалены через 2-е рабочих суток</p>
<div class="buy_list">
    
	<ul>
		<!-- BDP: list_row -->
		<li>
			<span>{TICKET_NUMBER}&nbsp;&nbsp;&nbsp;<a href="/viewevent/{EVENT_ID}">{EVENT_NAME}</a></span>
			{EVENT_PIC}
			<div class="desc">
				<p>Дата:<strong>{EVENT_DATE}{EVENT_DATE2}</strong></p>
				<p>Город:<strong>{EVENT_CITY}</strong></p>
				<p style="height: 35px;">Адрес:<strong>{EVENT_ADRES}</strong></p>
				<p>Время начала:<strong>{EVENT_TIME}</strong></p>
				<p>Вид мероприятия:<strong>{EVENT_TYPE}</strong></p>
			</div>
			<div class="ticket">
				{EVENT_TICKETS}
				<!-- BDP: list_row_status_on -->
				<p>Статус: <strong class="gr">оплачено</strong></p>
				<p>&nbsp;</p>
				<!-- EDP: list_row_status_on -->
				<!-- BDP: list_row_status_off -->
				<p>Статус: <strong class="red">оплата</strong></p>
      			<p><strong class="red">не подтверждена</strong></p>
      			<!--<form action="/ticketpay" method="post" target="_blank">
				<input type="hidden" name="ticketId" value="{TICKET_ID}">
				<p><input type="submit" value="Оплатить картой" class="button" /></p>
				</form>-->
				<form action="/getplatezhticket" method="post" target="_blank">
				<input type="hidden" name="ticketId" value="{TICKET_ID}">
				<p><input type="submit" value="Оплата через банк" class="button" /></p>
				</form>
				<!-- EDP: list_row_status_off -->
				<!--
				<form action="{ACTION_SECURITY}" method="post" target="_blank">
				<input type="hidden" name="ticketId" value="{TICKET_ID}">
                                {BUTTON_PRINT}
				
				</form>
                                -->
                                <form action="/ticketprint" method="post" target="_blank">
				<input type="hidden" name="ticketId" value="{TICKET_ID}">
				<p><input type="submit" value="Напечатать билеты" class="button_print" /></p>
				</form>
                                
				
			</div>
			<div class="clear"></div>
		</li>
		<!-- EDP: list_row -->
	</ul>
</div>
<!-- EDP: list -->