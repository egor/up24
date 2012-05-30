<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
    tinyMCE.init({
        mode : "specific_textareas",
        editor_selector : "mceEditor",
        language : "ru",
        mode : "textareas",
        theme : "advanced",
        plugins : "imagemanager,filemanager,safari,layer,table,advhr,advimage,advlink,preview,media,contextmenu,paste,directionality,noneditable,visualchars, nonbreaking",
        theme_advanced_buttons2_add : "separator,preview",
        theme_advanced_buttons2_add_before: "cut,copy,pastetext,pasteword,separator",
        theme_advanced_buttons3_add_before : "tablecontrols,separator",
        theme_advanced_buttons3_add : "media,advhr,separator,visualchars,nonbreaking",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_path_location : "bottom",
        plugin_insertdate_dateFormat : "%Y-%m-%d",
        plugin_insertdate_timeFormat : "%H:%M:%S",
        extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color],span[class|align|style]",
        file_browser_callback : "mcImageManager.filebrowserCallBack",
        theme_advanced_resize_horizontal : true,
        theme_advanced_resizing : true,
        apply_source_formatting : false,
        relative_urls : false,
        add_unload_trigger : true,
        strict_loading_mode : true
    });
</script>

<form method="post" action="/admin/lookups/">

<table>
    <tr>
        <td class="width"><span>*</span> Alt/Title для логотипа</td>
        <td><input type="text" name="logo_alt" value="{LOGO_ALT}" /></td>
    </tr>
    <tr>
        <td class="width"><span>*</span> Alt/Title для иконки "На главную"</td>
        <td><input type="text" name="home_alt" value="{HOME_ALT}" /></td>
    </tr>
    <tr>
        <td class="width"><span>*</span> Alt/Title для иконки "Обратная связь"</td>
        <td><input type="text" name="feed_alt" value="{FEED_ALT}" /></td>
    </tr>
    <tr>
        <td class="width"><span>*</span> Alt/Title для иконки "Добавить в избранное"</td>
        <td><input type="text" name="fav_alt" value="{FAV_ALT}" /></td>
    </tr>
    <tr>
        <td class="width">
            <span>*</span> Адрес внизу страницы<br />
        </td>
        <td><textarea name="bottom_adres">{BOTTOM_ADRES}</textarea></td>
    </tr>
    <tr>
        <td class="width">
            <span>*</span> Слоган внизу страницы<br />
        </td>
        <td><textarea name="slg_bottom">{SLG_BOTTOM}</textarea></td>
    </tr>
    <tr>
        <td class="width">
            <span>*</span> Краткий текст, слева от формы регистрации<br />
        </td>
        <td><textarea name="welcome_block" class="mceEditor">{WELCOME_BLOCK}</textarea></td>
    </tr>
    <tr>
        <td class="width">
            <span>*</span> Страница для печати: Адрес внизу страницы<br />
        </td>
        <td><textarea name="print_bottom_adres">{PRINT_BOTTOM_ADRES}</textarea></td>
    </tr>
    <tr>
        <td class="width">
            <span>*</span> Страница для печати: Номера телефонов внизу страницы<br />
        </td>
        <td><textarea name="print_bottom_phones">{PRINT_BOTTOM_PHONES}</textarea></td>
    </tr>
    <tr>
        <td colspan="2"><strong>Поля, отмеченные <span>*</span>, обязательны для заполнения</strong></td>
    </tr>
    
        
    <tr>
        <td colspan="2"><input type="submit" value="Применить" class="button" name="edit_lookups" /></td>
    </tr>
</table>
</form>