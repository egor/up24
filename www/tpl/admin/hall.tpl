<!-- BDP: country -->
<br clear="all">
<table class="country_tabl">
    <tr>
        <td {CLASS_ACTIVE_RU} ><a {CLASS_ACTIVE_RU} href="/admin/hall?country=ru"><div class="t_l_b">������</div></a></td>
        <td {CLASS_ACTIVE_UA} ><a {CLASS_ACTIVE_UA} href="/admin/hall?country=ua"><div class="t_l_b">�������</div></a></td>
        <td class="select_ev">
            
        </td>
    </tr>
    <tr><td colspan="3" class="country_line"></td></tr>
</table>
<!-- EDP: country -->
<!-- BDP: list -->

<h2>{GROUP_CITY}</h2>
<div class="rzd_list">
    <ul>
        <!-- BDP: list_row -->
        <li><span>{HALL_ADM}<a href="/admin/viewhall/{HALL_ID}">{HALL_NAME}</a></span><div class="clear"></div></li>
        <!-- EDP: list_row -->
    </ul>
</div>
<!-- EDP: list -->

<!-- BDP: detail -->
<div class="catalog_list catalog_info">
    <ul class="noborder">
        <li>
            <div class="desc">
                <p>��������:<strong>{HALL_NAME}</strong></p>
                <p>�����:<strong>{HALL_CITY}</strong></p>
                <p>�����:<strong>{HALL_ADRES}</strong></p>
            </div>
            <div class="clear"></div>
        </li>
    </ul>
    
    <h3>{HALL_PIC_H3}</h3>
    <div class="fl_left">
        <img src="/images/hall/{HALL_PIC}" width="249" height="142" alt="{HALL_NAME}" title="{HALL_NAME}" />
        <p><a href="/images/hall/big/{HALL_PIC}" target="_blank" title="���������">��������� <img src="/img/plus.gif" width="12" height="12" alt="���������" title="���������" /></a></p>
    </div>
    
    {HALL_PREVIEW}
    
    <div class="clear"></div>
    
    <a href="/admin/addsectors/{HALL_ID}" title="�������� �������"><img src="/img/admin_icons/add_page.png" width="32" height="32" alt="�������� �������" title="�������� �������" /></a>
    
    <form action="/admin/updatesectors/" method="post">
    <input type="hidden" name="hall_id" value="{HALL_ID}" />
    <table>
        <tr>
            <th>������</th>
            <th class="center">���-�� �����</th>
            <th class="right">��������</th>
        </tr>
        <!-- BDP: sectors_row -->
        <tr>
            <td class="left">{SECTOR_NAME}</td>
            <td class="center"><!--<input type="text" name="sector_count_{SECTOR_ID}" value="{SECTOR_COUNT}" />-->{SECTOR_COUNT}</td>
            <td class="right"><a href="/admin/editsector/{SECTOR_ID}" title="�������������"><img src="/img/admin_icons/edit.png" width="16" height="16" alt="�������������" title="�������������" /></a>&nbsp;&nbsp;&nbsp;<a href="/admin/deletesector/{SECTOR_ID}" title="�������"><img src="/img/admin_icons/delete.png" width="16" height="16" alt="�������" title="�������" /></a></td>
        </tr>
        <!-- EDP: sectors_row -->
        
        
        
        <!-- BDP: sectors_row_empty -->
        <tr>
            <td colspan="3" class="center"><i>�������� �� �������</i></td>
        </tr>
        <!-- EDP: sectors_row_empty -->
        <!-- BDP: sector_confirm -->
        <tr>
            <td colspan="3" class="right"><input type="submit" value="���������" class="button" /></td>
        </tr>
        <!-- EDP: sector_confirm -->
    </table>
    </form>
</div>
<!-- EDP: detail -->