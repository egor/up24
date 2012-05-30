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


<script type="text/javascript" src="/js/modal_window/jquery-1.3.2.min.js"></script>
<script>

$(document).ready(function() {	
		//Cancel the link behavior
		//e.preventDefault();
		//Get the A tag
		var id = '#dialog';
	
		//Get the screen height and width
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
	
		//Set heigth and width to mask to fill up the whole screen
		$('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//transition effect		
		$('#mask').fadeIn(500);	
		$('#mask').fadeTo("slow",0.4);	
	
		//Get the window height and width
		var winH = $(window).height();
		var winW = $(window).width();
              
		//Set the popup window to center
		$(id).css('top',  winH/2-$(id).height()/2);
		$(id).css('left', winW/2-$(id).width()/2);
	
		//transition effect
		$(id).fadeIn(2000); 
	
	
	//if close button is clicked
	$('.window .close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		$('#mask, .window').hide();
	});		
	
	//if mask is clicked
	$('#mask').click(function () {
		$(this).hide();
		$('.window').hide();
	});			
	
});

</script>
<style>
.news_full{
    width:1000px; margin: 0 auto; float: none;
    }
    .main_block2 h1{
        width:1000px; margin: 0 auto; float: none;
        padding-left: 55px;
        padding-top: 20px;
    }
    .container .main_block2 {
    margin-top: -17px;
    }
</style>
</head>
<body>
    
{WELC_ERR2}


    
<div class="wraper">
    <div class="header2">
 <div class="header">
  <a class="logo" href="/" title="{LOGO_ALT}"><img src="/img/logo.jpg" width="208" height="19" alt="{LOGO_ALT}" title="{LOGO_ALT}" /></a>
  <a class="logo2" href="/" title="{LOGO_ALT}"><img src="/img/logo2.jpg" width="75" height="72" alt="{LOGO_ALT}" title="{LOGO_ALT}" /></a>
  <p class="top_text">Всего зарегистрировано: {USERS_COUNT}</p>
  <div class="mail_upline"><a href="/feedback/">Есть вопрос? Пишите</a><a href="/feedback/"><img src="/img/mail_upline.jpg"></a></div>
  {LOGIN_BLOCK}
 </div>
       </div>

 <div class="container">
 <div class="main_block2">   
   <h1>{HEADER}</h1> 
  {CONTENT}
 </div>
     
 </div>
    <br class="clear">
    <div class="footer2">
 <div class="footer">
  <div class="left">
      <img class="logo" src="/img/fln.gif" alt="{LOGO_ALT}" title="{LOGO_ALT}" />
   <p>&nbsp;</p>
   <!--<p>{BOTTOM_ADRES}</p>-->
        
  </div>

 
  <div class="center">
   <p>{SLG_BOTTOM}</p>
   <a href="/leaders" class="main_link_a">Информация о лидерах Альянса Бриллиантов</a>
  </div>
  <div class="right">
   <p><a href="http://www.deluxe.dp.ua/" title="Создание сайта студия веб дизайна de LUXE">создание сайта</a><br /><a href="http://www.deluxe.dp.ua/" title="Создание сайта студия веб дизайна de LUXE"><img src="/img/deluxe.gif" width="80" height="18" alt="Создание сайта студия веб дизайна de LUXE" title="Создание сайта студия веб дизайна de LUXE" /></a></p>
  </div>
  <div class="clear"></div>
 </div>
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