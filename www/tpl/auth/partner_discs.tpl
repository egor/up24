<!-- BDP: discs_order_list -->
<div class="buy_tbl">
    <table>
        <tr>
            <th>����</th>
            <th class="center">����� ������</th>
            <th class="center">�����</th>
            <th class="center">������</th>
            <th></th>
        </tr>
        <!-- BDP: discs_order_list_row -->
        
        <tr>
            <td><a href="/partners/viewdisk/{ORDER_ID}" target="_blank">{ORDER_DATE}</a></td>
            <td class="center"><a href="/partners/viewdisk/{ORDER_ID}" target="_blank">{ORDER_NUMBER}</a></td>
            <td class="center"><strong>{ORDER_SUMM} {MONEY3}</strong></td>
            <td class="center"><strong class="{ORDER_STATUS_CLASS}">{ORDER_STATUS}</strong></td>
            <td class="right">
                <form action="/partners/viewdisk/{ORDER_ID}" method="GET" target="_blank">
                    <input type="submit" value="������ ������" class="button bpl" />
                </form>
                <!--<br />
                <form action="/getplatezhdisk" method="post" target="_blank">
                    <input type="hidden" name="diskId" value="{ORDER_ID}">
                    <input type="submit" value="������ ����� ����" class="button bpl" />
                </form>-->
            </td>
        </tr>
        
        <!-- EDP: discs_order_list_row -->
        
        <!-- BDP: discs_order_list_empty -->
        <tr>
            <td colspan="5" class="center"><i>������� �� �������</i></td>
        </tr>
        <!-- EDP: discs_order_list_empty -->
    </table>
</div>
<!-- EDP: discs_order_list -->