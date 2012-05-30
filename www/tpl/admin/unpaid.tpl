<!-- BDP: list -->
<div class="buy_list">
	<ul>
		<!-- BDP: list_row -->
		<li>
			<span>{TICKET_NUMBER}&nbsp;&nbsp;&nbsp;<a href="/viewevent/{EVENT_ID}">{EVENT_NAME}</a> <span>����� ������ {TICKET_DATE}</span></span>
			<a href="/viewevent/{EVENT_ID}">{EVENT_PIC}</a>
			<div class="desc">
				<p>����:<strong>{EVENT_DATE}{EVENT_DATE2}</strong></p>
				<p>�����:<strong>{EVENT_CITY}</strong></p>
				<p>�����:<strong>{EVENT_ADRES}</strong></p>
				<p>����� ������:<strong>{EVENT_TIME}</strong></p>
				<p>��� �����������:<strong>{EVENT_TYPE}</strong></p>
				<p>����� Amway:<strong>{USER_NUMBER}</strong></p>
			</div>
			<div class="ticket">
				<p>������� �������: <strong>{EVENT_TICKETS}</strong></p>
				<p>������: <strong class="red">������</strong></p>
      			<p><strong class="red">�� ������������</strong></p>
      			<form action="/admin/ticketdelete/{TICKET_ID}" method="post">
				<p><input type="submit" value="������� �����" class="button" /></p>
				</form>
				<form action="/admin/ticketprint/" method="post" target="_blank">
				<input type="hidden" name="ticketId" value="{TICKET_ID}">
				<p><input value="���������� ������" class="button_print" type="submit"></p>
				</form>
			</div>
			<div class="clear"></div>
		</li>
		<!-- EDP: list_row -->
	</ul>
</div>
<!-- EDP: list -->