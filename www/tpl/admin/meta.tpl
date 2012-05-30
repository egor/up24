<!-- BDP: meta_list -->
<table>
    <tr>
        <th>Название</th>
        <th class="center" width="150px">Действия</th>
    </tr>
    <!-- BDP: meta_list_row -->
    <tr>
        <td>{META_NAME}</td>
        <td class="center">
            <a href="/admin/editmetatag/{META_ID}" title="Редактировать"><img src="/img/admin_icons/edit.png" width="16px" height="16px" alt="Редактировать" title="Редактировать" />
        </td>
    </tr>
    <!-- EDP: meta_list_row -->
</table>
<!-- EDP: meta_list -->

<!-- BDP: smeta_edit -->
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

<form method="post" action="/admin/editmetatag/{META_ID}">
<table>
    <tr>
        <td class="width"><span>*</span> Header</td>
        <td><input type="text" name="edit_header" value="{META_HEADER}" /></td>
    </tr>
    <tr>
        <td><span>*</span> Title</td>
        <td><input type="text" name="edit_title" value="{META_TITLE}" /></td>
    </tr>
    <tr>
        <td><span>*</span> Keywords</td>
        <td><input type="text" name="edit_keywords" value="{META_KEYWORDS}" /></td>
    </tr>
    <tr>
        <td><span>*</span> Description</td>
        <td><input type="text" name="edit_description" value="{META_DESCRIPTION}" /></td>
    </tr>
    
    <tr>
        <td>
            <span>*</span> Текст страницы
        </td>
        <td>
            <textarea class="mceEditor" name="edit_body">{META_BODY}</textarea>
        </td>
    </tr>

    <tr>
        <td>Редактируемое поле 1</td>
        <td><textarea  name="ed1" class="mceEditor" rows=2 style="width: 98%;">{ED1}</textarea></td>
    </tr>
        <tr>
        <td>Редактируемое поле 2</td>
        <td><textarea name="ed2" class="mceEditor" rows=2 style="width: 98%;">{ED2}</textarea></td>
    </tr>
    <tr>
        <td colspan="2"><strong>Поля, отмеченные <span>*</span>, обязательны для заполнения</strong></td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit" value="Применить" class="button" name="edit_meta" /></td>
    </tr>
</table>
<!-- BDP: meta_false -->
<p>Необходимо заполнить все поля.</p>
<!-- EDP: meta_false -->
</form>
<!-- EDP: meta_edit -->