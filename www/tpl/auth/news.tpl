


<!-- BDP: news -->
{PAGES_TOP}
<div class="rzd_list">
    <ul>
        <!-- BDP: news_item -->
        <li><span>{NEWS_ADM}<strong>{NEWS_DATE}</strong> <a href="/news/{NEWS_ADRESS}">{NEWS_NAME}</a></span>{NEWS_PIC}{NEWS_PREVIEW}<strong><a href="/news/{NEWS_ADRESS}">Подробнее »</a></strong><div class="clear"></div></li>
        <!-- EDP: news_item -->
    </ul>
</div>
{PAGES_BOTTOM}
<!-- EDP: news -->

<!-- BDP: news_detail -->
<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<div class="news_full">
   {NEWS_BODY}
   <br>
   
   <a href="#" id="clickme" style="cursor: pointer;">
  Поделиться с партнером
</a>

<div id="result"></div>
  </div>

{NEWS_SEND_PART}
<script>
$('#clickme').click(function(){
  
    $('#result').html("<br /><form action='' method='post'> E-mail: <input type='text' style='width:200px;' name='mail_part' /> <input type='submit' class='new_b' style='width:100px; height:23px;' value='Отправить' name='go_send_part' /> </form>");
  
});
</script>
<!-- EDP: news_detail -->