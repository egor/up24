<!-- BDP: list -->
<div class="buy_list">
	<ul>
		<!-- BDP: list_row -->
		<li>
			<span>{TICKET_NUMBER}&nbsp;&nbsp;&nbsp;<a href="/viewevent/{EVENT_ID}">{EVENT_NAME}</a> <span>Заказ создан {TICKET_DATE}</span></span>
			<a href="/viewevent/{EVENT_ID}">{EVENT_PIC}</a>
			<div class="desc">
				<p>Дата:<strong>{EVENT_DATE}{EVENT_DATE2}</strong></p>
				<p>Город:<strong>{EVENT_CITY}</strong></p>
				<p>Адрес:<strong>{EVENT_ADRES}</strong></p>
				<p>Время начала:<strong>{EVENT_TIME}</strong></p>
				<p>Вид мероприятия:<strong>{EVENT_TYPE}</strong></p>
				<p>Номер Amway:<strong>{USER_NUMBER}</strong></p>
			</div>
			<div class="ticket">
				<p>Куплено билетов: <strong>{EVENT_TICKETS}</strong></p>
				<p>Статус: <strong class="red">оплата</strong></p>
      			<p><strong class="red">не подтверждена</strong></p>
      			<form action="/admin/ticketdelete/{TICKET_ID}" method="post">
				<p><input type="submit" value="Удалить заказ" class="button" /></p>
				</form>
				<form action="/admin/ticketprint/" method="post" target="_blank">
				<input type="hidden" name="ticketId" value="{TICKET_ID}">
				<p><input value="Напечатать билеты" class="button_print" type="submit"></p>
				</form>
			</div>
			<div class="clear"></div>
		</li>
		<!-- EDP: list_row -->
	</ul>
</div>
<!-- EDP: list -->