<!-- BDP: country -->

<br clear="all">
<table class="country_tabl">
    <tr>
        <td {CLASS_ACTIVE_RU} ><a {CLASS_ACTIVE_RU} href="/admin/events?country=ru"><div class="t_l_b">Россия</div></a></td>
        <td {CLASS_ACTIVE_UA} ><a {CLASS_ACTIVE_UA} href="/admin/events?country=ua"><div class="t_l_b">Украина</div></a></td>
        <td class="select_ev">
            <form action="" method="get">
                Выберите тип семинара: <select name="filter_type" onchange="this.form.submit();">{FILTER_TYPE_OPTIONS}</select>
            </form>
        </td>
    </tr>
    <tr><td colspan="3" class="country_line"></td></tr>
</table>
<!-- EDP: country -->

<!-- BDP: filter -->
<link rel="stylesheet" type="text/css" media="all" href="/css/calendar-blue.css" title="winter" />
<script type="text/javascript" src="/js/calendar.js"></script>
<script type="text/javascript" src="/js/calendar-ru.js"></script>
<script type="text/javascript" src="/js/calendar-setup.js"></script>


<!--
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "filter_start",     // id of the input field
        //ifFormat       :    "%Y-%m-%d",      // format of the input field
        ifFormat       :    "%d.%m.%Y",      // format of the input field
        button         :    "trigger1",  // trigger for the calendar (button ID)
        align          :    "B1",           // alignment (defaults to "Bl")
        singleClick    :    true
    });
    
    Calendar.setup({
        inputField     :    "filter_end",     // id of the input field
        //ifFormat       :    "%Y-%m-%d",      // format of the input field
        ifFormat       :    "%d.%m.%Y",      // format of the input field
        button         :    "trigger2",  // trigger for the calendar (button ID)
        align          :    "B1",           // alignment (defaults to "Bl")
        singleClick    :    true
    });
</script>-->
<!-- EDP: filter -->

<!-- BDP: list -->
<div class="catalog_list">
    <ul>
    	<!-- BDP: list_row -->
        <li>
            <span>{EVENT_NAME}</span>
            {EVENT_PIC}
            
            <div class="desc">
                <p>Дата:<strong>{EVENT_DATE}{EVENT_DATE2}</strong></p>
                <p>Город:<strong>{EVENT_CITY}</strong></p>
                <p>Адрес:<strong>{EVENT_ADRES}</strong></p>
                {EVENT_TIME}
                <p>Вид мероприятия:<strong>{EVENT_TYPE}</strong></p>
            </div>
            
            <div class="clear"></div>
            
            {EVENT_PREVIEW}<br /><strong><a href="/admin/viewevent/{EVENT_ID}">Подробнее »</a></strong>
            
            <div class="clear"></div>
        </li>
        <!-- EDP: list_row -->
    </ul>
</div>
<!-- EDP: list -->

<!-- BDP: detail -->
<div class="catalog_list catalog_info">
    <ul class="noborder">
        <li>
            {EVENT_PIC}
            <div class="desc">
                <p>Дата:<strong>{EVENT_DATE}{EVENT_DATE2}</strong></p>
                <p>Город:<strong>{EVENT_CITY}</strong></p>
                <p>Адрес:<strong>{EVENT_ADRES}</strong></p>
                {EVENT_TIME}
                <p>Вид мероприятия:<strong>{EVENT_TYPE}</strong></p>
                <div class="events_t_b_main">
                <div class="events_t_b">
                <form action="/admin/eventexport/{EVENT_ID}" method="post">
                    <input type="submit" value="Экспорт заказов" class="button long" />
                </form>
                    </div>
                    <div class="events_t_b">
                <form action="/admin/viewevent/{EVENT_ID}" method="post">
                    <input type="submit" name="{SUBMIT_NAME}" value="{SUBMIT_VALUE}" class="button long" />
                </form>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </li>
    </ul>

    {EVENT_PREVIEW}

    <h3>Подробное описание семинара</h3>

    {EVENT_BODY}

    <h3>{HALL_PIC_H3}</h3>

    {HALL_PIC}

    {HALL_PREVIEW}
    <table>
	<form action="/admin/sectorscanner" method="post" id="sectorscanner">
    <input type="hidden" name="event" value="{EVENT_ID}" />
        <tr>
            <th>Сектор</th>
            <th class="center">Мест всего</th>
            <th class="center">Билетов осталось</th>
            <th class="center">&nbsp;</th>
            <th class="right">Экспорт CSV</th>
        </tr>
        <!-- BDP: sectors_row -->
        <tr>
            <td class="left">{SECTOR_ROW_NAME}</td>
            <td class="center">{SECTOR_ROW_COUNT}</td>
            <td class="center"><strong>{SECTOR_ROW_AVAILABLE}</strong></td>
            <td class="center"><input type="checkbox" name="export[{SECTOR_ROW_ID}]"></td>
            <td class="right">
                <!--<form action="/admin/exportscanner" method="post">
                    <input type="hidden" name="event" value="{EVENT_ID}" />
                    <input type="hidden" name="sector" value="{SECTOR_ROW_ID}" />
                    <input type="submit" value="export" class="button" />
                </form>-->
            </td>
        </tr>
        <!-- EDP: sectors_row -->
        <tr>
            <td colspan="5" class="right">
                <input type="button" value="export" class="button" onclick="document.getElementById('sectorscanner').submit();" />
            </td>
        </tr>
        
    </form>
    </table>
</div>
<!-- EDP: detail -->