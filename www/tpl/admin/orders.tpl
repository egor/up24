<!-- BDP: country -->
<table class="country_tabl country_tabl_disc admin_country_tabl_disc">
    <tr>
        <td {CLASS_ACTIVE_RU} ><a {CLASS_ACTIVE_RU} href="/admin/orders?country=ru"><div class="t_l_b">������</div></a></td>
        <td {CLASS_ACTIVE_UA} ><a {CLASS_ACTIVE_UA} href="/admin/orders?country=ua"><div class="t_l_b">�������</div></a></td>
        <td class="select_ev country_tabl_disc_ev">
            
        </td>
    </tr>
    <tr><td colspan="3" class="country_line"></td></tr>
</table>

<!-- EDP: country -->
<!-- BDP: discs_order_list -->
<div class="buy_tbl">
    <table>
        <tr>
            <th>����</th>
            <th class="center">����� ������</th>
            <th class="center">�����</th>
            <th class="center">������</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <!-- BDP: discs_order_list_row -->
        <form action="/admin/vieworder/{ORDER_ID}" method="GET" target="_blank">
        <tr>
            <td><a href="/admin/vieworder/{ORDER_ID}" target="_blank">{ORDER_DATE}</a></td>
            <td class="center"><a href="/admin/vieworder/{ORDER_ID}" target="_blank">{ORDER_NUMBER}</a></td>
            <td class="center"><strong>{ORDER_SUMM} {MONEY3}</strong></td>
            <td class="center"><strong class="{ORDER_STATUS_CLASS}">{ORDER_STATUS}</strong></td>
            <td class="right">
                <a title="�������������" href="/admin/editorder/{ORDER_ID}">
                    <img width="12" height="12" alt="�������" src="/img/admin_icons/edit.png">
                </a>
            </td>
            <td class="right">
                <a onclick="return confirm('�� ������� ��� ������ �������?'); return false;" title="�������" href="/admin/deleteOrderDisc/{ORDER_ID}">
                    <img width="12" height="12" alt="�������" src="/img/admin_icons/delete.png">
                </a>
            </td>
            <td class="right"><input type="submit" value="������ ������" class="button" /></td>
        </tr>
        </form>
        <!-- EDP: discs_order_list_row -->
        
        <!-- BDP: discs_order_list_empty -->
        <tr>
            <td colspan="6" class="center"><i>������� �� �������</i></td>
        </tr>
        <!-- EDP: discs_order_list_empty -->
    </table>
</div>
<!-- EDP: discs_order_list -->