

<SCRIPT Language=JavaScript >
//Скрипт очищающий форму от текста при нажатии на нее курсора 
function doClear(theText) { if (theText.value == theText.defaultValue) { theText.value = "" } }
function doDefault(theText) { if (theText.value == "") { theText.value = theText.defaultValue } }
</script>



<div class="main_block2">
<div class="main_block">
    <div class="welcome_block">
        {WELC_ERR}
        
        {WELCOME_BLOCK}
        <iframe src="http://www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FDiamond-Alliance-Alans-Brilliantov-{SITE5}%2F140204912703046&amp;width=250&amp;colorscheme=light&amp;show_faces=true&amp;stream=false&amp;header=false&amp;height=320" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:250px; margin-left: 20px; height:320px; float: left;" allowTransparency="true" align="center"></iframe>
       
        
        <script type="text/javascript" src="http://userapi.com/js/api/openapi.js?34"></script>

<!-- VK Widget -->
<div id="vk_groups" style="float:right; margin-right: 20px;"></div>
<script type="text/javascript">
VK.Widgets.Group("vk_groups", {mode: 0, width: "250", height: "320"}, 30447271);
</script>
    </div>
    
    <div class="register_block">
        <h3>Регистрация</h3>
        <form method="post" action="/">
        <table>
            <tr>
                <td class="width">Ваше имя</td>
                <td>
                    
                    <input type="text" name="name" value="{REGISTRATION_NAME}"  /></td>
            </tr>
            <tr>
                <td>Ваша фамилия</td>
                <td><input type="text" name="surname" value="{REGISTRATION_SURNAME}" /></td>
            </tr>
            <tr>
                <td>Ваш номер Amway</td>
                <td><input type="text" name="number" value="{REGISTRATION_NUMBER}" /></td>
            </tr>
            <tr>
                <td>Ваш e-mail</td>
                <td><input type="text" name="email" value="{REGISTRATION_EMAIL}" /></td>
            </tr>
            <tr>
                <td>Ваш телефон</td>
                <td><input type="text" name="phone" value="{REGISTRATION_PHONE}" /></td>
            </tr>
            <tr>
                <td>Ваш Бриллиантовый НПА</strong></td>
                <td><input type="text" name="diamond"  value="{REGISTRATION_DIAMOND}" {SCRIPT_CLEAR_DIAMOND} /></td>
            </tr>
            <tr>
                <td>Ваш Изумрудный НПА</td>
                <td><input type="text" name="emerald" value="{REGISTRATION_EMERALD}" {SCRIPT_CLEAR_EMERALD} /></td>
            </tr>
            <tr>
                <td>Ваш Платиновый НПА</td>
                <td><input type="text" name="platinum" value="{REGISTRATION_PLATINUM}" {SCRIPT_CLEAR_PLATINUM} /></td>
            </tr>
            <!--<tr>
                <td colspan="2"><strong>Поля, отмеченные <span>*</span>, обязательны для заполнения</strong></td>
            </tr>-->
            <tr>
                <td class="top">Введите символы,<br />указанные на картинке</td>
                <td><input type="text" class="small" name="code" value="{REGISTRATION_CODE}" /><br /><a href="javascript: reloadImage();">Обновить картинку</a><br /><img src="/captcha/kcaptcha_init.php" width="114" height="45" alt="" id="captcha" /><br /><input type="submit" value="Регистрация" class="button" name="registration" /></td>
            </tr>
        </table>
        <!-- BDP: registration_false -->
        <!--<p>Необходимо заполнить все поля</p>-->
        {REGISTRATION_ERROR}
        <!-- EDP: registration_false -->
        </form>
    </div>
</div>
</div>
<div class="clear"></div>