<!-- BDP: feedback_list -->
<table>
    <tr>
        <th width="150px" class="center">����</th>
        <th>�.�.�.</th>
        <th width="200px">E-Mail</th>
        <th width="100px">�������</th>
        <th class="center" width="80px">��������</th>
    </tr>
    <!-- BDP: feedback_list_row -->
    <tr>
        <td><a href="/admin/viewfeedback/{FEED_ID}">{FEED_DATE}</a></td>
        <td>{FEED_FIO}</td>
        <td>{FEED_EMAIL}</td>
        <td>{FEED_PHONE}</td>
        <td class="center">
            <!-- BDP: feedback_list_row_read -->
            <a href="/admin/setreadfeedback/{FEED_ID}" title="�������� ��� �����������"><img src="/img/admin_icons/read.png" width="16px" height="16px" alt="�������� ��� �����������" title="�������� ��� �����������" /></a>&nbsp;&nbsp;
            <!-- EDP: feedback_list_row_read -->
            <a href="/admin/deletefeedback/{FEED_ID}" title="�������"><img src="/img/admin_icons/delete.png" width="16px" height="16px" alt="�������" title="�������" /></a>
        </td>
    </tr>
    <!-- EDP: feedback_list_row -->
</table>
<!-- EDP: feedback_list -->

<!-- BDP: feedback_detail -->
<table>
    <tr>
        <th width="150px" class="center">����</th>
        <th>�.�.�.</th>
        <th width="200px">E-Mail</th>
        <th width="100px">�������</th>
        <th class="center" width="80px">��������</th>
    </tr>
    <tr>
        <td>{FEED_DATE}</td>
        <td>{FEED_FIO}</td>
        <td>{FEED_EMAIL}</td>
        <td>{FEED_PHONE}</td>
        <td class="center">
            <a href="/admin/deletefeedback/{FEED_ID}" title="�������"><img src="/img/admin_icons/delete.png" width="16px" height="16px" alt="�������" title="�������" /></a>
        </td>
    </tr>
    <tr>
        <th colspan="5" class="center">���������</td>
    </tr>
    <tr>
        <td colspan="5">{FEED_MESSAGE}</td>
    </tr>
</table>
<!-- EDP: feedback_detail -->