<!-- BDP: discs_order_list -->
<div class="buy_tbl">
    <table>
        <tr>
            <th>Дата</th>
            <th class="center">Номер заказа</th>
            <th class="center">Сумма</th>
            <th class="center">Статус</th>
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
                    <input type="submit" value="Печать заказа" class="button bpl" />
                </form>
                <!--<br />
                <form action="/getplatezhdisk" method="post" target="_blank">
                    <input type="hidden" name="diskId" value="{ORDER_ID}">
                    <input type="submit" value="Оплата через банк" class="button bpl" />
                </form>-->
            </td>
        </tr>
        
        <!-- EDP: discs_order_list_row -->
        
        <!-- BDP: discs_order_list_empty -->
        <tr>
            <td colspan="5" class="center"><i>Покупок не найдено</i></td>
        </tr>
        <!-- EDP: discs_order_list_empty -->
    </table>
</div>
<!-- EDP: discs_order_list -->