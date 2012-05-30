<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>{TITLE}</title>
<meta name="keywords" content="{KEYWORDS}">
<meta name="description" content="{DESCRIPTION}">
<meta http-equiv=Content-Type content="text/html; charset=windows-1251">
<meta name="document-state" content="dynamic">
<meta name="revisit" content="7 days">
<meta name="revisit-after" content="7 days">
<meta name="Resourse-type" content="document">
<meta name="Robots" content="index,follow">
<meta name="Rating" content="general">
<meta name="Distribution" content="global">
<meta name="Classification" content="">
<meta name="Category" content="">
<meta http-equiv="Pragma" content="token">
<meta http-equiv="Cache-Control" content="token">
<meta name="Copyright" content="2010 deluxe.dp.ua">
<link rel="stylesheet" type="text/css" href="/css/style.css" /> 
<!--[if lte IE 7]><link rel="stylesheet" href="/css/ie.css" type="text/css" /><![endif]-->
<link rel="shortcut icon" href="http://{SITE0}/favicon.ico" type='image/x-icon' />
<script type='text/javascript' src='/js/function.js'></SCRIPT>
<style>
    .wraper{width:1000px; margin: 0 auto;}
    .submenu{margin-top: 0px;}

</style>



<script type="text/javascript" src="/js/modal_window/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/myDataAjax.js"></script>
<script type="text/javascript" src="/js/live_search/jquery.autocomplete.js"></script>




</head>
<body>
 
<div id="boxes_ed">
<div id="dialog" class="window_ed">

<div class="mw_data_top"></div>
<div class="mw_cl">
    <a href="#" class="close close2"><img src="/img/modal/close.jpg"></a>
</div>
<div class="mw_data" id="mw_data_id"></div>
<div class="mw_data_b"></div>
</div>
</div>
<div id="mask"></div>    

    
<div class="wraper">
 <div class="header">
  <a class="logo" href="/" title="{LOGO_ALT}"><img src="/img/logo.jpg" width="208" height="19" alt="{LOGO_ALT}" title="{LOGO_ALT}" /></a>
  <a class="logo2" href="/" title="{LOGO_ALT}"><img src="/img/logo2.jpg" width="75" height="72" alt="{LOGO_ALT}" title="{LOGO_ALT}" /></a>
  <p class="top_text">Всего зарегистрировано: {USERS_COUNT}</p>
  <!--<ul class="pic_menu">
   <li class="active"><a class="home" href="/" title="{HOME_ALT}">&nbsp;</a></li>
   <li><a href="#" title="{FAV_ALT}" class="sitemap">&nbsp;</a></li>
   <li><a href="/feedback" title="{FEED_ALT}" class="email">&nbsp;</a></li>
  </ul>-->
  <div class="mail_upline"><a href="/feedback/">Есть вопрос? Пишите</a><a href="/feedback/"><img src="/img/mail_upline.jpg"></a></div>
  <div class="login_block">
  {LOGIN_BLOCK}
  </div>
 </div>
 <div class="submenu">
  <ul>
    <!-- BDP: menu_item -->
    <li><a href="{MENU_ITEM_HREF}" title="{MENU_ITEM_HEADER}" {MENU_ITEM_ACTIVE}>{MENU_ITEM_HEADER}</a></li>
    <!-- EDP: menu_item -->
  </ul>
  <div class="clear"></div>
 </div>
 <div class="left_col">
  <ul>
   <!-- BDP: user_main_menu -->
   <li><h3>Личный кабинет</h3>
    <ul>
     <li {ACTIVE_S}><a href="/events/">Семинары</a></li>
     <li {ACTIVE_D}><a href="/disc">Диски</a></li>
     <!-- BDP: user_main_menu_partners -->
     <li {ACTIVE_P}><a href="/partners/">Партнеры</a></li>
     <!-- EDP: user_main_menu_partners -->
     <li {ACTIVE_DOC}><a href="/documents">Документы</a></li>
     <!--<li class="bg"><a href="/disc">Диски</a></li>-->
     <!--<li><a href="#">Партнеры</a></li>-->
    </ul>
   </li>
   <li><h3>История покупок</h3>
    <ul>
     <li {ACTIVE_T}><a href="/purchases/">Билеты</a></li>
     <li {ACTIVE_DISC}><a href="/mydiscs/">Диски</a></li>
    </ul>
   </li>
   <li><h3>Настройки</h3>
    <ul>
     <li {ACTIVE_MS}><a href="/mysettings/">Мои данные</a></li>
    </ul>
   </li>
   <!-- EDP: user_main_menu -->
   <!-- BDP: admin_main_menu -->
   <li class="exit"><a href="/admin/main">Админ-панель</a></li>
   <!-- EDP: admin_main_menu -->
   <li class="exit"><a href="/logout">Выход</a></li>
  </ul>

    <div class="ef">{ED1}</div>
    <div class="ef">{ED2}</div>

 </div>
 <div class="container right_col">
 <h1>{HEADER}</h1>
  {CONTENT}
 </div>
 <div class="footer2f">&nbsp;</div>
 <div class="footer">
  <div class="left">
      <img class="logo" src="/img/fln.gif" alt="{LOGO_ALT}" title="{LOGO_ALT}" />
   <p>&nbsp;</p>
  </div>
  <div class="center">
   <p>{SLG_BOTTOM}</p>
   <a href="/leaders" class="main_link_a">Информация о лидерах Альянса Бриллиантов</a>
  </div>
  <div class="right">
   <!--<p><a href="http://www.deluxe.dp.ua/" title="Создание сайта студия веб дизайна de LUXE">создание сайта</a><br /><a href="http://www.deluxe.dp.ua/" title="Создание сайта студия веб дизайна de LUXE"><img src="/img/deluxe.gif" width="80" height="18" alt="Создание сайта студия веб дизайна de LUXE" title="Создание сайта студия веб дизайна de LUXE" /></a></p>-->
   <p><a href="http://www.deluxe.dp.ua/" title="Создание сайта студия веб дизайна de LUXE">создание сайта</a><br /><a href="http://www.deluxe.dp.ua/" title="Создание сайта студия веб дизайна de LUXE"><img src="/img/deluxe.gif" width="80" height="18" alt="Создание сайта студия веб дизайна de LUXE" title="Создание сайта студия веб дизайна de LUXE" /></a></p>
  </div>
  <div class="clear"></div>
 </div>
</div>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-15559928-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>
</html>