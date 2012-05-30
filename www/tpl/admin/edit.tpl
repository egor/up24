<!-- BDP: start -->
<form enctype="multipart/form-data" method="post" action="">
<table Border=0 CellSpacing=0 CellPadding=0 Width="100%" Align="" vAlign="">
<!-- EDP: start -->

<!-- BDP: jq_gallery_edit_picture -->
<script language="javascript" type="text/javascript" src="/js/jq-gallery-edit-picture.js"></script>
<!-- EDP: jq_gallery_edit_picture -->

<!-- BDP: mce -->
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
        theme_advanced_buttons3_add : "media,advhr,separator,visualchars,nonbreaking,forecolor,backcolor",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_path_location : "bottom",
        plugin_insertdate_dateFormat : "%Y-%m-%d",
        plugin_insertdate_timeFormat : "%H:%M:%S",
        extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color],span[class|align|style]",
        file_browser_callback : "mcImageManager.filebrowserCallBack",
        theme_advanced_resize_horizontal : true,
        theme_advanced_resizing : true,
        apply_source_formatting : true,
        relative_urls : false,
        add_unload_trigger : true,
        strict_loading_mode : true
    });
</script>
<!-- EDP: mce -->

<!-- BDP: date -->
<link rel="stylesheet" type="text/css" media="all" href="/css/calendar-blue.css" title="winter" />
  <script type="text/javascript" src="/js/calendar.js"></script>
  <script type="text/javascript" src="/js/calendar-ru.js"></script>
  <script type="text/javascript" src="/js/calendar-setup.js"></script>
<tr>
     <td>Дата: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size="10" name="date" value="{ADM_DATE}" id="date_i" readonly="1" /> 
     <img src="/img/calendar.gif" id="trigger" style="cursor: pointer; border: 1px solid red;" title="Date selector"
      onmouseover="this.style.background='red';" onmouseout="this.style.background=''" /></td>
</tr>
<script type="text/javascript">

    Calendar.setup({
        inputField     :    "date_i",     // id of the input field
        //ifFormat       :    "%Y-%m-%d",      // format of the input field
        ifFormat       :    "%d.%m.%Y",      // format of the input field
        button         :    "trigger",  // trigger for the calendar (button ID)
        align          :    "B1",           // alignment (defaults to "Bl")
        singleClick    :    true
    });
</script>


<!-- EDP: date -->

<!-- BDP: settings -->
 <!-- BDP: settings_item -->
<tr>
  <td width="220" valign="middle">{NAME}: &nbsp;&nbsp;&nbsp;</td><td><textarea rows="1" style="width: 100%;" name="settings_{KEY}">{VALUE}</textarea><br></td>
</tr>
 <!-- EDP: settings_item -->
<!-- EDP: settings -->

<!-- BDP: lookups -->
    <!-- BDP: lookups_item -->
    <tr>
        <td width="220" valign="middle">{LOOK_NAME}: &nbsp;&nbsp;&nbsp;</td><td><textarea rows="4" style="width: 100%;" name="{LOOK_KEY}">{LOOK_VALUE}</textarea><br></td>
    </tr>
    <!-- EDP: lookups_item -->
<!-- EDP: lookups -->

<!-- BDP: adress -->
<tr>
     <td>Адрес в URL <small><font color="red">[</font>A-Z<font color="red">]</font><font color="red">[</font>a-z<font color="red">]</font><font color="red">[</font>0-9<font color="red">]</font><font color="red">[</font>-<font color="red">]</font><font color="red">[</font>_<font color="red">]</font></small>:</td><td><input type=text size=50 name="adm_href" value='{ADM_HREF}'><br></td>
</tr>
<!-- EDP: adress -->

<!-- BDP: hallcity -->
<tr>
    <td>Выберите город<br /><br />Или введите новый</td>
    <td>
  	<select name="city_id">{ADM_CITY_LIST}
  	</select>
  	<br /><br />
  	<input type="text" size=50 name="city_name" value='{ADM_CITY_NAME}'>
    </td>
</tr>
<!-- EDP: hallcity -->

<!-- BDP: hallselect -->
<tr>
    <td>Выберите зал</td>
    <td>
  	<select name="hall_id">{ADM_HALL_LIST}
  	</select>
    </td>
</tr>
<!-- EDP: hallselect -->

<!-- BDP: eventtype -->
<tr>
    <td>Выберите тип семинара</td>
    <td>
  	<select name="type_id">{ADM_TYPE_LIST}
  	</select>
    </td>
</tr>
<!-- EDP: eventtype -->

<!-- BDP: eventpic -->
<tr>
    <td>Логотип семинара:</td><td><input type="file" name="eventpic"></td>
</tr>
<!-- EDP: eventpic -->

<!-- BDP: eventdatetime -->
<link rel="stylesheet" type="text/css" media="all" href="/css/calendar-blue.css" title="winter" />
<script type="text/javascript" src="/js/calendar.js"></script>
<script type="text/javascript" src="/js/calendar-ru.js"></script>
<script type="text/javascript" src="/js/calendar-setup.js"></script>
<tr>
     <td>Дата: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size="10" name="eventdate" value="{ADM_DATE}" id="date_i" readonly="1" style="width: 100px;text-align:center;" /> 
     <img src="/img/calendar.gif" id="trigger" style="cursor: pointer;" /></td>
</tr>
<tr>
    <tr>
     <td>Дата Конца: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size="10" name="eventdate2" value="{ADM_DATE2}" id="date_i2" readonly="1" style="width: 100px;text-align:center;" /> 
     <img src="/img/calendar.gif" id="trigger2" style="cursor: pointer;" /></td>
</tr>
<tr>

    
     <td>Время: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size="10" name="eventtime" value="{ADM_TIME}" style="width: 100px;text-align:center;" /></td>
</tr>
<script type="text/javascript">

    Calendar.setup({
        inputField     :    "date_i",     // id of the input field
        //ifFormat       :    "%Y-%m-%d",      // format of the input field
        ifFormat       :    "%d.%m.%Y",      // format of the input field
        button         :    "trigger",  // trigger for the calendar (button ID)
        align          :    "B1",           // alignment (defaults to "Bl")
        singleClick    :    true
    });
</script>
<script type="text/javascript">

    Calendar.setup({
        inputField     :    "date_i2",     // id of the input field
        //ifFormat       :    "%Y-%m-%d",      // format of the input field
        ifFormat       :    "%d.%m.%Y",      // format of the input field
        button         :    "trigger2",  // trigger for the calendar (button ID)
        align          :    "B1",           // alignment (defaults to "Bl")
        singleClick    :    true
    });
</script>
<!-- EDP: eventdatetime -->

<!-- BDP: visible -->
<tr>
    <td>Видимость</td>
    <td>
  	<select name="visible">{VISIBLE_S}
  	</select>
    </td>
</tr>
<!-- EDP: visible -->

<!-- BDP: slide -->
<tr>
    <td>Выпадающее</td>
    <td>
  	<select name="slide">{SLIDE_S}
  	</select>
    </td>
</tr>
<!-- EDP: slide -->

<!-- BDP: artikul -->
<tr>
     <td>Артикул: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="artikul" value='{ADM_ARTIKUL}'><br></td>
</tr>
<!-- EDP: artikul -->

<!-- BDP: cost -->
<tr>
     <td>Цена: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="cost" value='{ADM_COST}'><br></td>
</tr>
<!-- EDP: cost -->

<!-- BDP: header -->
<tr>
     <td>Header: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="header" value='{ADM_HEADER}'><br></td>
</tr>
<!-- EDP: header -->

<!-- BDP: name -->
<tr>
     <td>Название: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="name" value='{ADM_NAME}'><br></td>
</tr>
<!-- EDP: name -->

<!-- BDP: adres -->
<tr>
     <td>Адрес: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="adres" value='{ADM_ADRES}'><br></td>
</tr>
<!-- EDP: adres -->

<!-- BDP: meta -->
<tr>
     <td>Header: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="header" value='{ADM_HEADER}'><br></td>
</tr>
<tr>
     <td>Title: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="title" value='{ADM_TITLE}'><br></td>
</tr>
<tr>
     <td>Keywords: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="keywords" value='{ADM_KEYWORDS}'><br></td>
</tr>
<tr>
     <td>Description: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="description" value='{ADM_DESCRIPTION}'><br></td>
</tr>
<!-- EDP: meta -->

<!-- BDP: sectors_ajax -->
<tr id="sector_block">
     <td>Количество секторов в зале: </td><td><input type="text" size=50 name="sector_ajax" id="sector_ajax" value='0'><br><input type="button" value="Добавить" class="button2" onclick="addSectors();"></td>
</tr>
<!-- EDP: sectors_ajax -->

<!-- BDP: sectors_count -->
<input type="hidden" name="sector_count" id="sector_count" value="{SECTOR_COUNT}" />
<!-- EDP: sectors_count -->

<!-- BDP: sectors -->
<tr>
<td colspan="2">
{ADD_ROW}
{BACK_PAGE}
</td>
</tr>

<tr>
     <td colspan="2">Название сектора: <input type="text" class="small" name="sector[{INCREMENT}][name]" value="{SECTOR_NAME}" />&nbsp;&nbsp;&nbsp;Количество рядов в секторе: <input type="text" class="small2" name="sector[{INCREMENT}][count]" value="{SECTOR_COUNT}" /></td>
</tr>
{ROW_LIST_E}
<!-- EDP: sectors -->

<!-- BDP: rows -->


<tr>
     <td colspan="2">{BACK_PAGE_ROW}</td>
</tr>

<tr>
     <td colspan="2">Название ряда: <input type="text" class="small" name="series[{INCREMENT}][name]" value="{ROW_NAME}" />&nbsp;&nbsp;&nbsp;Номер первого места: <input type="text" class="small2" name="series[{INCREMENT}][first_location]" value="{ROW_COUNTF}" />&nbsp;&nbsp;&nbsp;Количество мест: <input type="text" class="small2" name="series[{INCREMENT}][count_location]" value="{ROW_COUNT}" /></td>
</tr>
<!-- EDP: rows -->

<!-- BDP: news -->
<link rel="stylesheet" type="text/css" media="all" href="/css/calendar-blue.css" title="winter" />
  <script type="text/javascript" src="/js/calendar.js"></script>
  <script type="text/javascript" src="/js/calendar-ru.js"></script>
  <script type="text/javascript" src="/js/calendar-setup.js"></script>
<tr>
     <td>Дата: &nbsp;&nbsp;&nbsp;</td><td><input type="text" size="10" name="date" value="{ADM_DATE}" id="date_i" readonly="1" /> 
     <img src="/img/calendar.gif" id="trigger" style="cursor: pointer; border: 1px solid red;" title="Date selector"
      onmouseover="this.style.background='red';" onmouseout="this.style.background=''" /></td>
</tr>
<script type="text/javascript">

    Calendar.setup({
        inputField     :    "date_i",     // id of the input field
        //ifFormat       :    "%Y-%m-%d",      // format of the input field
        ifFormat       :    "%d.%m.%Y",      // format of the input field
        button         :    "trigger",  // trigger for the calendar (button ID)
        align          :    "B1",           // alignment (defaults to "Bl")
        singleClick    :    true
    });
</script>

<tr>
     <td colspan=2><b>Анонс:</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan=2><textarea class="mceEditor" rows=10 style="width: 100%;" name="preview">{ADM_PREVIEW}</textarea></td>
</tr>
<!-- EDP: news -->

<!-- BDP: pos -->
<tr>
    <td>Позиция:</td><td><input type="text" size="50" name="position" value="{ADM_POSITION}"></td>
</tr>
<!-- EDP: pos -->
<!-- BDP: nomination -->
<tr>
    <td>Наименование:</td><td><input type="text" size="50" name="nomination" value="{ADM_NOMINATION}"></td>
</tr>
<!-- EDP: nomination -->
<!-- BDP: preview -->
<tr>
     <td colspan=2><b>Краткое описание:</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan="2"><textarea class="mceEditor" rows=25 style="width: 100%;" name="preview">{ADM_PREVIEW}</textarea></td>
</tr>
<!-- EDP: preview -->

<!-- BDP: body -->
<tr>
     <td colspan=2><b>Текст:</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan="2"><textarea class="mceEditor" rows=25 style="width: 100%;" name="body">{ADM_BODY}</textarea></td>
</tr>
<!-- EDP: body -->

<!-- BDP: image_signature -->
<tr>
     <td colspan=2><b>Подпись к изображению:</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan="2"><textarea class="mceEditor" rows=2 style="width: 98%;" name="body">{ADM_SIGNATURE}</textarea></td>
</tr>
<!-- EDP: image_signature -->

<!-- BDP: zip -->
<tr>
    <td>Файл архива Zip:</td><td><input type="file" name="zip"></td>
</tr>
<!-- EDP: zip -->

<!-- BDP: pic -->
<tr>
    <td>Картинка:</td><td><input type="file" name="pic"></td>
</tr>
{SHOW_PIC}

<!-- EDP: pic -->

<!-- BDP: hallpic -->
<tr>
    <td>Схема зала:</td><td><input type="file" name="hallpic"></td>
</tr>
<script>
function td(){
    if (!confirm("Точно удалить?")) {
        return false;
    } else {
        window.location = "/admin/edithall/{THISHALLID}?del=pichall";
    }

}
</script>
<tr style="{THISHALLPICVIS}">
    <td>{THISHALLPIC}</td><td><a href="#" onclick="td(); return false;"><img src="/img/admin_icons/delete.png" ></a></td>
</tr>
<!-- EDP: hallpic -->

<!-- BDP: import -->
<tr>
    <td>Файл формата MS-Excel:</td><td><input type="file" name="file"></td>
</tr>
<!-- EDP: import -->

<!-- BDP: recomendation -->
<tr>
     <td colspan=2><b>Рекомендуемые товары (разделитель запятая):</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan="2"><textarea rows=5 style="width: 562px;" name="recomendation">{ADM_RECOMENDATION}</textarea></td>
</tr>
<!-- EDP: recomendation -->

<!-- BDP: discount -->
<tr>
     <td>Размер скидки, в процентах (%): &nbsp;&nbsp;&nbsp;</td><td><input type="text" size=50 name="discount" value='{DISCOUNT_VALUE}'><br></td>
</tr>
<!-- EDP: discount -->

<!-- BDP: stars -->
<tr>
     <td colspan=2>&nbsp;</td>
</tr>
<tr>
     <td colspan=2><b>Модуль "Звёзды"</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
    <td>Звезда?</td>
    <td>
  	<select name="stars">{STARS_S}
  	</select>
    </td>
</tr>
<tr>
     <td colspan=2><b>Краткая информация (выводится в шапке страницы):</b> &nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
     <td colspan="2"><textarea class="mceEditor" rows=10 style="width: 100%;" name="stars_preview">{ADM_STARS_PREVIEW}</textarea></td>
</tr>
<tr>
    <td>Фотография:</td><td><input type="file" name="pic"></td>
</tr>
<!-- EDP: stars -->

<!-- BDP: upload_catalog_pics -->
<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td>Картинка1:</td><td><input type="file" name="pic1" class="frm_text"></td>
</tr>

<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td>Картинка2:</td><td><input type="file" name="pic2" class="frm_text"></td>
</tr>

<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td>Картинка3:</td><td><input type="file" name="pic3" class="frm_text"></td>
</tr>

<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td>Картинка4:</td><td><input type="file" name="pic4" class="frm_text"></td>
</tr>

<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td>Картинка5:</td><td><input type="file" name="pic5" class="frm_text"></td>
</tr>

<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td>Картинка6:</td><td><input type="file" name="pic6" class="frm_text"></td>
</tr>
<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td>Картинка7:</td><td><input type="file" name="pic7" class="frm_text"></td>
</tr>
<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td>Картинка8:</td><td><input type="file" name="pic8" class="frm_text"></td>
</tr>
<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td>Картинка9:</td><td><input type="file" name="pic9" class="frm_text"></td>
</tr>
<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td>Картинка10:</td><td><input type="file" name="pic10" class="frm_text"></td>
</tr>
<!-- EDP: upload_catalog_pics -->

<!-- BDP: gallery_pic -->
<tr>
    <td>Картинка:</td><td id="gallery-admin-pic">{ADM_PIC}</td>
</tr>
<tr>
     <td colspan="2"><img src="/img/gallery/themes/{ADM_PIC_PATH}" /></td>
</tr>
<!-- EDP: gallery_pic -->

<!-- BDP: gallery_filter -->
<tr>
    <td rowspan="5">Филтр</td>
    <td><input type="checkbox" name="filter[]" {ADM_FILTER1} value='1' /> Тренажерные залы      
      </td>
  </tr>
  <tr>
    <td><input type="checkbox"  name="filter[]"  {ADM_FILTER2} value='2' />Боевые искусства</td>
  </tr>
  <tr>
    <td><input type="checkbox"  name="filter[]"  {ADM_FILTER3} value='3' /> Кардио залы</td>
  </tr>
  <tr>
    <td><input type="checkbox"  name="filter[]"  {ADM_FILTER4} value='4' /> Шейпинг</td>
  </tr>
  <tr>
    <td><input type="checkbox"  name="filter[]"  {ADM_FILTER5} value='5' /> Игровые виды спорта</td>
  </tr>

<!-- EDP: gallery_filter -->


<!-- BDP: upload_gallery_pics_edit -->
<tr>
    <td colspan="2">&nbsp;</td>
</tr>

<tr>
    <td>Картинка 1:</td><td><input type="file" name="file1" class="frm_text"></td>
</tr>
{ADM_GELLARY_ITEM_VIEW_PIC}
<tr>
    <td>Позиция 1:</td><td><input type="text" size="20" name="position1" value="{ADM_POSITION1}">
</tr>
<tr>
    <td>Подпись к изображению 1:</td><td><input type="text" size="20" name="text1" value="{ADM_SIGNATURE1}">
</tr>
<!-- EDP: upload_gallery_pics_edit -->

<!-- BDP: upload_gallery_pics -->
<tr>
    <td colspan="2">&nbsp;</td>
</tr>

<tr>
    <td>Картинка 1:</td><td><input type="file" name="file1" class="frm_text"></td>
</tr>
{ADM_GELLARY_ITEM_VIEW_PIC}
<tr>
    <td>Позиция 1:</td><td><input type="text" size="20" name="position1" value="{ADM_POSITION1}">
</tr>
<tr>
    <td>Подпись к изображению 1:</td><td><input type="text" size="20" name="text1" value="{ADM_SIGNATURE1}">
</tr>


<tr class="upload_gallery_pics">
    <td colspan="2">&nbsp;</td>
</tr>
<tr class="upload_gallery_pics">
    <td>Картинка 2:</td><td><input type="file" name="file2" class="frm_text"></td>
</tr>
<tr class="upload_gallery_pics">
    <td>Позиция 2:</td><td><input type="text" size="20" name="position2" value="{ADM_POSITION2}">

</tr>
<tr>
    <td>Подпись к изображению 2:</td><td><input type="text" size="20" name="text2" value="{ADM_SIGNATURE2}">
</tr>

<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr class="upload_gallery_pics">
    <td>Картинка 3:</td><td><input type="file" name="file3" class="frm_text"></td>
</tr>
<tr class="upload_gallery_pics">
    <td>Позиция 3:</td><td><input type="text" size="20" name="position3" value="{ADM_POSITION3}">
</tr>
<tr>
    <td>Подпись к изображению 3:</td><td><input type="text" size="20" name="text3" value="{ADM_SIGNATURE3}">
</tr>


<tr class="upload_gallery_pics">
    <td colspan="2">&nbsp;</td>
</tr>
<tr class="upload_gallery_pics">
    <td>Картинка 4:</td><td><input type="file" name="file4" class="frm_text"></td>
</tr>
<tr class="upload_gallery_pics">
    <td>Позиция 4:</td><td><input type="text" size="20" name="position4" value="{ADM_POSITION4}">
</tr>
<tr>
    <td>Подпись к изображению 4:</td><td><input type="text" size="20" name="text4" value="{ADM_SIGNATURE4}">
</tr>

<tr class="upload_gallery_pics">
    <td colspan="2">&nbsp;</td>
</tr>
<tr class="upload_gallery_pics">
    <td>Картинка 5:</td><td><input type="file" name="file5" class="frm_text"></td>
</tr>
<tr class="upload_gallery_pics">
    <td>Позиция 5:</td><td><input type="text" size="20" name="position5" value="{ADM_POSITION5}">
</tr>
<tr>
    <td>Подпись к изображению 5:</td><td><input type="text" size="20" name="text5" value="{ADM_SIGNATURE5}">
</tr>

<!-- EDP: upload_gallery_pics -->

<!-- BDP: question -->
<tr>
     <td>Вопрос :</td><td><input type=text size=50 name="adm_question" value='{ADM_QUESTION}'></td>

</tr>
<tr><td colspan="2" height="25"></td></tr>
<tr>


<!-- BDP: answer -->

<td>Ответ {NUM_ANSWER}:</td><td><input type=text size=50 name="adm_answer[]" value='{ADM_ANSWER}'><input type="hidden" name="adm_answer_id[]" value='{ADM_ANSWER_ID}'>{ADM_DELL_QUESTION}</td>
</tr>
<tr>
     <td>Результат {NUM_ANSWER}:</td><td><input type=text size=15 name="adm_result[]" value='{ADM_RESULT}'></td>
</tr>
<tr><td colspan="2" height="25"></td></tr>

<!-- EDP: answer -->

<!-- EDP: question -->

<!-- BDP: new_answer -->

<td>Новый ответ :</td><td><input type=text size=50 name="adm_new_answer" value=''></td>
</tr>
<tr>
     <td>Новый результат :</td><td><input type=text size=15 name="adm_new_result" value=''></td>
</tr>
<tr><td colspan="2" height="25"></td></tr>

<!-- EDP: new_answer -->


<!-- BDP: banner_adress -->
<tr>
     <td>Ссылка баннера <small style="color: red;"><strong>(необязательна для Flash баннера)</strong></small>:</td><td><input type=text size=50 name="adm_href" value='{ADM_HREF}'><br></td>
</tr>
<!-- EDP: banner_adress -->

<!-- BDP: banner_object -->
<tr>
    <td>Баннер:</td><td><input type="file" name="object"></td>
</tr>
<!-- EDP: banner_object -->

<!-- BDP: banner_is_default -->
<tr>
    <td>Опубликовать: </td><td><input type="checkbox" name="is_default" value="yes" {IS_DEFAULT_CHECKED}></td>
</tr>
<!-- EDP: banner_is_default -->



<!-- BDP: page_pic -->
<tr>
    <td>Картинка для <b>статьи:</b></td><td><input type="file" name="page_pic"></td>
</tr>
<!-- EDP: page_pic -->


<!-- BDP: banner_text -->
<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td colspan="2"><strong>Код баннера:</strong></td>
</tr>
<tr>
    <td colspan="2"><textarea cols="80" rows="10" name="code">{BANNER_CODE}</textarea></td>
</tr>
<!-- EDP: banner_text -->
<!-- BDP: ed_f -->
<tr>
        <td>Редактируемое поле 1</td>
        <td><textarea  name="ed1" class="mceEditor" rows=2 style="width: 98%;">{ED1}</textarea></td>
    </tr>
        <tr>
        <td>Редактируемое поле 2</td>
        <td><textarea name="ed2" class="mceEditor" rows=2 style="width: 98%;">{ED2}</textarea></td>
    </tr>
<!-- EDP: ed_f -->
<!-- BDP: order_disc_edit -->
<tr>
    <td colspan="2">&nbsp;</td>
</tr>
<tr>
    <td>Индекс:</td><td><input type="text" name="index" value="{ED_INDEX}"></td>
</tr>
<tr>
    <td>Город:</td><td><input type="text" name="city" value="{ED_CITY}"></td>
</tr>
<tr>
    <td>Адрес:</td><td><input type="text" name="adr" value="{ED_ADR}"></td>
</tr>
<tr>
    <td>ФИО получателя:</td><td><input type="text" name="fio" value="{ED_FIO}"></td>
</tr>
<tr>
    <td>Паспортные данные:</td><td><input type="text" name="passport" value="{ED_PASSPORT}"></td>
</tr>
<tr>
    <td>Телефон:</td><td><input type="text" name="phone" value="{ED_PHONE}"></td>
</tr>
<tr>
    <td>Дополнительная информация:</td><td><textarea name="add_info">{ED_INFO}</textarea></td>
</tr>
<!-- EDP: order_disc_edit -->

<!-- BDP: end -->
<tr>
     <td colspan=2>&nbsp; <input type="hidden" name="HTTP_REFERER" value="{REFERER}"></td>
</tr>
<tr>
     <td colspan=2><br><br><center><input class="frm_bsubm" type="submit"></center></td>
</tr>
</table>
</form>
<!-- EDP: end -->
