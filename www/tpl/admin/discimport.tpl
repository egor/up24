<!-- BDP: import_form -->
<form action="/admin/discimport" method="post" enctype="multipart/form-data">
	<input type="file" name="import" />
	<input type="submit" value="Импорт" class="button" name="import" />
</form>
<!-- EDP: import_form -->

{IMPORT_FALSE}

<!-- BDP: import_error -->
<table>
	<tr>
		<th class="center">№<br />строки</th>
		<th class="center">Ошибка</th>
		<th class="center">Номер заказа</th>
	</tr>
	<!-- BDP: import_error_row -->
	<tr>
		<td class="center">{FILE_LINE}</td>
		<td>{IMPORT_MESSAGE}</td>
		<td>{IMPORT_NUMBER}</td>
	</tr>
	<!-- EDP: import_error_row -->
</table>
<!-- EDP: import_error -->