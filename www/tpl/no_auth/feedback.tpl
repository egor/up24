<center><div class="feedback_block">
    <!--<h1>�������� �����</h1>-->
    <form method="post" action="/feedback">
    <table>
        <tr>
            <td class="width"><span>*</span> ������� ���� �.�.�.</td>
            <td><input type="text" name="name" value="{FEED_NAME}" /></td>
        </tr>
        <tr>
            <td><span>*</span> ������� ��� e-mail:</td>
            <td><input type="text" name="email" value="{FEED_EMAIL}" /></td>
        </tr>
        <tr>
            <td>������� ��� ������� �<br />������� <strong>{SITE7} 999 1234567:</strong></td>
            <td><input type="text" name="phone" value="{FEED_PHONE}" /></td>
        </tr>
        <tr>
            <td colspan="2"><span>*</span> ������� ����� ���������:<br /><textarea name="message">{FEED_MESSAGE}</textarea></td>
        </tr>
        <tr>
            <td colspan="2"><strong>����, ���������� <span>*</span>, ����������� ��� ����������</strong></td>
        </tr>
        <tr>
            <td class="top">������� �������,<br />��������� �� ��������:</td>
            <td><input type="text" class="small" name="code" value="{FEED_CODE}" /><br /><a href="javascript: reloadImage();">������ ��������</a><br /><img src="/captcha/kcaptcha_init.php" width="111" height="45" alt="" id="captcha" /><br /><input type="submit" value="��������� ���������" class="button" name="feedback" /></td>
        </tr>
    </table>
    <!-- BDP: feedback_false -->
    <p>���������� ��������� ��� ����.</p>
    {FEEDBACK_ERROR}
    <!-- EDP: feedback_false -->
    </form>
</div>
<div class="clear"></div></center>