<link rel="stylesheet" type="text/css" href="/css/calendar.css" title="winter" />
<script type="text/javascript" src="/js/calendar.js"></script>
<script type="text/javascript" src="/js/calendar-ru.js"></script>
<script type="text/javascript" src="/js/calendar-setup.js"></script>

<div class="export_block">

<form method="post" action="/admin/export/">
	<p>Выберите период с 
	<input type="text" class="small" id="date_start" name="date-start" value="{DATE_START}" /> 
	<a href="javascript://" onclick="return false;"><img src="/img/calendar.gif" width="16" height="15" alt="" id="trigger1" /></a> по 
	<input type="text" class="small" id="date_end" name="date-end" value="{DATE_END}" /> 
	<a href="javascript://" onclick="return false;"><img src="/img/calendar.gif" width="16" height="15" alt="" id="trigger2" /></a>
	</p>
	<p><input type="checkbox" class="check" name="new"{CHECK_NEW} /><strong>Новые (2)</strong></p>
	<p><input type="checkbox" class="check" name="confirmed"{CHECK_CONFIRMED} /><strong>Подтвержденные (1)</strong></p>
	<p><input type="checkbox" class="check" name="banned"{CHECK_BANNED} /><strong>Забаненные (0)</strong></p>
	<p><input type="submit" value="Экспортировать в XLS" class="button button_lng" name="export" /></p>
</form>

<script type="text/javascript">
	Calendar.setup({
		inputField     :    "date_start",
		ifFormat       :    "%d.%m.%y",
		button         :    "trigger1",
		align          :    "T1",
		singleClick    :    true
	});
</script>

<script type="text/javascript">
	Calendar.setup({
		inputField     :    "date_end",
		ifFormat       :    "%d.%m.%y",
		button         :    "trigger2",
		align          :    "T1",
		singleClick    :    true
	});
</script>

</div>

{EXPORT_FAIL}