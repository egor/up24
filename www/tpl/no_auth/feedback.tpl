<center><div class="feedback_block">
    <!--<h1>Обратная связь</h1>-->
    <form method="post" action="/feedback">
    <table>
        <tr>
            <td class="width"><span>*</span> Введите Ваши Ф.И.О.</td>
            <td><input type="text" name="name" value="{FEED_NAME}" /></td>
        </tr>
        <tr>
            <td><span>*</span> Введите Ваш e-mail:</td>
            <td><input type="text" name="email" value="{FEED_EMAIL}" /></td>
        </tr>
        <tr>
            <td>Введите Ваш телефон в<br />формате <strong>{SITE7} 999 1234567:</strong></td>
            <td><input type="text" name="phone" value="{FEED_PHONE}" /></td>
        </tr>
        <tr>
            <td colspan="2"><span>*</span> Введите текст сообщения:<br /><textarea name="message">{FEED_MESSAGE}</textarea></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Поля, отмеченные <span>*</span>, обязательны для заполнения</strong></td>
        </tr>
        <tr>
            <td class="top">Введите символы,<br />указанные на картинке:</td>
            <td><input type="text" class="small" name="code" value="{FEED_CODE}" /><br /><a href="javascript: reloadImage();">Другую картинку</a><br /><img src="/captcha/kcaptcha_init.php" width="111" height="45" alt="" id="captcha" /><br /><input type="submit" value="Отправить сообщение" class="button" name="feedback" /></td>
        </tr>
    </table>
    <!-- BDP: feedback_false -->
    <p>Необходимо заполнить все поля.</p>
    {FEEDBACK_ERROR}
    <!-- EDP: feedback_false -->
    </form>
</div>
<div class="clear"></div></center>