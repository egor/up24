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

<form method="post" action="/admin/settings/">

<table>
    <tr>
        <th class="center" colspan="2">ѕостраничный навигатор</th>
    </tr>
    <tr>
        <td class="width"> оличество новостей в списке на страницу</td>
        <td><input type="text" name="num_news" value="{NUM_NEWS}" /></td>
    </tr>
    <tr>
        <td class="width"> оличество разделов/статей в списке на страницу</td>
        <td><input type="text" name="num_pages" value="{NUM_PAGES}" /></td>
    </tr>


    <tr>
        <th class="center" colspan="2">јвторизаци€</th>
    </tr>
    <tr>
        <td class="width">ќтображать форму авторизации в шапке сайта?</td>
        <td><input type="radio" name="use_auth" value="1" class="input_radio"{USE_AUTH_TRUE} />ƒа&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="use_auth" value="0" class="input_radio"{USE_AUTH_FALSE} />Ќет</td>
    </tr>
    
    <tr>
        <th class="center" colspan="2">–егистраци€</th>
    </tr>
    <tr>
        <td class="width"><span>*</span> «аголовок (тема) письма с регистрационными данными</td>
        <td><input type="text" name="register_subject" value="{REGISTER_SUBJECT}" /></td>
    </tr>
    <tr>
        <td class="width">
            <span>*</span> “екст письма с регистрационными данными<br /><br />
            <span class="small">%LOGIN%, %PASSWORD% - подстав€тс€ регистрационные данные пользовател€</span>
        </td>
        <td><textarea class="mceEditor" name="register_message">{REGISTER_MESSAGE}</textarea></td>
    </tr>
    
    <tr>
        <th class="center" colspan="2">¬осстановление парол€</th>
    </tr>
    <tr>
        <td class="width"><span>*</span> «аголовок (тема) письма с ссылкой дл€ восстановлени€ парол€</td>
        <td><input type="text" name="remind_subject" value="{REMIND_SUBJECT}" /></td>
    </tr>
    <tr>
        <td class="width">
            <span>*</span> “екст письма с ссылкой дл€ восстановлени€ парол€<br /><br />
            <span class="small">%RESTORE_LINK% - будет заменено на сгенерированную ссылку восстановлени€ парол€</span>
        </td>
        <td><textarea class="mceEditor" name="remind_message">{REMIND_MESSAGE}</textarea></td>
    </tr>
    
    <tr>
        <th class="center" colspan="2">”становка нового парол€</th>
    </tr>
    <tr>
        <td class="width"><span>*</span> «аголовок (тема) письма с новыми регистрационными данными</td>
        <td><input type="text" name="restore_subject" value="{RESTORE_SUBJECT}" /></td>
    </tr>
    <tr>
        <td class="width">
            <span>*</span> “екст письма с новыми регистрационными данными<br /><br />
            <span class="small">%LOGIN%, %PASSWORD% - подстав€тс€ регистрационные данные пользовател€</span>
        </td>
        <td><textarea class="mceEditor" name="restore_message">{RESTORE_MESSAGE}</textarea></td>
    </tr>
    
    <tr>
        <th class="center" colspan="2">‘орма обратной св€зи</th>
    </tr>
    <tr>
        <td class="width">
            <span>*</span> ѕочтовые адреса, дл€ отправки сообщений формы обратной св€зи<br /><br />
            <span class="small">можно указывать несколько, раздел€€ адреса зап€той</span>
        </td>
        <td><input type="text" name="feedback_email" value="{FEEDBACK_EMAIL}" /></td>
    </tr>
    <tr>
        <td class="width"><span>*</span> «аголовок (тема) письма с формы обратной св€зи</td>
        <td><input type="text" name="feedback_subject" value="{FEEDBACK_SUBJECT}" /></td>
    </tr>
    <tr>
        <td class="width">
            <span>*</span> “екст письма с формы обратной св€зи<br /><br />
            <span class="small">%NAME%, %EMAIL%, %PHONE%, %MESSAGE% - подстав€тс€ введеные пользователем данные</span>
        </td>
        <td><textarea class="mceEditor" name="feedback_message">{FEEDBACK_MESSAGE}</textarea></td>
    </tr>
    
    <tr>
        <th class="center" colspan="2">«аказ билетов</th>
    </tr>
    <tr>
        <td class="width"><span>*</span> «аголовок (тема) письма с данным о заказе</td>
        <td><input type="text" name="ticketbuy_subject" value="{TICKETBUY_SUBJECT}" /></td>
    </tr>
    <tr>
        <td class="width">
            <span>*</span> “екст письма с данным о заказе<br /><br />
            <span class="small">%NUMBER%, %DATE%, %CITY%, %ADRES%, %TIMESTART%, %TYPE%, %COUNT% - подстав€тс€ введеные пользователем данные</span>
        </td>
        <td><textarea class="mceEditor" name="ticketbuy_message">{TICKETBUY_MESSAGE}</textarea></td>
    </tr>
    
    <tr>
        <th class="center" colspan="2">«аказ дисков</th>
    </tr>
    <tr>
        <td class="width">
            <span>*</span> ѕочтовые адреса, дл€ отправки сообщений с данными о заказе дисков<br /><br />
            <span class="small">можно указывать несколько, раздел€€ адреса зап€той</span>
        </td>
        <td><input type="text" name="discbuy_email" value="{DISCBUY_EMAIL}" /></td>
    </tr>
    <tr>
        <td class="width"><span>*</span> «аголовок (тема) письма с данными о заказе дисков</td>
        <td><input type="text" name="discbuy_subject" value="{DISCBUY_SUBJECT}" /></td>
    </tr>
    
    <tr>
        <td colspan="2"><strong>ѕол€, отмеченные <span>*</span>, об€зательны дл€ заполнени€</strong></td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit" value="ѕрименить" class="button" name="edit_settings" /></td>
    </tr>
</table>
</form>