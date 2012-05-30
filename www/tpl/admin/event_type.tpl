<form method="post" action="/admin/eventtype/">

<table>
    <tr>
        <th class="center" colspan="3">Типы семинаров</th>
    </tr>
    <!-- BDP: event_type_row -->
    <tr>
        <td class="width"><span>*</span> {TYPE_NAME} ({MONEY_NAME})</td>
        <td><input type="text" name="{TYPE_ID}" value="{TYPE_COST}" class="small" /></td>
        <td class="width"> {COUNTRY_NAME} </td>
    </tr>
    <!-- EDP: event_type_row -->
    <tr>
        <td colspan="3"><strong>Поля, отмеченные <span>*</span>, обязательны для заполнения</strong></td>
    </tr>
    <tr>
        <td colspan="3"><input type="submit" value="Применить" class="button" name="edit_settings" /></td>
    </tr>
</table>

</form>