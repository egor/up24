<!-- BDP: admin_block -->
<div class="login_block">
    <table>
        <tr>
            <td>������������ �������������</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><a href="/admin/main">�����-������</a></td>
            <td><a href="/logout">�����</a></td>
        </tr>
    </table>
</div>
<!-- EDP: admin_block -->

<!-- BDP: user_block -->
<div class="login_block">
    <table>
        <tr>
            <td>������������</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><a href="/logout">�����</a></td>
        </tr>
    </table>
</div>
<!-- EDP: user_block -->

<!-- BDP: auth_block -->
<div class="login_block">
    <form method="post" action="/">
    <table>
        <tr>
            <td>����������� �����</td>
            <td>������</td>
        </tr>
        <tr>
            <td class="remember_me_tr_a"><input type="text" name="email" /></td>
            <td class="remember_me_tr_a"><input type="password" name="password" /><input type="submit" value="�����" class="submit" name="auth" /></td>
        </tr>
        <tr>
            <td><input id="remember_me" type="checkbox" checked="checked" value="1" name="remember_me"><label for="remember_me">&nbsp;&nbsp;��������� ����</label></td>
            <td><a href="/remind">������ ������?</a></td>
        </tr>
    </table>
    </form>
</div>
<!-- EDP: auth_block -->

<!-- BDP: remind_password -->
<center>
<div class="feedback_block">
    <!--<h1>�������������� ������</h1>-->
    <form method="post" action="/remind">
    <table>
        <tr>
            <td><span>*</span> ������� ��� e-mail:</td>
            <td><input type="text" name="email" value="{REMIND_EMAIL}" /></td>
        </tr>
        <tr>
            <td colspan="2"><strong>����, ���������� <span>*</span>, ����������� ��� ����������</strong></td>
        </tr>
        <tr>
            <td class="top">������� �������,<br />��������� �� ��������:</td>
            <td><input type="text" class="small" name="code" value="{REMIND_CODE}" /><br /><a href="javascript: reloadImage();">������ ��������</a><br /><img src="/captcha/kcaptcha_init.php" width="114" height="45" alt="" id="captcha" /><br /><input type="submit" value="������������ ������" class="button" name="remind" /></td>
        </tr>
    </table>
    {REMIND_ERROR}
    </form>
</div>
<div class="clear"></div>
</center>
<!-- EDP: remind_password -->

<!-- BDP: restore_password -->
<center>
<div class="feedback_block">
    <!--<h1>�������������� ������</h1>-->
    <form method="post" action="/restore">
    <input type="hidden" name="code" value="{RESTORE_CODE}" />
    <table>
        <tr>
            <td><span>*</span> ����� ������:</td>
            <td><input type="password" name="password" value="{RESTORE_PASSWORD}" /></td>
        </tr>
        <tr>
            <td><span>*</span> ������������� ������:</td>
            <td><input type="password" name="confirm" value="{RESTORE_CONFIRM}" /></td>
        </tr>
        <tr>
            <td colspan="2"><strong>����, ���������� <span>*</span>, ����������� ��� ����������</strong></td>
        </tr>
        <tr>
            <td class="top">&nbsp;</td>
            <td><input type="submit" value="�������� ������" class="button" name="restore" /></td>
        </tr>
    </table>
    {RESTORE_ERROR}
    </form>
</div>
<div class="clear"></div>
</center>
<!-- EDP: restore_password -->

<!-- BDP: auth_page -->
<div class="main_block">
    <div class="welcome_block">
        {WELCOME_BLOCK}
    </div>
    
    <div class="register_block">
        <h3>�����������</h3>
        <form method="post" action="/enter">
        <table>
            <tr>
                <td><span>*</span> ������� ��� e-mail</td>
                <td><input type="text" name="email" value="" /></td>
            </tr>
            <tr>
                <td><span>*</span> ������� ��� ������</td>
                <td><input type="password" name="password" value="" /></td>
            </tr>
            <tr>
                <td colspan="2"><strong>����, ���������� <span>*</span>, ����������� ��� ����������</strong></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" value="�����" class="button" name="auth_page" /></td>
            </tr>
        </table>
        {AUTH_PAGE_FALSE}
        </form>
    </div>
</div>
<!-- EDP: auth_page -->