<!-- BDP: list -->
<p>������������ ������ ����� ������� ����� 2-� ������� �����</p>
<div class="buy_list">
    
	<ul>
		<!-- BDP: list_row -->
		<li>
			<span>{TICKET_NUMBER}&nbsp;&nbsp;&nbsp;<a href="/viewevent/{EVENT_ID}">{EVENT_NAME}</a></span>
			{EVENT_PIC}
			<div class="desc">
				<p>����:<strong>{EVENT_DATE}{EVENT_DATE2}</strong></p>
				<p>�����:<strong>{EVENT_CITY}</strong></p>
				<p style="height: 35px;">�����:<strong>{EVENT_ADRES}</strong></p>
				<p>����� ������:<strong>{EVENT_TIME}</strong></p>
				<p>��� �����������:<strong>{EVENT_TYPE}</strong></p>
			</div>
			<div class="ticket">
				{EVENT_TICKETS}
				<!-- BDP: list_row_status_on -->
				<p>������: <strong class="gr">��������</strong></p>
				<p>&nbsp;</p>
				<!-- EDP: list_row_status_on -->
				<!-- BDP: list_row_status_off -->
				<p>������: <strong class="red">������</strong></p>
      			<p><strong class="red">�� ������������</strong></p>
      			<!--<form action="/ticketpay" method="post" target="_blank">
				<input type="hidden" name="ticketId" value="{TICKET_ID}">
				<p><input type="submit" value="�������� ������" class="button" /></p>
				</form>-->
				<form action="/getplatezhticket" method="post" target="_blank">
				<input type="hidden" name="ticketId" value="{TICKET_ID}">
				<p><input type="submit" value="������ ����� ����" class="button" /></p>
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
				<p><input type="submit" value="���������� ������" class="button_print" /></p>
				</form>
                                
				
			</div>
			<div class="clear"></div>
		</li>
		<!-- EDP: list_row -->
	</ul>
</div>
<!-- EDP: list -->