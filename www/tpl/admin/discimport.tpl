<!-- BDP: import_form -->
<form action="/admin/discimport" method="post" enctype="multipart/form-data">
	<input type="file" name="import" />
	<input type="submit" value="������" class="button" name="import" />
</form>
<!-- EDP: import_form -->

{IMPORT_FALSE}

<!-- BDP: import_error -->
<table>
	<tr>
		<th class="center">�<br />������</th>
		<th class="center">������</th>
		<th class="center">����� ������</th>
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