<div class="main_block">
    <div class="welcome_block">
        {WELCOME_BLOCK}
    </div>
    
    <div class="register_block">
        <h3>�����������</h3>
        <form method="post" action="/">
        <table>
            <tr>
                <td class="width"><span>*</span> ������� ���� ���</td>
                <td><input type="text" name="name" value="{REGISTRATION_NAME}" /></td>
            </tr>
            <tr>
                <td><span>*</span> ������� ���� �������</td>
                <td><input type="text" name="surname" value="{REGISTRATION_SURNAME}" /></td>
            </tr>
            <tr>
                <td><span>*</span> ������� ��� ����� Amway</td>
                <td><input type="text" name="number" value="{REGISTRATION_NUMBER}" /></td>
            </tr>
            <tr>
                <td><span>*</span> ������� ��� e-mail	</td>
                <td><input type="text" name="email" value="{REGISTRATION_EMAIL}" /></td>
            </tr>
            <tr>
                <td><span>*</span> ������� ��� ������� � <br />������� <strong>+38 (067) 8889900:</strong></td>
                <td><input type="text" name="phone" value="{REGISTRATION_PHONE}" /></td>
            </tr>
            <tr>
                <td><span>*</span> ������� ������� ������<br /><strong>��������������</strong> ��������</td>
                <td><input type="text" name="diamond" value="{REGISTRATION_DIAMOND}" /></td>
            </tr>
            <tr>
                <td><span>*</span> ������� ������� ������<br /><strong>�����������</strong> ��������</td>
                <td><input type="text" name="emerald" value="{REGISTRATION_EMERALD}" /></td>
            </tr>
            <tr>
                <td><span>*</span> ������� ������� ������<br /><strong>�����������</strong> ��������</td>
                <td><input type="text" name="platinum" value="{REGISTRATION_PLATINUM}" /></td>
            </tr>
            <tr>
                <td colspan="2"><strong>����, ���������� <span>*</span>, ����������� ��� ����������</strong></td>
            </tr>
            <tr>
                <td class="top">������� �������,<br />��������� �� ��������:</td>
                <td><input type="text" class="small" name="code" value="{REGISTRATION_CODE}" /><br /><a href="javascript: reloadImage();">������ ��������</a><br /><img src="/captcha/kcaptcha_init.php" width="114" height="45" alt="" id="captcha" /><br /><input type="submit" value="�����������" class="button" name="registration" /></td>
            </tr>
        </table>
        <!-- BDP: registration_false -->
        <p>���������� ��������� ��� ����.</p>
        {REGISTRATION_ERROR}
        <!-- EDP: registration_false -->
        </form>
    </div>
</div>

<div class="clear"></div>

<h1>{HEADER}</h1>
{BODY}