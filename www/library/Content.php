<?php

require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

class Content extends Main_Abstract implements Main_Interface
{
    
    protected $_denied = array(
        'news',
        'events',
        'viewevent',
        'buyticket',
        'purchases',
        'ticketprint',
        'buysucess',
        'disc',
        'viewDisc',
        'discsucess',
        'mydiscs',
    	'partners'
    );
    
    public function factory()
    {
        if (!$this->_isAuth() && in_array($this->url[0], $this->_denied)) {
            return false;
        }
        
        return true;
    }
    
    public function main()
    {
        
        $href = end($this->url);
        
        $validate = new Zend_Validate_Regex(array('pattern' => '/^[A-Za-z0-9\-\_]+$/'));
        if (!$validate->isValid($href)) {
            // Что непонятное в ссылке
            return false;
        }
        
        $system = $this->db->fetchRow("SELECT * FROM `system` WHERE `href` = '$href'");
        
        if (!$system) {
            // На данный момент страницы сайта отсутствуют
            return $this->page($href);
        }
        
        $this->tpl->define_dynamic('main', "main.tpl");
        
        $this->setMetaTags($system['header']);
        $this->setWay(stripslashes($system['header']));
        
        $this->tpl->assign(
            array(
                'HEADER' => stripslashes($system['header']),
                'BODY' => stripslashes($system['body'])
            )
        );
        
        $this->tpl->parse('CONTENT', '.main');
        
        return true;
    }
    
    private function page($href = null)
    {
        
        if (null === $href) {
            return false;
        }
        
        $page = $this->db->fetchRow("SELECT * FROM `page` WHERE `href` = '$href'");
        $this->tpl->assign(
                array(

                    'ED1' => ($page['ed1']!=''?$page['ed1']:''),
                    'ED2' => ($page['ed2']!=''?$page['ed2']:'')
                )
            );
        if (!$page || $page['type'] == 'link') {
            return false;
        }
        
        $page['header'] = $this->getAdminEdit('page', $page['id']).$page['header'];
        $this->setMetaTags($page);
        
        $content = '<div class="news_full">'.stripslashes($page['body']).'</div>';
        if ($page['type'] == 'section') {
            $content .= $this->getAdminAdd('page', $page['id']);
        }
        
        $this->tpl->assign('CONTENT', $content);
        
        if ($page['type'] == 'section') {
            $this->loadSubPage($page);
        }
        
        
        return true;
    }
    
    private function loadSubPage($page = null)
    {
        if (null === $page) {
            return false;
        }
        
        $id = $page['id'];
        $url = $this->getFullUri($page).$page['href'].'/';
        
        $start = 0;
        $navbar = $navTop = $navBot = '';
        $page = 1;
        
        $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `page` WHERE `level` = '$id'".($this->_isAdmin() ? "" : " AND `visibility` = '1'"));
        
        $num_pages = $this->settings['num_pages'];
            
        if ($count > 0) {
            if ($count > $num_pages) {
                if (isset($this->getParam['page'])) {
                    $page = (int) $this->getParam['page'];
                    $start = $num_pages * $page - $num_pages;
                    
                    if ($start > $count) {
                        $start = 0;
                    }
                }
                
                $navbar = $this->loadPaginator((int) ceil($count/$num_pages), (int) $page, $url);
            }
        }
        
        $items = $this->db->fetchAll("SELECT * FROM `page` WHERE `level` = '$id' ORDER BY `position` LIMIT $start, $num_pages");
        
        if (!$items) {
            return true;
        }
        
        $this->tpl->define_dynamic('_pages', "pages.tpl");
        $this->tpl->define_dynamic('pages', "_pages");
        $this->tpl->define_dynamic('pages_row', "pages");
        
        foreach ($items as $item) {
            $pic = '';
            if($item['pic'] != '' && file_exists('./images/pages/'.$item['pic'])){
                $pic='<img src="/images/pages/'.$item['pic'].'">';
            }
            $this->tpl->assign(
                array(
                    'PAGE_ADM' => $this->getAdminEdit('page', $item['id']),
                    'PAGE_NAME' => $item['header'],
                    'PAGE_PREVIEW' => stripslashes($item['preview']),
                    'PAGE_ADRESS' => $url.$item['href'],
                    'PAGE_PIC' => $pic,

                )
            );
            
            $this->tpl->parse('PAGES_ROW', '.pages_row');
        }
        
        $this->tpl->assign(
            array(

                'PAGES_TOP' => $navbar,
                'PAGES_BOTTOM' => $navbar
            )
        );
        
        $this->tpl->parse('CONTENT', '.pages');
    }
    
    public function index()
    {
        
        global $money, $site;
        $regErrE = '';
        $this->tpl->define_dynamic('index', "index.tpl");
        $this->tpl->define_dynamic('registration_false', 'index');
        
        $this->tpl->assign('REGISTRATION_ERROR', '');
        
        $index = $this->db->fetchRow("SELECT `title`, `keywords`, `description`, `header`, `body` FROM `system` WHERE `href` = 'mainpage'");
        
        if (!$index) {
            return $this->error404();
        }
        
        $this->setMetaTags($index);
        
        $this->tpl->assign(
            array(
                'HEADER' => stripslashes($index['header']),
                'BODY' => stripslashes($index['body']),      
            )
        );
        
        $name       = $this->getVar('name', '', $_POST);
        $surname    = $this->getVar('surname', '', $_POST);
        $number     = $this->getVar('number', '', $_POST);
        $email      = $this->getVar('email', '', $_POST);
        $phone      = $this->getVar('phone', $site[7][$_SESSION['countryDName']], $_POST);
        $diamond    = $this->getVar('diamond', '', $_POST);
        $emerald    = $this->getVar('emerald', '', $_POST);
        $platinum   = $this->getVar('platinum', '', $_POST);
        $code       = $this->getVar('code', '', $_POST);
        $captcha    = $this->getVar('code_confirm', null, $_SESSION);
        
        $error = false;
        $all_err=0;
        if (!empty($_POST) && isset($_POST['registration']) && null === $this->auth) {
            //if (!$name || !$surname || !$diamond || !$emerald || !$platinum || !$code || !$captcha) {
            //    $error = true;
            //}
            if (!$name || !$surname || !$diamond || !$emerald || !$platinum){
                $regErrE .= '<p>Необходимо заполнить все поля</p>';
                $all_err=1;
                $this->tpl->assign('REGISTRATION_ERROR', '<p>Необходимо заполнить все поля</p>');
                $error = true;
            }
            if (!$email) {
                $error = true;
            } else {
                //$res = $this->db->fetchOne("SELECT ``");
                $validate = new Zend_Validate_EmailAddress();
                if (!$validate->isValid($email) and $all_err==0) {
                    $regErrE .= '<p>E-mail введен некорректно</p>';
                    $this->tpl->assign('REGISTRATION_ERROR', '<p>E-mail введен некорректно</p>');
                    $error = true;
                }
                
            }
            
            
            if ((!$number || !ctype_digit($number)) and $all_err==0) {
                $regErrE .= '<p>Проверьте номер Amway</p>';
                $this->tpl->assign('REGISTRATION_ERROR', '<p>Проверьте номер Amway</p>');
                $error = true;
            }
            if ((!$phone || $phone=='+3' || $phone=='+7')  and $all_err==0) {
                $regErrE .= '<p>Проверьте номер телефона</p>';
                $this->tpl->assign('REGISTRATION_ERROR', '<p>Проверьте номер телефона</p>');
                $error = true;
                
            }
            //echo $captcha.' - '.$code;// die;
            
            
            $user = null;
            
            if (!$error) {
                
                
                $sEmail = $this->db->fetchAll("SELECT * FROM `users` WHERE `email` = '$email'");
                if ($sEmail) {
                    $regErrE .= '<p>Такой e-mail уже используется.<br>Возможно вы <a href="/remind" class="link-err">забыли пароль?</a></br> Воспользуйтесь функцией <a href="/remind" class="link-err">восстановления пароля</a><br> или обратитесь в <a href="/feedback/" class="link-err">службу поддержки</a></p>';
                    $this->tpl->assign('REGISTRATION_ERROR', '<p>Такой e-mail уже используется. Возможно вы забыли пароль? Воспользуйтесь функцией восстановления пароля <br>или обратитесь в службу поддержки.</p>');
                    $error = true;
                    
                }
                $sNAmway = $this->db->fetchAll("SELECT * FROM `users` WHERE `number` = '$number'");
                if ($sNAmway) {
                    $regErrE .= '<p>Такой номер Amway уже зарегистрирован</p>';
                    $this->tpl->assign('REGISTRATION_ERROR', '<p>Такой номер Amway уже зарегистрирован</p>');
                    $error = true;
                    
                }
                
                /*
                $user = $this->db->fetchAll("SELECT * FROM `users` WHERE `email` = '$email' OR `number` = '$number'");
                if ($user) {
                        $regErrE .= '<p>Неверная комбинация E-mail и номера Amway</p>';
                        $this->tpl->assign('REGISTRATION_ERROR', '<p>Неверная комбинация E-Mail и номера Amway</p>');
                        $error = true;
                }
                 */
            }
            if (!$error) {
                if ($captcha !== $code) {
                    $regErrE .= '<p>Неверный код подтверждения</p>';
                    $this->tpl->assign('REGISTRATION_ERROR', '<p>Неверный код подтверждения</p>');
                    $error = true;
                    
                }
                //die;
            }
             
             
            $this->tpl->assign(
            array(
                'REGISTRATION_ERROR' => $regErrE                        
            )
        ); 
            //$this->tpl->assign('REGISTRATION_ERROR', $regErrE);
            
            if (!$error && $user) {
                switch ($user[0]['status']) {
                    case 0 :
                        $this->redirect('/banned');
                        break;
                    case 1 :
                        $this->redirect('/remind');
                        break;
                    case 2 :
                        $this->redirect('/process');
                        break;
                    default :
                        break;
                }
                //exit();
            } elseif (!$error && !$user) {            
                $_SESSION['code_confirm'] = null;
                 $p = str_replace('@', '', $email);
                $p = substr($p, 0, 6);
            
                $password = $p.$this->generatePassword();
                
                $data = array(
                    'name'      => $name,
                    'surname'   => $surname,
                    'number'    => $number,
                    'email'     => $email,
                    'phone'     => $this->checkPhone($phone),
                    'diamond'   => $diamond,
                    'emerald'   => $emerald,
                    'platinum'  => $platinum,
                    'date'      => mktime(),
                    'password'  => crypt($password, $this->_salt)
                );
                
                $this->logger->addLogRow('Регистрация нового пользователя', serialize($data));
                
                $this->db->insert('users', $data);
                
                $subject = $this->getVar('register_subject', 'Регистрация на сайте: http://'.$_SERVER['HTTP_HOST'].'/', $this->settings);
                
                $body = $this->getVar('register_message', '<strong>Логин:</strong> %LOGIN%<br /><strong>Пароль:</strong> %PASSWORD%', $this->settings);
                $body = str_replace("%LOGIN%", $email, $body);
                $body = str_replace("%PASSWORD%", $password, $body);
                
                $body = str_replace('\\"', '', $body);
                //$body = stripslashes($body);
                //echo $body; die;
                $mail = new Zend_Mail('Windows-1251');
                //$mail->setFrom('webmaster@upline24.ru', 'Webmaster');
                $mail->setFrom('Upline24@'.$site[0][$_SESSION['countryDName']], 'Upline24');
                
                $mail->setSubject($subject);
    		    $mail->setBodyHtml($body);
    		    		    
    		    $mail->addTo($email, $surname.' '.$name);
		    		    
                $mail->send();
                
                $this->redirect('/sucess');
            }
        }
        
        $this->tpl->assign(
            array(
                'REGISTRATION_NAME'     => ((!isset($name) or $name=='')?'':$name),
                'SCRIPT_CLEAR_NAME'     => ((!isset($name) or $name=='')?'':''),
                'REGISTRATION_SURNAME'  => $surname,
                'REGISTRATION_NUMBER'   => $number,
                'REGISTRATION_EMAIL'    => $email,
                'REGISTRATION_PHONE'    => $phone,
                'REGISTRATION_DIAMOND'  => ((!isset($diamond) or $diamond=='' or $diamond=='Фамилия вышестоящего спонсора')?'Фамилия вышестоящего спонсора':$diamond),
                'SCRIPT_CLEAR_DIAMOND'     => ((!isset($diamond) or $diamond=='')?' onblur="if(this.value==\'\'){this.value=\'Фамилия вышестоящего спонсора\'; $(this).css(\'color\',\'#999\');} " onfocus="if(this.value==\'Фамилия вышестоящего спонсора\'){this.value=\'\';} $(this).css(\'color\',\'#000\');" value="Фамилия вышестоящего спонсора" style="color:#999999;" ':''),
                'REGISTRATION_EMERALD'  => ((!isset($emerald) or $emerald=='')?'Фамилия вышестоящего спонсора':$emerald),
                'SCRIPT_CLEAR_EMERALD'     => ((!isset($emerald) or $emerald=='' or $emerald=='Фамилия вышестоящего спонсора')?' onblur="if(this.value==\'\'){this.value=\'Фамилия вышестоящего спонсора\'; $(this).css(\'color\',\'#999\');} " onfocus="if(this.value==\'Фамилия вышестоящего спонсора\'){this.value=\'\';} $(this).css(\'color\',\'#000\');" value="Фамилия вышестоящего спонсора" style="color:#999999;" ':''),
                'REGISTRATION_PLATINUM' => ((!isset($platinum) or $platinum=='')?'Фамилия вышестоящего спонсора':$platinum),
                'SCRIPT_CLEAR_PLATINUM'     => ((!isset($platinum) or $platinum=='' or $platinum=='Фамилия вышестоящего спонсора')?'  onblur="if(this.value==\'\'){this.value=\'Фамилия вышестоящего спонсора\'; $(this).css(\'color\',\'#999\');} " onfocus="if(this.value==\'Фамилия вышестоящего спонсора\'){this.value=\'\';} $(this).css(\'color\',\'#000\');" value="Фамилия вышестоящего спонсора" style="color:#999999;"  ':''),
                'REGISTRATION_CODE'     => '',
                'HEADER'=>''
            )
        );
        //echo $regErrE; die;
        if (!$error) {
            $this->tpl->parse('REGISTRATION_FALSE', 'null');
        } else {
            $this->tpl->parse('REGISTRATION_FALSE', '.registration_false');
        }
        
        $this->tpl->parse('CONTENT', '.index');
        $this->edPageSys('mainpage');
        return true;
    }
    
    public function feedback()
    {
		global $site;
        $this->tpl->define_dynamic('feedback', "feedback.tpl");
        $this->tpl->define_dynamic('feedback_false', "feedback");
        
        $admin_email = $this->getVar('feedback_email', 'vova@deluxe.dp.ua', $this->settings);
        
        $name       = $this->getVar('name', '', $_POST);
        $email      = $this->getVar('email', '', $_POST);
        $phone      = $this->getVar('phone', '', $_POST);
        $message    = $this->getVar('message', '', $_POST);
        $code       = $this->getVar('code', '', $_POST);
        $captcha    = $this->getVar('code_confirm', null, $_SESSION);
		
        $error = false;
        $this->tpl->assign('FEEDBACK_ERROR', '');
        
		if (!empty($_POST) && isset($_POST['feedback'])) {
            if (!$name || !$message || !$code || !$captcha) {
                $error = true;
            }
            
            if (!$email) {
                $error = true;
            } else {
                $validate = new Zend_Validate_EmailAddress();
                if (!$validate->isValid($email)) {
                    $this->tpl->assign('FEEDBACK_ERROR', '<p>E-mail введен некорректно</p>');
                    $error = true;
                }
            }
            
            if (!$error) {
                if ($captcha !== $code) {
                    $this->tpl->assign('FEEDBACK_ERROR', '<p>Неверный код подтверждения</p>');
                    $error = true;
                }
            }
		}
		
		if (!empty($_POST) && isset($_POST['feedback']) && !$error) {
            $_SESSION['code_confirm'] = null;
            
            $data = array(
                'name'      => $name,
                'email'     => $email,
                'phone'     => $phone,
                'message'   => $message,
                'date'      => mktime()
            );
            
            if ($this->_isAuth()) {
            	$this->logger->addLogRow('Новое сообщение формы обратной связи', serialize($data));
			}
            
            $this->db->insert('feedback', $data);
            
            $subject = $this->getVar('feedback_subject', 'Письмо с сайта: http://'.$_SERVER['HTTP_HOST'].'/', $this->settings);
            
            $body = $this->getVar('feedback_message', '<strong>Ф.И.О.:</strong> %NAME%<br /><strong>E-Mail:</strong> %EMAIL%<br /><strong>Номер телефона:</strong> %PHONE%<br /><strong>Сообщение:</strong><br />%MESSAGE%', $this->settings);
            $body = str_replace("%NAME%", $name, $body);
            $body = str_replace("%EMAIL%", $email, $body);
            $body = str_replace("%PHONE%", $phone, $body);
            $body = str_replace("%MESSAGE%", strip_tags($message), $body);
            
            $mail = new Zend_Mail('Windows-1251');
            //$mail->setFrom('webmaster@upline24.ru', 'Webmaster');
            $mail->setFrom('Upline24@'.$site[0][$_SESSION['countryDName']], 'Upline24');
            
            $mail->setSubject($subject);
		    $mail->setBodyHtml($body);
		    		    
		    $emails = explode(',', $admin_email);
		    foreach ($emails as $item) {
		        $mail->addTo(trim($item), $item);
		    }
		    		    
		    $mail->send();
            
            $this->redirect('/message-sent');
        }
		
		if ($this->_err) {
		    $this->viewErr();
		} 
		
		$this->tpl->assign(
			array(
				'FEED_NAME'     => $name,
				'FEED_EMAIL'    => $email,
				'FEED_PHONE'    => $phone,
				'FEED_MESSAGE'  => $message,
				'FEED_CODE'     => ''
			)
		);
		
		if (!$error) {
            $this->tpl->parse('FEEDBACK_FALSE', 'null');
        } else {
            $this->tpl->parse('FEEDBACK_FALSE', '.feedback_false');
        }
		
		$this->tpl->parse('CONTENT', '.feedback');
		
		return true;
    }
    
    public function enter()
    {
        
        $this->tpl->define_dynamic('auth', "auth.tpl");
        $this->tpl->define_dynamic('auth_page', 'auth');
        
        $error = '';
        
        if (!empty($_POST) && isset($_POST['auth_page'])) {
            $email = $this->getVar('email', null, $_POST);
            $password = $this->getVar('password', null, $_POST);
            
            $_POST = null;
            
            if ('' !== $email && '' !== $password) {
                $validate = new Zend_Validate_EmailAddress();
                if ($validate->isValid($email)) {
                    $authAdapter = new Zend_Auth_Adapter_DbTable($this->db);
                    $authAdapter->setTableName('users');
        		    $authAdapter->setIdentityColumn('email');
        		    $authAdapter->setCredentialColumn('password');
        		    
        		    $authAdapter->setIdentity($email);
        		    $authAdapter->setCredential(crypt($password, $this->_salt));
        		    
        		    $auth = Zend_Auth::getInstance();
        		    $result = $auth->authenticate($authAdapter);
        		    
        		    if ($result->isValid()) {
        		        $data = $authAdapter->getResultRowObject(null, 'password');
        		        $auth->getStorage()->write($data);
            		        if (!empty($_SESSION['user_go_to_news'])){
                                    $this->redirect($_SESSION['user_go_to_news']);
                                    $_SESSION['user_go_to_news']='';
                                    die;
                                }
                                else
                                    $this->redirect('/');
        		    } else {
        		        $error .= '<p>Неверный E-mail/Пароль</p>';
        		    }
                } else {
                    $error .= '<p>E-mail введен некорректно</p>';
                }
            } else {
                $error .= '<p>Необходимо заполнить все поля</p>';
            }
        }
        
        $this->tpl->assign('AUTH_PAGE_FALSE', $error);
        $this->tpl->parse('CONTENT', 'auth_page');
        
        return true;
    }
    
    
    public function news($index = false)
    {
        $this->edPageMeta('news');
        $this->tpl->define_dynamic('_news', 'news.tpl');
        $this->tpl->define_dynamic('news', '_news');
        $this->tpl->define_dynamic('news_item', 'news');
        $this->tpl->define_dynamic('news_detail', '_news');
        
        if (!isset($this->url[1])) {
            $start = 0;
            $navbar = $navTop = $navBot = '';
            $page = 1;
            
            $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `news`".($this->_isAdmin() ? "" : " WHERE `visibility` = '1'"));
            
            $num_news = $this->settings['num_news'];
                
            if ($count > 0) {
                if ($count > $num_news) {
                    if (isset($this->getParam['page'])) {
                        $page = (int) $this->getParam['page'];
                        $start = $num_news * $page - $num_news;
                        
                        if ($start > $count) {
                            $start = 0;
                        }
                    }
                    
                    $navbar = $this->loadPaginator((int) ceil($count/$num_news), (int) $page, $this->basePath.'news/');
                    
                    /*if ($navbar) {
                        $navTop = '<div class="sep_h"><div><img src="/img/z.gif" alt="" height="1px" width="1px"></div></div>' . $navbar . '<div class="sep_h"><div><img src="/img/z.gif" alt="" height="1px" width="1px"></div></div>';
                        $navBot = $navbar . '<div class="sep_h"><div><img src="/img/z.gif" alt="" height="1px" width="1px"></div></div>';
                    }*/
                }
            } else {
                $this->tpl->assign('CONTENT', $this->getAdminAdd('news').'{EMPTY_SECTION}');
                return true;
            }
            
            $news = $this->db->fetchAll("SELECT * FROM `news`".($this->_isAdmin() ? "" : " WHERE `visibility` = '1'")." ORDER BY `date` DESC LIMIT ".$start.", ".$num_news);
            
            foreach ($news as $item) {
                $pic = '';
                if($item['pic'] != '' && file_exists('./images/news/'.$item['pic'])){
                    $pic = '<a href="/news/'.$item['href'].'" title="'.$item['header'].'"><img src="/images/news/'.$item['pic'].'" width="140" height="105" alt="'.$item['header'].'" title="'.$item['header'].'" /></a>';
                }
                
                
                $this->tpl->assign(
                    array(
                        'NEWS_ADM' => $this->getAdminEdit('news', $item['id']),
                        'NEWS_DATE' => $this->convertDate($item['date']),
                        'NEWS_ADRESS' => $item['href'],
                        'NEWS_NAME' => stripslashes($item['header']),
                        'NEWS_PIC' => $pic,
                        'NEWS_PREVIEW' => stripslashes($item['preview'])
                    )
                );
                
                $this->tpl->parse('NEWS_ITEM', '.news_item');
            }
            
            $this->tpl->assign(
                array(
                    'PAGES_TOP' => $navbar,
                    'PAGES_BOTTOM' => $navbar,
                    'CONTENT' => $this->getAdminAdd('news')
                )
            );
            
            if ($count > 0) {
                $this->tpl->parse('NEWS_INDEX_SEP', 'null');
                $this->tpl->parse('CONTENT', '.news');
            }            
        } elseif (!isset($this->url[2])) {
            $href = end($this->url);
            
            $news = $this->db->fetchRow("SELECT * FROM `news` WHERE `language` = '".$this->lang."' AND `href` = '$href'");
            
            if (!$news) {
                return $this->error404();
            }
            
            $date = $this->convertDate($news['date']);
            
            $this->setWay($date.' | '.$news['name']);
            
            $news['header'] = '<span>'.$date.'  </span>'.$news['header'];
            
            $this->setMetaTags($news);
            
            if (isset($_POST['go_send_part'])){
    /* получатели */
$to=  $this->getVar('mail_part', '', $_POST);; //обратите внимание на запятую


/* тема/subject */
$subject = "Новость ".$_SERVER['HTTP_HOST'];

/* сообщение */
$message = '<html><head><title></title></head><body><p>Мне это понравилось: 
    <a href="http://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'].'">http://'.$_SERVER['HTTP_HOST'].''.$_SERVER['REQUEST_URI'].'</a>
        </p></body></html>';

/* Для отправки HTML-почты вы можете установить шапку Content-type. */
$headers= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=windows-1251\r\n";

/* дополнительные шапки */
$headers .= "From: upline24 <Upline24@".$_SERVER['HTTP_HOST'].">\r\n";
$headers .= "Cc: Upline24@".$_SERVER['HTTP_HOST']."\r\n";
$headers .= "Bcc: Upline24@".$_SERVER['HTTP_HOST']."\r\n";

/* и теперь отправим из */
mail($to, $subject, $message, $headers);               
            }

            $this->tpl->assign(
				array(
					'NEWS_BODY' => stripslashes($news['body']),
                                        'NEWS_SEND_PART' => (isset($_POST['go_send_part'])?'Ваше сообщение отправлено.':''),
                                        'ED1'=>($news['ed1']!=''?$news['ed1']:''),
                                        'ED2'=>($news['ed2']!=''?$news['ed2']:'')
					//'NEWS_ADM' => $this->getAdminEdit('news', $news['id']),
					//'NEWS_DATE' => $date,
					//'NEWS_TITLE' => $news['header'],
					//'PIC' => (is_file(PATH.'img/news/big/'.$news['pic']) ? '<p><img  src="/img/news/big/'.$news['pic'].'" width="150" height="170" alt="'.$news['header'].'" class="float_img" /></p>' :'')
				)
			);
			
			$this->tpl->parse('CONTENT', 'news_detail');
        } else {
            return $this->error404();
        }
        
        return true;
    }
    
    public function events()
    {
        //echo $this->generateScannerCode();
    	//$this->setMetaTags('Семинары');
	    
	    //$this->viewMessage($this->getAdminAdd('events', ''));
	    
	    $filter_city = $this->getVar('filter_city', 'all');
	    $filter_type = $this->getVar('filter_type', 'all');
	    $filter_start = $this->getVar('filter_start', date('d.m.Y'));
	    $filter_end = $this->getVar('filter_end', date('d.m.Y'));
	    
	    if ($filter_start != 'all' && $filter_end != 'all') {
		    $filter_start = explode('.', $filter_start);
		    $filter_end = explode('.', $filter_end);
		    
		    $filter_start = mktime(0, 0, 0, $filter_start[1], $filter_start[0], $filter_start[2]);
		    $filter_end = mktime(23, 59, 59, $filter_end[1], $filter_end[0], $filter_end[2]);
	    }
	    
	    $this->tpl->define_dynamic('events', "events.tpl");
	    $this->tpl->define_dynamic('filter', "events");
	    $this->tpl->define_dynamic('list', "events");
            $this->tpl->define_dynamic('country', "events");
	    $this->tpl->define_dynamic('list_row', "list");
	    
	    $event_types = $this->db->fetchAll("SELECT * FROM `event_types` WHERE `country`='".$_SESSION['countryDName']."' ORDER BY `position`");
	    $city = $this->db->fetchAll("SELECT * FROM `city` ORDER BY `name`");
	    
	    $types = '<option value="all"'.($filter_type == 'all' ? 'selected' : '').'>Все</option>';
	    foreach ($event_types as $row) {
	        $types .= '<option value="'.$row['id'].'"'.($filter_type == $row['id'] ? 'selected' : '').'>'.$row['name'].'</option>';
	    }
	    
	    $cityList = '<option value="all"'.($filter_city == 'all' ? 'selected' : '').'>Все</option>';
	    foreach ($city as $row) {
	        $cityList .= '<option value="'.$row['id'].'"'.($filter_city == $row['id'] ? 'selected' : '').'>'.$row['name'].'</option>';
	    }
	    
	    $this->tpl->assign(
            array(
                'FILTER_TYPE_OPTIONS' => $types,
                'FILTER_CITY_OPTIONS' => $cityList,
                'FILTER_DATE_START' => $this->convertDate($filter_start),
                'FILTER_DATE_END' => $this->convertDate($filter_end)
            )
	    );
            $this->tpl->parse('CONTENT', '.country');
	    $this->tpl->parse('CONTENT', '.filter');
            
	    
	    $select = $this->db->select();
	    $select->from('events', array('num_rows' => 'COUNT(events.id)'));
	    $select->join('hall', 'hall.id = events.hall_id', array());
	    $select->join('city', 'city.id = hall.city_id', array());
	    $select->join('event_types', 'event_types.id = events.type_id', array());
	    
	    $navGet = '?';
	    
	    if ($filter_type != 'all') {
	        $select->where('type_id = ?', array("$filter_type"));
	        $navGet .= 'filter_type='.$filter_type.'&';
	    }
            $select->where('events.country = ?', $_SESSION['countryDName']);
            //страна
            
            //$select->where('`events.country` = "'.$_SESSION['countryDName'].'"');
            
	    /*
	    if ($this->convertDate($filter_start) != $this->convertDate($filter_end) && $filter_end > $filter_start) {
	        $select->where('date >= ?', array("$filter_start"));
	        $select->where('date <= ?', array("$filter_end"));
	        $navGet .= 'filter_start='.$this->convertDate($filter_start).'&filter_end='.$this->convertDate($filter_end).'&';
	    }
	    
	    if ($filter_city != 'all') {
	        $select->where('city_id = ?', array("$filter_city"));
	        $navGet .= 'filter_city='.$filter_city.'&';
	    }
	    if (!$this->_isAdmin()){
                $select->where('`type_id`=\'3\' OR date >= ?', time ());
                
            }
            //echo $select->__toString(); die;
             * 
             */

	    if ($navGet == '?') $navGet = '';
	    if ($navGet != '') $navGet = substr($navGet, 0, -1);
	    
	    $select->order('date DESC');
            //$select->order('date');
	    
	    $sql = $select->__toString();
	    if (!$this->_isAdmin())
                //$select->where('date >'.time ());
                    $select->where('`type_id`=\'3\' OR `type_id`=\'6\'  OR `date` >= ?', time ());

	    $count_rows = $this->db->fetchOne($sql);
	    
	    $start = 0;
        $navbar = $navTop = $navBot = '';
        $page = 1;
        
        $num_rows = 20;
            
        if ($count_rows > 0) {
            if ($count_rows > $num_rows) {
                if (isset($this->getParam['page'])) {
                    $page = (int) $this->getParam['page'];
                    $start = $num_rows * $page - $num_rows;
                    
                    if ($start > $count_rows) {
                        $start = 0;
                    }
                }
                
                $navbar = $this->loadPaginator((int) ceil($count_rows/$num_rows), (int) $page, $this->basePath.'events/'.$navGet);
            }
        }
	    
	    if ($count_rows > 0) {
    	    $select = $this->db->select();
    	    $select->from('events', array('*'));
    	    $select->join('hall', 'hall.id = events.hall_id', array('city_id', 'adres'));
    	    $select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
    	    $select->join('event_types', 'event_types.id = events.type_id', array('type_name' => 'name'));

    	    if ($filter_type != 'all') {
    	        $select->where('type_id = ?', array("$filter_type"));
    	    }
    	    
    	    if ($this->convertDate($filter_start) != $this->convertDate($filter_end) && $filter_end > $filter_start) {
    	        $select->where('date >= ?', array("$filter_start"));
    	        $select->where('date <= ?', array("$filter_end"));
    	    }
    	    
    	    if ($filter_city != 'all') {
    	        $select->where('city_id = ?', array("$filter_city"));
    	    }
   
            if (!$this->_isAdmin()){
                //$select->where('date >= ?', time ());
                $select->where('`type_id`=\'3\' OR `type_id`=\'6\' OR date >= ?', time ());
            }
            $select->where('events.country = ?', $_SESSION['countryDName']);
    	    //$select->order('events.date DESC');
            $select->order('events.date');
    	    $select->limit($num_rows, $start);
    	    //echo $select->__toString();
    	    $events = $this->db->fetchAll($select);
    	    foreach ($events as $row) {
    	        $pic = '<div class="img">&nbsp;</div>';
    	        if ($row['pic'] && file_exists('./images/events/'.$row['pic'])) {
    	            $pic = '<div class="img"><img src="/images/events/'.$row['pic'].'" width="118" height="100" alt="'.$row['name'].'" title="'.$row['name'].'" /></div>';
    	        }
    	        /////////////

//                if ($row['id']!=43){
                $times = $this->convertDate($row['date'], 'H:i');
    	        $this->tpl->assign(
    	           array(
    	               'EVENT_ID' => $row['id'],
    	               'EVENT_NAME' => $row['name'],
    	               'EVENT_DATE' => $this->convertDate($row['date']),
                       'EVENT_DATE2' => ($row['date']>=$row['date2']?'':' - '.$this->convertDate($row['date2'])),
    	               'EVENT_CITY' => $row['city_name'],
    	               'EVENT_ADRES' => $row['adres'],
    	               'EVENT_TIME' => ($times!='00:00'?'<p>Время начала:<strong>'.$times.'</strong></p>':''),
    	               'EVENT_TYPE' => $row['type_name'],
    	               'EVENT_PREVIEW' => stripslashes($row['preview']),
    	               'EVENT_PIC' => $pic
    	           )
    	        );
/*                      if ($_GET['admin']=='a')
            $_SESSION['a']='a';
         if ($row['id']==43 AND $_SESSION['a']==a){
              	        $this->tpl->assign(
    	           array(
    	               'EVENT_ID' => $row['id'],
    	               'EVENT_NAME' => $row['name'],
    	               'EVENT_DATE' => $this->convertDate($row['date']),
                       'EVENT_DATE2' => ($row['date']>=$row['date2']?'':' - '.$this->convertDate($row['date2'])),
    	               'EVENT_CITY' => $row['city_name'],
    	               'EVENT_ADRES' => $row['adres'],
    	               'EVENT_TIME' => $this->convertDate($row['date'], 'H:i'),
    	               'EVENT_TYPE' => $row['type_name'],
    	               'EVENT_PREVIEW' => stripslashes($row['preview']),
    	               'EVENT_PIC' => $pic
    	           )
    	        );  
         }

        */ 
    	        $this->tpl->parse('LIST_ROW', '.list_row');
             //   }
    	    }
    	    
    	    $this->tpl->assign('NAVBAR', $navbar);
    	    $this->tpl->assign(
    	           array(
                       'CLASS_ACTIVE_RU'    =>  ($_SESSION['countryDName']=='ru'?' class="acrive"':' class="noacrive"'),
                       'CLASS_ACTIVE_UA'    =>  ($_SESSION['countryDName']=='ua'?' class="acrive"':' class="noacrive"')

    	           )
    	        );

            
    	    $this->tpl->parse('CONTENT', '.list');
	    } else {
	        $this->viewMessage('Семинаров не найдено');
                $this->tpl->assign(
    	           array(
                       'CLASS_ACTIVE_RU'    =>  ($_SESSION['countryDName']=='ru'?' class="acrive"':' class="noacrive"'),
                       'CLASS_ACTIVE_UA'    =>  ($_SESSION['countryDName']=='ua'?' class="acrive"':' class="noacrive"')

    	           )
    	        );
	    }
	$this->edPageMeta('events');    
    	return true;
    }
    
    public function viewevent() {
        global $site;
        //Обнулим сессию с купленными билетами.
        //Это нас избавит от ошибок
        unset ($_SESSION['userBuy']);
        $id = end($this->url);
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }

        $select = $this->db->select();
        $select->from('events', array('*'));
        $select->join('hall', 'hall.id = events.hall_id', array('city_id', 'adres', 'hall_preview' => 'preview', 'hall_pic' => 'pic', 'hall_name' => 'name'));
        $select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
        $select->join('event_types', 'event_types.id = events.type_id', array('type_name' => 'name', 'cost'));
        $select->where('events.id = ?', array("$id"));

        $event = $this->db->fetchRow($select->__toString());

        if (!$event) {
            return false;
        }

        $this->setMetaTags($event['name']);

        $this->tpl->define_dynamic('events', "events.tpl");
        $this->tpl->define_dynamic('detail', "events");
        $this->tpl->define_dynamic('sectors_row', "detail");
        $this->tpl->define_dynamic('print_sectors', "detail");

        $pic = '';
        if ($event['pic'] && file_exists('./images/events/'.$event['pic'])) {
            $pic = '<div class="img"><img src="/images/events/'.$event['pic'].'" width="118" height="100" alt="'.$event['name'].'" title="'.$event['name'].'" /></div>';
        }

        $hall_pic = '';
	if ($event['hall_pic'] && file_exists('./images/hall/'.$event['hall_pic'])) {
            $hall_pic = '<div class="fl_left">
                        <img src="/images/hall/'.$event['hall_pic'].'" width="249" height="142" alt="'.$event['hall_name'].'" title="'.$event['hall_name'].'" />
                        <p><a href="/images/hall/big/'.$event['hall_pic'].'" title="Увеличить" target="_blanc"><img src="/img/plus.gif" width="12" height="12" alt="Увеличить" title="Увеличить" /></a> <a href="/images/hall/big/'.$event['hall_pic'].'" title="Увеличить" target="_blanc">Увеличить</a></p>
                        </div>';
	}
        if ($id==114 or $id==152) {
            
            $mapG = '<center><iframe width="745" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.ru/maps/ms?msa=0&amp;msid=206070459696107350209.0004b59c39fc3a8b1aff8&amp;ie=UTF8&amp;ll=57.642784,39.875239&amp;spn=26.013929,127.340469&amp;t=m&amp;vpsrc=6&amp;output=embed"></iframe><br /><small>Просмотреть <a href="http://maps.google.ru/maps/ms?msa=0&amp;msid=206070459696107350209.0004b59c39fc3a8b1aff8&amp;ie=UTF8&amp;ll=57.642784,39.875239&amp;spn=26.013929,127.340469&amp;t=m&amp;vpsrc=6&amp;source=embed" style="color:#0000FF;text-align:left">ОМП Diamond Alliance</a> на карте большего размера</small></center>';
        } else { 
            $mapG='';
        }
            $times = $this->convertDate($event['date'], 'H:i');
	    $this->tpl->assign(
	       array(
	           'EVENT_PIC' => $pic,
	           'EVENT_DATE' => $this->convertDate($event['date']),
                   'EVENT_DATE2' => ($event['date']>=$event['date2']+10?'':' - '.$this->convertDate($event['date2'])),
	           'EVENT_CITY' => $event['city_name'],
	           'EVENT_ADRES' => $event['adres'],
	           'EVENT_TIME' => ($times!='00:00'?'<p>Время начала:<strong>'.$times.'</strong></p>':''),//$this->convertDate($event['date'], 'H:i'),
	           'EVENT_TYPE' => $event['type_name'],
	           'EVENT_PREVIEW' => stripslashes($event['preview']),
	           'EVENT_BODY' => stripslashes($event['body']).$mapG,
	           'HALL_PIC' => $hall_pic,
                   'HALL_PIC_H3' => (!empty($hall_pic)?'Описание зала':''),
	           'HALL_PREVIEW' => stripslashes($event['hall_preview']),
                   'ED1'    =>  ($event['ed1']!=''?$event['ed1']:''),
                   'ED2'    =>  ($event['ed2']!=''?$event['ed2']:'')
	       )
	    );
	    
	    $select = $this->db->select();
	    $select->from('sectors', array('*'));
	    $select->joinLeft('sectors_event', 'sectors_event.sector_id = sectors.id AND event_id = "'.$id.'"', array('ticket_id' => 'id', 'reservation', 'event_id'));
	    $select->where('hall_id = ?', array("".$event['hall_id'].""));
	    $select->order('sectors.id ASC');
	    
	    $sectors = $this->db->fetchAll($select);

            
            $buyLoc = $this->db->fetchAll("SELECT `sector_id`, `row`, `location` FROM `tickets` WHERE `event_id`='$id'");
          
                   

                   //echo $event['type_name']; die;
                   //echo "SELECT `cost` FROM `event_types` WHERE `name`='".$event['no_sell_tickets']."' AND `country`='".$_SESSION['countryDName']."'";
                   $eTypeCost = $this->db->fetchRow("SELECT `cost` FROM `event_types` WHERE `name`='".$event['type_name']."' AND `country`='".$_SESSION['countryDName']."'");
                   //echo $eTypeCost['cost'];
                   $_SESSION['event_cost']=$eTypeCost['cost'];
                   
        if ($event['no_sell_tickets']!=1 OR ($this->_isAdmin())){
            $printSchemeRow = $this->db->fetchAll("
                SELECT  
                    t1.id_sector as t1id_sector, 
                    t1.id_hall as t1id_hall,
                    t1.row_name as t1row_name, 
                    t1.first_location as t1first_location, 
                    t1.count_location as t1count_location, 
                    t2.hall_id as t2hall_id, 
                    t2.name as t2name,
                    t2.id as t2id
                FROM `series` as `t1` 
                RIGHT JOIN  `sectors` as `t2` 
                ON `t1`.`id_hall`=`t2`.`hall_id` 
                WHERE `t2`.`hall_id`='".$event['hall_id']."' 
                    AND  `t1`.`id_sector`=t2.id 
                ORDER BY `t2`.`name`, `t1`.`row_name`
            ");
        }
        $lacation = '';
        $oldSectors = '';
        $j=0;
        $this->tpl->assign(array('HIDE_CLASS' => ''));
        if (empty ($printSchemeRow)){
            $this->tpl->assign(array('HIDE_CLASS' => 'class="hide_class"'));
        }
        $this->tpl->assign(array('BTT' => '<div class="buy_t_d">Купить билет</div>'));
        if (empty ($printSchemeRow)){
            $this->tpl->assign(array('BTT' => ''));
        }
        
        if (isset($printSchemeRow)){
            foreach ($printSchemeRow as $value) {
               $j++;
               if ($j==1)
                   $oldSector = $value['t2name'];
                if ($oldSector!=$value['t2name']){
                    $this->tpl->assign(
                        array(
                           'SECTOR_LOCATION' => '<li><a href="#">'.$oldSector.'</a><ul><li>

<div class="sector_ex">
<div class="sector_exs"><a class="free" href="#">1</a>&nbsp;&nbsp;-&nbsp;&nbsp;Место свободно</div>
<div class="sector_exs"><a class="purchased" href="#">1</a>&nbsp;&nbsp;-&nbsp;&nbsp;Место занято</div>
</div>
'.$lacation.'
    
</li></ul></li>',
                        )
                    );
                    $this->tpl->parse('PRINT_SECTORS', '.print_sectors');   
                    $lacation = '';
                    $oldSector=$value['t2name'];
                }               
                $lacation .= '<div class="sector_info"><div class="sector_name">Ряд '.$value['t1row_name'].'</div><div class="sector_loc">';
                for ($loc=0; $loc<$value['t1count_location']; $loc++) {
                    $j++;
                    $checkLocation = $this->db->fetchRow ("SELECT `id` FROM `tickets` WHERE `event_id`='$id' AND `sector_id`='".$value['t2id']."' AND `row`='".$value['t1row_name']."' AND `location`='".($value['t1first_location']+$loc)."' LIMIT 1");
                    if ($checkLocation) {
                        $lacation .= '<a href="#" id="loc'.$j.'" class="purchased" onclick="return false;" class="sector_location">'.($value['t1first_location']+$loc).'</a>';    
                    } else {
                        $lacation .= '<a href="#" id="loc'.$j.'" class="free" onclick="reserveTickets(\''.$value['t2name'].'\', '.$value['t1row_name'].', '.($value['t1first_location']+$loc).', '.$j.', '.$id.', '.$value['t2id'].'); return false;" class="sector_location">'.($value['t1first_location']+$loc).'</a>';
                    }
                }
                $lacation .= '</div></div>';        
            }
        }
        $this->tpl->assign(
            array(
                'SECTOR_LOCATION' => '<li><a href="#">'.(isset($value['t2name'])?$value['t2name']:'').'</a><ul><li>
<div class="sector_ex">
<div class="sector_exs"><a class="free" href="#">1</a>&nbsp;&nbsp;-&nbsp;&nbsp;Место свободно</div>
<div class="sector_exs"><a class="purchased" href="#">1</a>&nbsp;&nbsp;-&nbsp;&nbsp;Место занято</div>
</div>'.$lacation.'</li></ul></li>',
            )       
        );
        $this->tpl->parse('PRINT_SECTORS', '.print_sectors');  

            if ($sectors) {
	    	foreach ($sectors as $row) {
                    
	    		$available = (int) $row['count']-$row['reservation'];
	    		
	    		$this->tpl->assign(
	    			array(
	    				'SECTOR_ROW_NAME' => $row['name'],
	    				'SECTOR_ROW_COUNT' => $row['count'],
	    				'SECTOR_ROW_AVAILABLE' => $available,
	    				'IS_DISABLED' => $available < 1 ? ' disabled' : '',
	    				'EVENT_ID' => $id,
	    				'SECTOR_ID' => $row['id'],
	    				'TICKET_ID' => $row['ticket_id'],
                                        'SECTOR_CLASS' => (($available>0)?' class="sector_vis" ':' class="tool_block" toolDiv="1" '),
	    			)
	    		);
                        $this->tpl->parse('SECTORS_ROW', '.sectors_row');
	    	}
	    } else {
	    	$this->tpl->parse('SECTORS_ROW', 'null');
	    }
	    
                //$this->tpl->parse('SECTORS_ROW', 'null');
	    
            
            
            $this->tpl->parse('CONTENT', '.detail');
	    
	    $hash = new Zend_Session_Namespace('buyHash');
	    $hash->hashCode = sha1($event['id']);
	    
	    return true;
	}
	
	public function purchasestat()
	{
		$id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        if ($this->auth->privillege != 'administrator' && $this->auth->type == 'user') {
        	return false;
        }
        
        return true;
	}


/****************************************************/
    public function buytickets() {
        global $money, $site;
        $ids=0;
        foreach ($_SESSION['userBuy'] as $key) {
            $arr = explode('|', $key);
            //id семинара
            $event_id = $arr[0];
            //id сектора
            $sector_id = $arr[3];
            //номер ряда
            $rowE = $arr[1];
            //номер места
            $locE = $arr[2];
            $event = $this->db->fetchRow("SELECT * FROM `events` WHERE `id` = '$event_id'");
            $country = $arr[4];
            //Цена за билет
            $cost = (float) $this->db->fetchOne("SELECT `cost` FROM `event_types` WHERE `id` = '".$event['type_id']."' AND `country`='$country'");   
            //непонятно зачем
            $lastNumber = 0;
            //страна где проходит семинар
            
            
            $count = count($_SESSION['userBuy']);
            $number = date('ymdHis').$event_id.$sector_id;
            $orderNumber = $this->generateCode();
            $data = array(
                    'event_id'      =>  $event_id,
                    'sector_id'     =>  $sector_id,
                    'user_id'       =>  $this->auth->id,
                    'count'         =>  $count,
                    'cost'          =>  (float) $cost,
                    'date'          =>  mktime(),
                    'number'        =>  $number,
                    'lastNumber'    =>  (int) $lastNumber,
                    'code'          =>  $orderNumber,
                    'row'           =>  $rowE,
                    'location'      =>  $locE,
                    'hall'          =>  0,
                    'ids'           =>  $ids,
                    'country'       =>  $country
            );

            $this->logger->addLogRow('Заказ билетов', serialize($data));

            $this->db->insert("tickets", $data);

            $ticketId = $this->db->lastInsertId();
            if ($ids==0)
                $ids =  $this->db->lastInsertId();
            $code = $this->generateScannerCode();
            $data = array(
                'ticket_id' =>  $ticketId,
                'code'      =>  $code
            );
            $this->db->insert("tickets_code", $data);
        }
        unset ($_SESSION['userBuy']);      
        header ('location: /purchases/'); die;
	return true;
    }
        
        
        
	public function buyticket()
	{
            global $money, $site;
		$hash = new Zend_Session_Namespace('buyHash');
		
		$event_id = $this->getVar('event_id', null, $_POST);
		$sector_id = $this->getVar('sector_id', null, $_POST);
		$reservation = $this->getVar('reservation', null, $_POST);
		
		if (null === $event_id || null === $sector_id || null === $reservation) {
		    //die('1');
			return false;
		}
		
		$event_id = (int) $event_id;
		$sector_id = (int) $sector_id;
		$reservation = (int) $reservation;
		
		if ($event_id < 1 || $sector_id < 1 || $reservation < 1) {
		    //die('2');
			return false;
		}
		
		$event = $this->db->fetchRow("SELECT * FROM `events` WHERE `id` = '$event_id'");
		
		if (!$event) {
		    //die('3');
			return false;
		}
		
		if (!isset($hash->hashCode) || $hash->hashCode !== sha1($event_id)) {
			header('Location: /viewevent/'.$event_id);
			return true;
		}
		
		$sector = $this->db->fetchRow("SELECT * FROM `sectors` WHERE `id` = '$sector_id'");
		
		if (!$sector) {
		    //die('4');
			return false;
		}
		
		$sector_event = $this->db->fetchRow("SELECT * FROM `sectors_event` WHERE `sector_id` = '$sector_id' AND `event_id` = '$event_id'");
		
		if (!$sector_event) {
		    $data = array(
                'sector_id' => $sector_id,
                'event_id' => $event_id
		    );
		    
		    //$this->logger->addLogRow('add_sector_event', serialize($data));
		    
		    $this->db->insert('sectors_event', $data);
		    
		    $sector_event = $this->db->fetchRow("SELECT * FROM `sectors_event` WHERE `sector_id` = '$sector_id' AND `event_id` = '$event_id'");
		    
		    if (!$sector_event) {
		        //die('5');
		        return false;
		    }
		}
		
		if (($sector['count']-$sector_event['reservation']) < $reservation) {
			$limit_tickets = $this->db->fetchRow("SELECT * FROM `system` WHERE `href` = 'ticket-limit'");
			
			$this->setMetaTags($limit_tickets);
			$this->viewMessage(stripslashes($limit_tickets['body']).'<br /><br /><p><a href="/viewevent/'.$event_id.'">Вернуться к семинару</a></p>');
			$this->edPageSys('ticket-limit');
			return true;
		}
		
		$cost = (float) $this->db->fetchOne("SELECT `cost` FROM `event_types` WHERE `id` = '".$event['type_id']."'");
		
		//$this->logger->addLogRow('Обновление количества зарезервированных билетов сектора', serialize($sector_event));
		
		$data = array(
			'reservation' => (int) $sector_event['reservation']+$reservation
		);
		
		$this->db->update('sectors_event', $data, "id = '".$sector_event['id']."'");
		
		$lastNumber = $this->db->fetchOne("SELECT SUM(`count`) FROM `tickets` WHERE `event_id` = '$event_id'");
		
		$number = date('ymdHis').$event_id.$sector_id;
		
		//$code = mt_rand(10000000,99999999999);
		$orderNumber = $this->generateCode();
		
		$data = array(
			'event_id' => $event_id,
			'sector_id' => $sector_id,
			'user_id' => $this->auth->id,
			'count' => $reservation,
			'cost' => (float) $cost,
			'date' => mktime(),
			'number' => $number,
			'lastNumber' => (int) $lastNumber,
			'code' => $orderNumber
		);
		
		$this->logger->addLogRow('Заказ билетов', serialize($data));
		
		$this->db->insert("tickets", $data);
		
		$ticketId = $this->db->lastInsertId();
		
		if ($reservation > 1) {
		    for ($i=0; $i<$reservation; $i++) {
		        $code = $this->generateScannerCode();
		    
    		    $data = array(
                    'ticket_id' => $ticketId,
                    'code' => $code
    		    );
    		    
    		    $this->db->insert("tickets_code", $data);
		    }
		} else {
		    $code = $this->generateScannerCode();
		    
		    $data = array(
                'ticket_id' => $ticketId,
                'code' => $code
		    );
		    
		    $this->db->insert("tickets_code", $data);
		}
		
		$select = $this->db->select();
		$select->from('events', array('name', 'date'));
		$select->join('hall', 'events.hall_id = hall.id', array('adres'));
		$select->join('city', 'hall.city_id = city.id', array('city_name' => 'name'));
		$select->join('event_types', 'events.type_id = event_types.id', array('type_name' => 'name'));
		$select->where('events.id = ?', array($event_id));
		//echo $select->__toString();
		$sendData = $this->db->fetchRow($select);
		
		$subject = $this->getVar('ticketbuy_subject', 'Заказ билетов на сайте: http://'.$_SERVER['HTTP_HOST'].'/', $this->settings);
            
        $body = $this->getVar('ticketbuy_message', '<p>Спасибо, что воспользовались услугами нашего сайта.<br /><br />Данные о заказе:<br />Номер заказа: %NUMBER%<br />Дата семинара: %DATE%<br />Город: %CITY%<br />Адрес: %ADRES%<br />Время начала: %TIMESTART%<br />Вид мероприятия: %TYPE%<br />Количество билетов: %COUNT%</p>', $this->settings);
        $body = str_replace("%NUMBER%", $orderNumber, $body);
        $body = str_replace("%DATE%", $this->convertDate($sendData['date']), $body);
        $body = str_replace("%CITY%", $sendData['city_name'], $body);
        $body = str_replace("%ADRES%", $sendData['adres'], $body);
        $body = str_replace("%TIMESTART%", $this->convertDate($sendData['date'], 'H:i'), $body);
        $body = str_replace("%TYPE%", $sendData['type_name'], $body);
        $body = str_replace("%COUNT%", $reservation, $body);
        
        $mail = new Zend_Mail('Windows-1251');
        //$mail->setFrom('webmaster@upline24.ru', 'Webmaster');
        $mail->setFrom('Upline24@'.$site[0][$_SESSION['countryDName']], 'Upline24');
        
        $mail->setSubject($subject);
	    $mail->setBodyHtml($body);
	    		    
	    $mail->addTo($this->auth->email, $this->auth->name.' '.$this->auth->surname);
/*	    	
            $user_info = $this->db->fetchRow("SELECT * FROM `users` WHERE `id` = '".$this->auth->id."'");
            
            if ($user_info['diamond']){
            $arr_uname=explode(' ', $user_info['diamond']);
            
            if (empty($arr_uname[0]))
                $arr_uname[0]='';
            if (empty($arr_uname[1]))
                $arr_uname[1]='';
            
            
            if (((  $arr_uname[0] == $user_info['name']) 
                    OR ($arr_uname[0] == $user_info['surname'])) 
                    AND (($arr_uname[1] == $user_info['name']) 
                    OR ($arr_uname[1] == $user_info['surname'])))
                $send_diamond=0;
            else
                $send_diamond = $this->db->fetchOne("SELECT `email` FROM `users` WHERE ((`name` = '".$arr_uname[0]."' AND `surname` = '".$arr_uname[1]."') OR (`name` = '".$arr_uname[1]."' AND `surname` = '".$arr_uname[0]."'))");
            
            }
            // Проверка emerald
            if ($user_info['emerald']){
            $arr_uname=explode(' ', $user_info['emerald']);
            
            if (empty($arr_uname[0]))
                $arr_uname[0]='';
            if (empty($arr_uname[1]))
                $arr_uname[1]='';
            
            
            if (((  $arr_uname[0] == $user_info['name']) 
                    OR ($arr_uname[0] == $user_info['surname'])) 
                    AND (($arr_uname[1] == $user_info['name']) 
                    OR ($arr_uname[1] == $user_info['surname'])))
                $send_emerald=0;
            else
                $send_emerald = $this->db->fetchOne("SELECT `email` FROM `users` WHERE ((`name` = '".$arr_uname[0]."' AND `surname` = '".$arr_uname[1]."') OR (`name` = '".$arr_uname[1]."' AND `surname` = '".$arr_uname[0]."'))");

            }
            if ($user_info['platinum']){
                
           
            
            // Проверка platinum
            $arr_uname=explode(' ', $user_info['platinum']);
            
                 if (empty($arr_uname[0]))
                $arr_uname[0]='';
            if (empty($arr_uname[1]))
                $arr_uname[1]='';
            
            if (((  $arr_uname[0] == $user_info['name']) 
                    OR ($arr_uname[0] == $user_info['surname'])) 
                    AND (($arr_uname[1] == $user_info['name']) 
                    OR ($arr_uname[1] == $user_info['surname'])))
                $send_platinum=0;
            else
                $send_platinum = $this->db->fetchOne("SELECT `email` FROM `users` WHERE ((`name` = '".$arr_uname[0]."' AND `surname` = '".$arr_uname[1]."') OR (`name` = '".$arr_uname[1]."' AND `surname` = '".$arr_uname[0]."'))");
            }
            // отправим писмо diamond
            if ($send_diamond){
                $mail->addTo($send_diamond, $this->auth->name.' '.$this->auth->surname);
            //    $mail->send();
            }
            // отправим писмо emerald
            if ($send_emerald){
                $mail->addTo($send_emerald, $this->auth->name.' '.$this->auth->surname);
            //    $mail->send();
            }
            // отправим писмо platinum
            if ($send_platinum){
                $mail->addTo($send_platinum, $this->auth->name.' '.$this->auth->surname);
           //     $mail->send();
            }

 * 
 */
                        $mail->send();

            
            
            
            
            
            
		/*$tickets = $this->db->fetchRow("SELECT * FROM `tickets` WHERE `event_id` = '$event_id' AND `sector_id` = '$sector_id' AND `user_id` = '".$this->auth->id."'");
		
		if (!$tickets) {
			$data = array(
				'event_id' => $event_id,
				'sector_id' => $sector_id,
				'user_id' => $this->auth->id,
				'count' => $reservation,
				'date' => mktime(),
				'number' => (int) date('dmyHis').$event_id.$sector_id
			);
			
			$this->db->insert("tickets", $data);
		} else {
			$data = array(
				'count' => (int) $tickets['count'] + $reservation
			);
			
			$this->db->update("tickets", $data, $tickets['id']);
		}*/
		
		$buy_tickets = $this->db->fetchRow("SELECT * FROM `system` WHERE `href` = 'ticket-buy'");
		
		$this->setMetaTags($buy_tickets);
		$this->viewMessage(stripslashes($buy_tickets['body']).'<br /><br /><p><a href="/viewevent/'.$event_id.'">Вернуться к семинару</a></p>');
		
		$hash->hashCode = null;
		
		$cost = (float) $this->db->fetchOne("SELECT `cost` FROM `event_types` WHERE `id` = '".$event['type_id']."'");
		if ($cost > 0) {
			$total_cost = (float) ((int) $reservation * $cost);
			
			$merc_sign = "X5bP9JQJauTARvht83xMKNLXY2qF";
/*			
			$xml = "<request>      
			<version>1.2</version>
			<merchant_id>i8453807395</merchant_id>
			<result_url>http://upline24.ru/buysucess</result_url>
			<server_url>http://upline24.ru/buyconfirm.php</server_url>
			<order_id>ORDER_$orderNumber</order_id>
			<amount>$total_cost</amount>
			<currency>RUR</currency>
			<description>Tickets of Diamond Alliance Ukraine</description>
			<default_phone></default_phone>
			<pay_way>card</pay_way>
			</request>";
*/
                        			$xml = "<request>      
			<version>1.2</version>
			<merchant_id>".$site[3][$_SESSION['countryDName']]."</merchant_id>
			<result_url>http://".$site[0][$_SESSION['countryDName']]."/buysucess</result_url>
			<server_url>http://".$site[0][$_SESSION['countryDName']]."/buyconfirm.php</server_url>
			<order_id>ORDER_$orderNumber</order_id>
			<amount>$total_cost</amount>
			<currency>".$money[4][$_SESSION['countryDName']]."</currency>
			<description>".$site[1][$_SESSION['countryDName']]."</description>
			<default_phone></default_phone>
			<pay_way>card</pay_way>
			</request>";
                                                
			$sign = base64_encode(sha1($merc_sign.$xml.$merc_sign,1));
			$xml_encoded = base64_encode($xml); 
			
			$message = '<form action="https://www.liqpay.com/?do=clickNbuy" method="POST" name="buy" />
			<input type="hidden" name="operation_xml" value="'.$xml_encoded.'" />
			<input type="hidden" name="signature" value="'.$sign.'" />
			<!--<input type="submit" value="Оплатить" />-->
			</form>
			<script>
                document.buy.submit();
            </script>';
			
			$this->viewMessage($message);
		}
		
		return true;
	}
	
	public function buysucess()
	{
		$buysucess = $this->db->fetchRow("SELECT * FROM `system` WHERE `href` = 'buy-success'");
		//print_r($_POST);
		$operation_xml = $_POST['operation_xml'];
		$xml = base64_decode($operation_xml);
		//print_r($xml);
		
		$this->logger->addLogRow('buysucess', serialize($xml));
		
		$this->setMetaTags($buysucess);
		$this->viewMessage(stripslashes($buysucess['body']));
		$this->edPageSys('buy-success');
		return true;
	}
	
	
	public function purchases()
	{
		$user_id = $this->auth->id;
		
		if ($user_id == 1) {
		    //$user_id = 107;
		    //$user_id = 186;
		    //$user_id = 618;
		}
		
		$select = $this->db->select();
		$select->from('events', array('id', 'name', 'date', 'date2','pic'));
		$select->join('hall', 'events.hall_id = hall.id', array('adres'));
		$select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
		$select->join('event_types', 'events.type_id = event_types.id', array('event_type' => 'name'));
		$select->join('sectors', 'hall.id = sectors.hall_id', array());
		$select->join('tickets', 'tickets.event_id = events.id AND tickets.sector_id = sectors.id', array('ticket_id' => 'id', 'count', 'payment', 'ticket_number' => 'code','number','ids'));
		$select->where('tickets.user_id = ?', array("$user_id"));
                $select->where('tickets.ids = ?', '0');
                $select->order('tickets.date DESC');
                $select->order('tickets.id ASC');
		//$select->group('events.id');
		//echo $select->__toString();
                //echo $select; die;
		$purchases = $this->db->fetchAll($select);
		
		$this->tpl->define_dynamic('purchases', 'purchases.tpl');
		$this->tpl->define_dynamic('list', 'purchases');
		$this->tpl->define_dynamic('list_row', 'list');
		$this->tpl->define_dynamic('list_row_status_on', 'list_row');
		$this->tpl->define_dynamic('list_row_status_off', 'list_row');
		$this->tpl->define_dynamic('list_empty', 'purchases');
		
		if ($purchases) {
			$this->tpl->parse('LIST_ROW_EMPTY', 'null');
			$oldNumber='';
			foreach ($purchases as $row) {
                            if ($oldNumber!=$row['number']){
                            //echo $row['number'].'<br>';
                                $oldNumber=$row['number'];
				$pic = '';
                                //echo $oldNumber.' '.$row['number'].'<br>';
				if ($row['pic'] && file_exists('./images/events/'.$row['pic'])) {
					$pic = '<div class="img"><img src="/images/events/'.$row['pic'].'" width="118" height="100" alt="'.$row['name'].'" title="'.$row['name'].'" /></div>';
				}
				//if ($row['number']!=$oldNumber) {
                                //    $oldNumber=$row['number'];
                                if ($row['count']!=0){ 
                                    
                                    //echo $row['ids'].'<br>';
				$this->tpl->assign(
					array(
						'EVENT_ID' => $row['id'],
						'TICKET_ID' => $row['ticket_id'],
						'EVENT_NAME' => $row['name'],
						'TICKET_NUMBER' => $row['ticket_number'],
						'EVENT_DATE' => $this->convertDate($row['date']),
                                                'EVENT_DATE2' => ($row['date']>=$row['date2']+10?'':' - '.$this->convertDate($row['date2'])),
						'EVENT_CITY' => $row['city_name'],
						'EVENT_ADRES' => $row['adres'],
						'EVENT_TIME' => $this->convertDate($row['date'], 'H:i'),
						'EVENT_TYPE' => $row['event_type'],
						'EVENT_TICKETS' => '<p>Куплено билетов: <strong>'.$row['count'].'</strong></p>',
						'EVENT_PIC' => $pic,
                                                'BUTTON_PRINT' => ''
					)
				);
                                
				if ($row['payment'] == '1') {
				    $this->tpl->parse('LIST_ROW_STATUS_OFF', 'null');
				    $this->tpl->parse('LIST_ROW_STATUS_ON', 'list_row_status_on');
				} else {
					$this->tpl->parse('LIST_ROW_STATUS_ON', 'null');
					$this->tpl->parse('LIST_ROW_STATUS_OFF', 'list_row_status_off');
				}
				
				$this->tpl->parse('LIST_ROW', '.list_row');
                                }
                            }
			}
		} else {
			$this->tpl->parse('LIST_ROW', 'null');
			$this->tpl->parse('LIST_ROW_EMPTY', '.list_row_empty');
		}
		$this->edPageMeta('purchases');
		$this->tpl->parse('CONTENT', '.list');
		
		return true;
	}
	
	public function ticketprint()
	{
		$id = $this->getVar('ticketId', null, $_POST);
        
        if (null === $id || !ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $select = $this->db->select();
        $select->from('tickets', array('id', 'count', 'payment', 'ticket_date' => 'date', 'row', 'location', 'lastNumber'));
        $select->join('events', 'events.id = tickets.event_id', array('event_id' => 'id', 'event_name' => 'name', 'event_date' => 'date',  'pic'));
        $select->join('event_types', 'event_types.id = type_id', array('event_type' => 'name'));
        $select->join('hall', 'hall.id = events.hall_id', array('adres'));
        $select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
        $select->join('sectors', 'sectors.id = tickets.sector_id', array('sector_name' => 'name'));
        $select->join('users', 'users.id = tickets.user_id', array('user_id' => 'id', 'user_name' => 'name', 'surname', 'number'));
        $select->where('tickets.ids='.$id.' OR tickets.id = ?', array("$id"));
        $select->order('tickets.id ASC');
        
       //echo $select; die;
        //$select->where('tickets.ids = '.$id.'?', array("$id"));
        //echo $select; die;
        $tickets = $this->db->fetchAll($select);
       
        //print_r($ticket);
        //die;
        //echo $select->__toString(); die;
        /*
        if (!$ticket || sizeof($ticket) != 1) {
            die('1');
        	return false;
        }
        */
        $ticket = $tickets[0];
        //foreach ($ticket as $ticket1) {
            
        //}
        $ticketId = $ticket['id'];
        
        //$scannerCode = $this->db->fetchAll("SELECT * FROM `tickets_code` WHERE `ticket_id` = '$ticketId' OR `ids`='$ticketId' ORDER BY `id` DESC");
        //print_r($scannerCode);
        //die;
        
        $tick = $this->db->fetchAll("SELECT `id` FROM `tickets` WHERE `id` = '$ticketId' OR `ids`='$ticketId' ORDER BY `id` ASC");
        
        //echo "SELECT `id` FROM `tickets` WHERE `id` = '$ticketId' OR `ids`='$ticketId' ORDER BY `id` ASK";
        foreach ($tick as $code){
            $sCode = $this->db->fetchRow("SELECT `code` FROM `tickets_code` WHERE `ticket_id` = '".$code['id']."'");
            $scannerCode[]=$sCode['code'];
        }
        
        //var_dump($scannerCode); die;
        //$scannerCode = $this->db->fetchAll("SELECT * FROM `tickets_code` WHERE `ticket_id` = '$ticketId'");
        if (!$scannerCode) {
            die('2');
            return false;
        }
        
        require_once "library/Pdf.php";
        //echo $ticket;
        $myPdf = new My_Pdf($tickets, $scannerCode);
        $pdfString = $myPdf->__toString();

 
        /*$pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        
        
        $page->drawRectangle(30, 820, 566.2, 672.6, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
        
        $image = Zend_Pdf_Image::imageWithPath('./images/events/big/'.$ticket['pic']);
        $page->drawImage($image, 38.9, 687.2, 132.7, 796.2);
        
        $pdf->pages[] = $page;
        $pdfString = $pdf->render();*/
        //print_r($ticket);
        //var_dump($pdfString);
        //exit();
        ini_set('max_execution_time', '360');
        header('Content-type: application/pdf');
        echo $pdfString;
        //var_dump($image);
        exit();
        //var_dump($pdfString);
        
        return true;
	}
	
	public function ticketpay()
	{
            global $money, $site;
	    $id = $this->getVar('ticketId', null, $_POST);
        
        if (null === $id || !ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            //die('1');
            return false;
        }
        
        $ticket = $this->db->fetchRow("SELECT * FROM `tickets` WHERE `id` = '$id'");
        
        if (!$ticket) {
            //die('2');
            return false;
        }
        
        if ($ticket['payment'] != 0) {
            //die('3');
            return false;
        }
        
        $user_id = $this->auth->id;
        
        if ($user_id != $ticket['user_id']) {
            //die('4');
            return false;
        }
        
        $buy_tickets = $this->db->fetchRow("SELECT * FROM `system` WHERE `href` = 'ticket-buy'");
		
		$this->setMetaTags($buy_tickets);
        
        if ((int) $ticket['cost'] == 0) {
            $this->viewMessage('Сумма оплаты должна быть больше нуля. Обратитесь пожалуйста к Администратору сайта.');
            return true;
        }
        
        $total_cost = (float) ((int) $ticket['count'] * $ticket['cost']);
        
        $merc_sign = $site[2][$_SESSION['countryDName']];//"X5bP9JQJauTARvht83xMKNLXY2qF";
			
/*		$xml = "<request>      
		<version>1.2</version>
		<merchant_id>i8453807395</merchant_id>
		<result_url>http://upline24.ru/buysucess</result_url>
		<server_url>http://upline24.ru/buyconfirm.php</server_url>
		<order_id>ORDER_".$ticket['code']."</order_id>
		<amount>$total_cost</amount>
		<currency>RUR</currency>
		<description>Tickets of Diamond Alliance Ukraine</description>
		<default_phone></default_phone>
		<pay_way>card</pay_way>
		</request>";
*/
                $xml = "<request>      
		<version>1.2</version>
		<merchant_id>".$site[3][$_SESSION['countryDName']]."</merchant_id>
		<result_url>http://".$site[0][$_SESSION['countryDName']]."/buysucess</result_url>
		<server_url>http://".$site[0][$_SESSION['countryDName']]."/buyconfirm.php</server_url>
		<order_id>ORDER_".$ticket['code']."</order_id>
		<amount>$total_cost</amount>
		<currency>".$money[4][$_SESSION['countryDName']]."</currency>
		<description>".$site[1][$_SESSION['countryDName']]."</description>
		<default_phone></default_phone>
		<pay_way>card</pay_way>
		</request>";
		$sign = base64_encode(sha1($merc_sign.$xml.$merc_sign,1));
		$xml_encoded = base64_encode($xml); 
		
		$message = '<form action="https://www.liqpay.com/?do=clickNbuy" method="POST" name="buy" />
		<input type="hidden" name="operation_xml" value="'.$xml_encoded.'" />
		<input type="hidden" name="signature" value="'.$sign.'" />
		<!--<input type="submit" value="Оплатить" />-->
		</form>
		<script>
            document.buy.submit();
        </script>';
		
		$this->viewMessage($message);
		$this->edPageSys('ticket-buy');
		return true;
	}
	
	
	public function getplatezhticket()
	{
		global $money, $site;
	    //var_dump($this->numberToString(1546578));
	    //exit();
	    
	    $id = $this->getVar('ticketId', null, $_POST);
        
        if (null === $id || !ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $select = $this->db->select();
        $select->from('tickets', array('id', 'count', 'payment', 'ticket_date' => 'date', 'lastNumber', 'cost', 'code'));
        $select->join('events', 'events.id = tickets.event_id', array('event_id' => 'id', 'event_name' => 'name', 'event_date' => 'date', 'pic'));
        $select->join('event_types', 'event_types.id = type_id', array('event_type' => 'name'));
        $select->join('hall', 'hall.id = events.hall_id', array('adres'));
        $select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
        $select->join('sectors', 'sectors.id = tickets.sector_id', array('sector_name' => 'name'));
        $select->join('users', 'users.id = tickets.user_id', array('user_id' => 'id', 'user_name' => 'name', 'surname', 'number'));
        $select->where('tickets.id = ?', array("$id"));
        
        $ticket = $this->db->fetchAll($select);
        
        if (!$ticket || sizeof($ticket) != 1) {
            die('1');
        	return false;
        }
        
        $ticket = $ticket[0];
	    
        //print_r($ticket);
        //exit();
        
	    set_include_path(get_include_path() . PATH_SEPARATOR . 'excel/');
        include_once 'PHPExcel/IOFactory.php';
        
        $objPHPExcel = PHPExcel_IOFactory::load("xls/".$_SESSION['countryDName']."/template_ticket.xls");
        $objPHPExcel->setActiveSheetIndex(0);
        $aSheet = $objPHPExcel->getActiveSheet();
        //$excel = new PHPExcel("platezhka_ticket.xls");
        
        //$excel->load("./platezhka_ticket.xls");
        //$excel->setActiveSheetIndex(0);
        //$aSheet = $excel->getActiveSheet();
        
        //$aSheet->setCellValue('A9', mb_convert_encoding($ticket['user_name'].' '.$ticket['surname'] ,'utf-8', 'windows-1251'));
        $aSheet->setCellValue('B6', mb_convert_encoding("№ ".$ticket['code'] ,'utf-8', 'windows-1251'));
        $aSheet->setCellValue('B9', mb_convert_encoding(trim($this->numberToString($ticket['count']*$ticket['cost'])) ,'utf-8', 'windows-1251'));
        //$aSheet->setCellValue('E10', mb_convert_encoding(number_format($ticket['count']*$ticket['cost'], 2, '.', '').' рублей', 'utf-8', 'windows-1251'));
        $aSheet->setCellValue('E10', mb_convert_encoding(number_format($ticket['count']*$ticket['cost'], 2, '.', '').' '.$money[2][$_SESSION['countryDName']], 'utf-8', 'windows-1251'));
        $aSheet->setCellValue('A27', mb_convert_encoding("ПЛАТА ЗА БИЛЕТЫ НА СЕМИНАР ".$this->convertDate($ticket['event_date'])." ЗАКАЗ № ".$ticket['code'] ,'utf-8', 'windows-1251'));
        
        include("PHPExcel/Writer/Excel5.php");
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="print.xls"');
        header('Cache-Control: max-age=0');
        
        //$objWriter = new PHPExcel_Writer_Excel5($excel);
        $objWriter->save('php://output');
        //@unlink('./platezh.xls');
        exit;
	}
	
	public function getplatezhdisk($id = null)
	{
		global $money, $site;
	    //var_dump($this->numberToString(1546578));
	    //exit();
	    
	    $user_id = $this->auth->id;
	    
	    if (null === $id) {
	       $id = $this->getVar('diskId', null, $_POST);
	    }
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $order = $this->db->fetchRow("SELECT * FROM `order_disc` WHERE `id` = '$id'");
        
        if (!$order) {
            return false;
        }
        
        if ($order['user_id'] != $user_id) {
            return false;
        }
        
        $items = $this->db->fetchAll("SELECT `order_disc_items`.*, `discs`.`name`, `discs`.`articul`, `discs`.`cost` FROM `order_disc_items`, `discs` WHERE `order_id` = '$id' AND `order_disc_items`.`disc_id` = `discs`.`id`");
        
        if (!$items) {
            return false;
        }
        
        $totalSumm = 0;
        
        foreach ($items as $row) {
            $totalSumm += $row['summ'];
        }
        
        //print_r($ticket);
        //exit();
        
	    set_include_path(get_include_path() . PATH_SEPARATOR . 'excel/');
        include_once 'PHPExcel/IOFactory.php';
        
        $objPHPExcel = PHPExcel_IOFactory::load("xls/".($order['country']!=''?$order['country']:'ru')."/template_disk.xls");
        $objPHPExcel->setActiveSheetIndex(0);
        $aSheet = $objPHPExcel->getActiveSheet();
        //$excel = new PHPExcel("platezhka_ticket.xls");
        
        //$excel->load("./platezhka_ticket.xls");
        //$excel->setActiveSheetIndex(0);
        //$aSheet = $excel->getActiveSheet();
        
        //$aSheet->setCellValue('A9', mb_convert_encoding($ticket['user_name'].' '.$ticket['surname'] ,'utf-8', 'windows-1251'));
        $aSheet->setCellValue('B6', mb_convert_encoding("№ ".$order['id'] ,'utf-8', 'windows-1251'));
        $aSheet->setCellValue('B9', mb_convert_encoding(trim($this->numberToString($totalSumm)) ,'utf-8', 'windows-1251'));
        //$aSheet->setCellValue('E10', mb_convert_encoding(number_format($totalSumm, 2, '.', '').' рублей', 'utf-8', 'windows-1251'));
        $aSheet->setCellValue('E10', mb_convert_encoding(number_format($totalSumm, 2, '.', '').' '.$money[2][$_SESSION['countryDName']], 'utf-8', 'windows-1251'));
        $aSheet->setCellValue('A27', mb_convert_encoding("ПЛАТА ЗА CD ДИСКИ  ЗАКАЗ № ".$order['id'] ,'utf-8', 'windows-1251'));
        
        include("PHPExcel/Writer/Excel5.php");
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="print.xls"');
        header('Cache-Control: max-age=0');
        
        //$objWriter = new PHPExcel_Writer_Excel5($excel);
        $objWriter->save('php://output');
        //@unlink('./platezh.xls');
        exit;
	}
	
	
	public function disc()
	{
            if (((isset($_GET['show']) and $_GET['show']=='desc') or (isset ($_SESSION['show']) and $_SESSION['show']=='desc')) 
                    and (!isset($_GET['show']) or $_GET['show']!='all')){
                $_SESSION['show']='desc';
            } else {
                $_SESSION['show']='all';
            }
            global $site, $money;
	    $delivery = array();
	    $disc = array();
	    $totalCount = 0;
	    
	    if (!empty($_POST)) {
	        if (isset($_POST['delivery']) && !empty($_POST['delivery'])) {
	            $delivery = $_POST['delivery'];
	            unset($_POST['delivery']);
	        }
	        
	        $disc = $_POST['disc'];
	        
	        $discArray = array();
	        
	        foreach ($_POST['disc'] as $id => $count) {
	            //print_r($id);
	            //print_r($count);
	            if ((int) $count['count'] > 0) {
	                $discArray[] = array('id' => (int) $id, 'count' => (int) $count['count'], 'cost' => (float) $count['cost']);
	                $totalCount += (int) $count['count'];
	            }
	        }
	        
	        $this->isDelivery($delivery);
	        
	        if ($totalCount < 0) {
                $this->addErr('Общее количество заказываемых дисков должно быть больше 0(нуля)');
            }
            if (!$this->_err) {
            
            
                return $this->buydiscs($discArray, $delivery);
            }
	    }
	    
	    $totalCount = 0;
	    $totalSumm = 0;
	    
	    // Добавить сортировку?
            //echo "SELECT * FROM `discs` WHERE (".($this->_isAdmin() ? "" : " `visibility` = '1' and ")." `country`='".$_SESSION['country']."') ORDER BY id DESC"; die;
	    $discs = $this->db->fetchAll("SELECT * FROM `discs` WHERE (".($this->_isAdmin() ? "" : " `visibility` = '1' and ")." `country`='".$_SESSION['countryDName']."') ORDER BY `name`,`id` DESC");
	    
	    $this->tpl->define_dynamic('discs', 'discs.tpl');
	    $this->tpl->define_dynamic('discs_list', 'discs');
	    $this->tpl->define_dynamic('discs_list_row', 'discs_list');
            
            $this->tpl->define_dynamic('discs_list_short', 'discs');
	    $this->tpl->define_dynamic('discs_list_row_short', 'discs_list_short');
	    
            
	    $this->tpl->define_dynamic('discs_list_empty', 'discs');
	    
	    $this->tpl->assign(
            array(
                'CONTENT' => $this->getAdminAdd('disc')
            )
        );
	    $this->viewErr();
        if (!empty($_POST) && $this->_err) {
            
        }
        
	    if ($discs) {
	        foreach ($discs as $row) {
	            $pic = '<div class="img">&nbsp;</div>';
	            if ($row['pic'] && file_exists('./images/discs/'.$row['pic'])) {
	                $pic = '<div class="img"><img src="/images/discs/'.$row['pic'].'" width="97" height="97" alt="'.stripslashes($row['name']).'" title="'.stripslashes($row['name']).'" /></div>';
	            }
if ($_SESSION['show']=='desc'){
    $discName =$this->getAdminEdit('disc', $row['id']).'<a class="short_link" href="/viewdisc/'.$row['id'].'">'.stripslashes((strlen($row['name'])>54?substr($row['name'],0,54).'...':$row['name'])).'</a>';
} else {
    $discName =$this->getAdminEdit('disc', $row['id']).'<a class="short_link" href="/viewdisc/'.$row['id'].'">'.stripslashes($row['name']).'</a>';
}	            
	            $this->tpl->assign(
                    array(
                        'DISC_ID' => $row['id'],
                        'DISC_ARTICUL' => stripslashes($row['articul']),
                        
                        'DISC_NAME' =>  $discName,
                        'DISC_PREVIEW' => stripslashes($row['preview']),
                        'DISC_COST' => (float) $row['cost'],
                        'DISC_PIC' => $pic,
                        'DISC_COUNT' => isset($disc[$row['id']]) ? $this->getVar('count', 0, $disc[$row['id']]) : 0
                    )
	            );
	            
	            if (isset($disc[$row['id']])) {
	                $totalCount += (int) $disc[$row['id']]['count'];
	                $totalSumm += (float) $disc[$row['id']]['count']*$disc[$row['id']]['cost'];
	            }
	            if ($_SESSION['show']=='desc') {
                        $this->tpl->parse('DISCS_LIST_ROW_SHORT', '.discs_list_row_short');
                    } else { $this->tpl->parse('DISCS_LIST_ROW', '.discs_list_row'); }
	        }
	        
	        $this->tpl->assign(
	           array(
	               'TOTAL_COUNT' => $totalCount,
	               'TOTAL_SUMM' => $totalSumm,
	               'DELIVERY_ZIP' => $this->getVar('zip', '', $delivery),
	               'DELIVERY_CITY' => $this->getVar('city', '', $delivery),
	               'DELIVERY_ADRES' => $this->getVar('adres', '', $delivery),
	               'DELIVERY_PHONE' => $this->getVar('phone', '', $delivery),
	               'DELIVERY_FULLNAME' => $this->getVar('fullname', '', $delivery),
	               'DELIVERY_PASSPORT' => $this->getVar('passport', '', $delivery),
	               'DELIVERY_INFO' => $this->getVar('info', '', $delivery),
                       'ACTIVE_DESC'=>($_SESSION['show']=='desc'?' active_desc':''),
                       'ACTIVE_ALL'=>($_SESSION['show']=='all'?' active_all':''),
                       'CLASS_ACTIVE_RU'    =>  ($_SESSION['countryDName']=='ru'?' class="acrive"':' class="noacrive"'),
                       'CLASS_ACTIVE_UA'    =>  ($_SESSION['countryDName']=='ua'?' class="acrive"':' class="noacrive"'),
                       'DOSTAVKA_I'=>($_SESSION['countryDName']=='ua'?'<h1><br>Доставка по Украине:<br>- 10 дисков - 15 грн.<br>- 30 дисков - 20 грн.<br>- 100 дисков - 25 грн.<br></h1>':''),
                       'DOSTAVKA_O'=>($_SESSION['countryDName']=='ru'?'Доставка дисков осуществляется только по территории России.':'Доставка дисков осуществляется только по территории Украины.')
	           )
	        );
	        if ($site[6][$_SESSION['countryDName']]==1) {
                $disc_kray = $this->db->fetchAll("SELECT * FROM `calculate_shipping` WHERE `type` = '1'");
                $opt = '';
                if ($disc_kray){
                    foreach ($disc_kray as $kray) {
                        $opt.='<option value="'.$kray['id'].'">'.$kray['punkt'].'</option>';
                    }
                }
                
	        $this->tpl->assign(
	           array('PRINT_REG'=>'<h3>Примерный расчет стоимости доставки</h3>
                       <table><tr><td style="width:270px;">
                       <select size="1" name="country" onchange="javascript:selectRegion();" style="float:left;">
                        <option>Выберите местоположение</option>'.$opt.'</select>
                            </td><td style="text-align:left;"><a href="#" onclick="javascript:selectRegion(); return false;">Просчитать</a></td></tr></table>
                        <div name="selectDataRegion" style=""></div>
                        <div name="selectDataCity" style=""></div>
                        <br>')
	            );
                                   $this->tpl->assign(
	           array('PRINT_REG'=>'')
	            );

                } else {
                   $this->tpl->assign(
	           array('PRINT_REG'=>'')
	            );

                }
                
                
                
                
	        
                if ($_SESSION['show']=='desc') {
                    $this->tpl->parse('CONTENT', '.discs_list_short');
                } else { $this->tpl->parse('CONTENT', '.discs_list'); }


                //$this->tpl->parse('CONTENT', '.discs_form');
	    } else {
	        $this->tpl->parse('CONTENT', '.discs_list_empty');
	    }
	                    

            $this->mysettings('form');
            $this->edPageMeta('disc');
	    return true;
	}
	
	public function viewDisc()
	{
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        //Если пользователь не авторизирован, то текст ему, а не список дисков
        if (!isset($this->auth->id)){
            $this->tpl->assign(
	       array(
                   'KEYWORDS'   =>  '',
                   'DESCRIPTION'=>  '',
                   'TITLE'      =>  '',
                   'HEADER'     =>  '',
	           'CONTENT'    =>  '<div class="user_no_res_disc">
                                        <p>Приобрести обучающие материалы Альянса Бриллиантов можно, 
                                        пройдя регистрацию или авторизацию на сайте.</p>
                                        <p><a href="/">Вернуться на главную.</a></p></div>',
	       )
	    );
            return true;
        }
        
        $disc = $this->db->fetchRow("SELECT * FROM `discs` WHERE `id` = '$id'");
        
        if (!$disc) {
            return false;
        }
        
        $this->tpl->define_dynamic('discs', 'discs.tpl');
	    $this->tpl->define_dynamic('discs_detail', 'discs');
	    
	    $this->setMetaTags($disc);
	    
	    $buyCount = $this->db->fetchOne("SELECT SUM(`count`) FROM `order_disc_items` WHERE `disc_id` = '$id'");
	    
	    $pic = '';
        if ($disc['pic'] && file_exists('./images/discs/big/'.$disc['pic'])) {
            $pic = '<div class="fl_left"><img src="/images/discs/big/'.$disc['pic'].'" width="236" height="236" alt="'.stripslashes($disc['name']).'" title="'.stripslashes($disc['name']).'" /></div>';
        }
	    
	    $this->tpl->assign(
	       array(
	           'DISC_ARTICUL' => stripslashes($disc['articul']),
	           'DISC_NAME' => stripslashes($disc['name']),
	           'DISC_PREVIEW' => stripslashes($disc['preview']),
	           'DISC_BODY' => stripslashes($disc['body']),
	           'DISC_COST' => (float) $disc['cost'],
	           'DISC_BUY_COUNT' => (int) $buyCount,
	           'DISC_PIC' => $pic,
                   'ED1'    =>  ($disc['ed1']!=''?$disc['ed1']:''),
                   'ED2'    =>  ($disc['ed2']!=''?$disc['ed2']:'')
	       )
	    );
	    
	    $this->tpl->parse('CONTENT', '.discs_detail');
        
        return true;
	}
	
	private function isDelivery($delivery = null) {
	    if (null === $delivery) {
	        return false;
	    }
	    
	    $delivery = (array) $delivery;
	    //echo $delivery['c_slots'].'------------';
	    /*
            if (!isset($delivery['zip']) || empty($delivery['zip'])) {
	        $this->addErr('Вы не указали индекс');
	        //return false;
	    }
	    
	    if (!isset($delivery['city']) || empty($delivery['city'])) {
	        $this->addErr('Вы не указали город');
	        //return false;
	    }
	    
	    if (!isset($delivery['adres']) || empty($delivery['adres'])) {
	        $this->addErr('Вы не указали адрес');
	        //return false;
	    }
	    
	    if (!isset($delivery['phone']) || empty($delivery['phone'])) {
	        $this->addErr('Вы не указали номер телефона');
	        //return false;
	    }
	    
	    if (!isset($delivery['fullname']) || empty($delivery['fullname'])) {
	        $this->addErr('Вы не указали Ф.И.О. получателя');
	        //return false;
	    }
	    
	    if (!isset($delivery['passport']) || empty($delivery['passport'])) {
	        $this->addErr('Вы не указали Паспортные данные получателя');
	        //return false;
	    }
             * 
             */
	}
	
	private function buydiscs($discArray = null, $delivery = null)
	{
            global $money, $site;
	    if (null === $discArray || null === $delivery) {
	        return false;
	    }
	    
	    //print_r($discArray);
	    //print_r($delivery);
	    
	    $this->setMetaTags('Покупка дисков');
	    //echo $_SESSION['countryDName']; die;
	    $date = mktime();
	    //echo $delivery['c_slots'];
            $disc_data = $this->db->fetchRow("SELECT * FROM `user_adr` WHERE `user_id`='".$this->auth->id."' AND `slot` = '".$delivery['c_slots']."' AND `country`='".$_SESSION['countryDName']."'");
	    $data = array(
	       'date' => $date,
	       'zip' => $disc_data['index'],
	       'city' => $disc_data['city'],
	       'adres' => $disc_data['adr'],
	       'phone' => $disc_data['phone'],
	       'fullname' => $disc_data['fio'],
	       'passport' => $disc_data['passport_data'],
	       'info' => $disc_data['add_info'],
	       'user_id' => $this->auth->id,
               'country'    =>  $_SESSION['countryDName']
	    );
	    
	    $this->logger->addLogRow('Покупка дисков', serialize($data));
	    
	    $this->db->insert('order_disc', $data);
	    
	    $id = $this->db->lastInsertId();
	    
	    $summ = 0;
	    $table = '';
	    
	    foreach ($discArray as $row) {
	        $disc = $this->db->fetchRow("SELECT * FROM `discs` WHERE `id` = '".$row['id']."'");
	        //$disc = $this->db->fetchRow("SELECT * FROM ``");
	        
	        if (!$disc) {
	            continue;
	        }
	        
	        $data = array(
	           'order_id'   =>  $id,
	           'disc_id'    =>  $row['id'],
	           'count'      =>  $row['count'],
	           'summ'       =>  (float) ($row['count'] * $disc['cost']),
                   'country'    =>  $_SESSION['countryDName']	        );
	        
	        $this->logger->addLogRow('Покупка дисков: позиция', serialize($data), 'sub', false);
	        
	        $this->db->insert('order_disc_items', $data);
	        
	        $summ += (float) ($row['count'] * $disc['cost']);
	        
/*	        $table .= '<tr>
   <td class="left"><a href="http://upline24.com.ua/viewdisc/'.$disc['id'].'">'.$disc['name'].'</a></td>
   <td>'.$disc['articul'].'</td>
   <td>'.((float) $disc['cost']).' руб</td>
   <td>'.$row['count'].' шт.</td>
   <td class="right"><strong>'.((float) $disc['cost']*$row['count']).' руб</strong></td>
  </tr>';
 * 
 */
                	        $table .= '<tr>
   <td class="left"><a href="http://'.$site[0][$_SESSION['countryDName']].'/viewdisc/'.$disc['id'].'">'.$disc['name'].'</a></td>
   <td>'.$disc['articul'].'</td>
   <td>'.((float) $disc['cost']).' '.$money[3][$_SESSION['countryDName']].'</td>
   <td>'.$row['count'].' шт.</td>
   <td class="right"><strong>'.((float) $disc['cost']*$row['count']).' '.$money[3][$_SESSION['countryDName']].'</strong></td>
  </tr>';
	        //$disc['count'] = $row['count'];
	        //$discs[] = $disc;
	    }
	    
	    $subject = $this->getVar('discbuy_subject', 'Заказ дисков на сайте: http://'.$_SERVER['HTTP_HOST'].'/', $this->settings);
            
        /*$body = $this->getVar('discbuy_message', '<p>Спасибо, что воспользовались услугами нашего сайта.<br /><br />Данные о заказе:<br />Индекс: %ZIP%<br />Город: %CITY%<br />Адрес: %ADRES%<br />Телефон: %PHONE%<br />Ф.И.О.: %FIO%<br />Дополнительная информаиция: %MESSAGE%</p>', $this->settings);
        $body = str_replace("%ZIP%", $delivery['zip'], $body);
        $body = str_replace("%CITY%", $delivery['city'], $body);
        $body = str_replace("%ADRES%", $delivery['adres'], $body);
        $body = str_replace("%PHONE%", $delivery['phone'], $body);
        $body = str_replace("%FIO%", $delivery['fullname'], $body);
        $body = str_replace("%MESSAGE%", $delivery['info'], $body);*/
        
        $body = '<style>.info {border-bottom:12px solid #bebebe;width:100%;}
.info td {font:bold 16px Tahoma;color:#000;padding:15px 30px 15px 0;vertical-align:top;white-space:nowrap;border-top:2px solid #c2c2c2;}
.info td.right {text-align:right;padding-right:0;text-align:justify;font-size:14px;white-space:normal;}
.info td.right span {font-weight:normal;}
.info td.right span span {font-size:20px;}
.info td.right strong {font-size:12px;}
.info td.noborder {vertical-align:middle;border-top:none;}
.prod_list {width:100%;}
.prod_list th {font:bold 14px Tahoma;color:#000;text-align:center;border-bottom:1px dotted #878787;padding:15px 5px;}
.prod_list .left {text-align:left;padding-left:0;}
.prod_list .right {text-align:right;padding-right:0;}
.prod_list td {font:14px Tahoma;color:#000;text-align:center;border-bottom:1px dotted #878787;padding:15px 5px;}
.prod_list td a, .wraper_print .prod_list td a:visited {color:#000;}
.prod_list td strong span {font-size:16px;}
.prod_list td.noborder {border:none;}</style><table class="info">
  <tr>
   <td class="noborder" colspan="2"><img src="http://upline24.ru/img/diamond2.gif" width="358" height="93" alt="" /></td>
   <td class="right noborder"><span>Номер заказа: &nbsp; <span>'.$id.'</span></span><br /><br /><span>Дата заказа:</span> &nbsp; <strong>'.$this->convertDate($date, 'd.m.Y H:i').'</strong></td>
  </tr>
  <tr>
   <td>Индекс:<br />Город:<br />Адрес:<br />ФИО получателя:<br />Паспортные данные:<br />Телефон:</td>
   <td>'.$disc_data['index'].'<br />'.$disc_data['city'].'<br />'.$disc_data['adr'].'<br />'.$disc_data['fio'].'<br />'.$disc_data['passport_data'].'<br />'.$disc_data['phone'].'</td>
   <td class="right">Дополнительная информация:<br /><span>'.$disc_data['add_info'].'</span></td>
  </tr>
 </table>';
        
        $body .= '<table class="prod_list"> 
  <tr>
   <th class="left">Название диска</th>
   <th>Код</th>
   <th>Цена</th>
   <th>Количество</th>
   <th class="right">Сумма </th>
  </tr>';
        
        $body .= $table;
/*        
        $body .= '<tr>
   <td colspan="3" class="noborder">&nbsp;</td>
   <td class="noborder"><strong>Итого:</strong></td>
   <td class="right noborder"><strong><span>'.$summ.'</span> руб</strong></td>
  </tr>
 </table>';
*/        
  $body .= '<tr>
   <td colspan="3" class="noborder">&nbsp;</td>
   <td class="noborder"><strong>Итого:</strong></td>
   <td class="right noborder"><strong><span>'.$summ.'</span> '.$money[3][$_SESSION['countryDName']].'</strong></td>
  </tr>
 </table>';

        $mail = new Zend_Mail('Windows-1251');
        //$mail->setFrom('webmaster@upline24.ru', 'Webmaster');
        $mail->setFrom('Upline24@'.$site[0][$_SESSION['countryDName']], 'Upline24');
        $mail->setSubject($subject);
	    $mail->setBodyHtml($body);
	    		    
	    $mail->addTo($this->auth->email, $this->auth->name.' '.$this->auth->surname);
	    		    
	    $mail->send();
	    
	    $mail->clearRecipients();
	    
	    $admin_email = $this->getVar('discbuy_email', 'vova@deluxe.dp.ua', $this->settings);
	    
	    $emails = explode(',', $admin_email);
	    foreach ($emails as $item) {
	        $mail->addTo(trim($item), $item);
	    }
	    		    
	    $mail->send();
	    
	    /*if (isset($_POST['pay_card'])) {
	        $payType = 'card';
	    } elseif (isset($_POST['pay_bank'])) {
	        $payType = 'bank';
	    } else {
	        return false;
	    }
	    
	    if ($payType == 'bank') {
	        return $this->getplatezhdisk($id);
	    }*/
	    
	    $buy_tickets = $this->db->fetchRow("SELECT * FROM `system` WHERE `href` = 'disc-buy'");
		//print_r($buy_tickets);
		$this->setMetaTags($buy_tickets);
		$this->viewMessage(stripslashes($buy_tickets['body']).'<br /><br /><p><a href="/disc/">Вернуться к каталогу дисков</a></p>');
                $merc_sign = $site[2][$_SESSION['countryDName']];//"X5bP9JQJauTARvht83xMKNLXY2qF";
			
		$xml = "<request>      
		<version>1.2</version>
		<merchant_id>".$site[3][$_SESSION['countryDName']]."</merchant_id>
		<result_url>http://".$site[0][$_SESSION['countryDName']]."/discsucess</result_url>
		<server_url>http://".$site[0][$_SESSION['countryDName']]."/discconfirm.php</server_url>
		<order_id>ORDER_$id</order_id>
		<amount>$summ</amount>
		<currency>".$money[4][$_SESSION['countryDName']]."</currency>
		<description>".$site[4][$_SESSION['countryDName']]."</description>
		<default_phone></default_phone>
		<pay_way>card</pay_way>
		</request>";
		
		$sign = base64_encode(sha1($merc_sign.$xml.$merc_sign,1));
		$xml_encoded = base64_encode($xml); 
		
		$message = '<form action="https://www.liqpay.com/?do=clickNbuy" method="POST" name="buy" />
		<input type="hidden" name="operation_xml" value="'.$xml_encoded.'" />
		<input type="hidden" name="signature" value="'.$sign.'" />
		<input type="submit" value="Оплата картой" class="button button_disk_pay" />
		</form>
		<br />
		<form action="/getplatezhdisk" method="POST" name="buy2" />
		<input type="hidden" name="diskId" value="'.$id.'" />
		<input type="submit" value="Оплата через банк" class="button button_disk_pay" />
		</form>';
                $add_mes='';
	if ($_POST['buy_type']=='card')	
           $add_mes='<script>document.buy.submit();</script>';
        else
           $add_mes='<script>document.buy2.submit();</script>';
		$this->viewMessage($message.$add_mes);
	    
            $this->edPageSys('disc-buy');
	    return true;
	}
	
	public function discsucess()
	{
	    $buysucess = $this->db->fetchRow("SELECT * FROM `system` WHERE `href` = 'disc-success'");
		//print_r($_POST);
		$operation_xml = $_POST['operation_xml'];
		$xml = base64_decode($operation_xml);
		//print_r($xml);
		
		$this->logger->addLogRow('discsucess', serialize($xml));
		
		$this->setMetaTags($buysucess);
		$this->viewMessage(stripslashes($buysucess['body']));
		$this->edPageSys('disc-success');
		return true;
	}
	
	public function mydiscs()
	{
            global $money, $site;
            if (isset($_GET['d'])) {
                $d=(int)$_GET['d'];
                if (!empty($d)){
                    $this->db->delete('order_disc', "`id` = '$d'");                    
                    $this->db->delete('order_disc_items', "`order_id` = '$d'");                    
                    echo '<script>window.location.href = "/mydiscs"</script>';
                }
            }
	    $user_id = $this->auth->id;
		
		$select = $this->db->select();
		$select->from('order_disc', array('id', 'date', 'status', 'country', 'summ' => 'SUM(order_disc_items.summ)'));
		$select->join('order_disc_items', 'order_disc.id = order_disc_items.order_id', array());
		//$select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
		//$select->join('event_types', 'events.type_id = event_types.id', array('event_type' => 'name'));
		//$select->join('sectors', 'hall.id = sectors.hall_id', array());
		//$select->join('tickets', 'tickets.event_id = events.id AND tickets.sector_id = sectors.id', array('ticket_id' => 'id', 'count', 'payment'));
		$select->where('order_disc.user_id = ?', array("$user_id"));
		$select->group('order_disc.id');
                $select->order('date DESC');
		//$select->group('events.id');
		//echo $select->__toString();
		$purchases = $this->db->fetchAll($select);
		//print_r($purchases);
		$this->tpl->define_dynamic('discs', 'discs.tpl');
		$this->tpl->define_dynamic('discs_order_list', 'discs');
		$this->tpl->define_dynamic('discs_order_list_row', 'discs_order_list');
		$this->tpl->define_dynamic('discs_order_list_empty', 'discs_order_list');
		
		if ($purchases) {
			$this->tpl->parse('DISCS_ORDER_LIST_EMPTY', 'null');
			
			foreach ($purchases as $row) {
                            
				switch ($row['status']) {
                                    case 0:
				        $status = 'оплата не подтверждена';
				        $statusClass = 'red';
				        break;
				    case 1:
				        $status = 'оплата не подтверждена';
				        $statusClass = 'red';
				        break;
				    case 2:
				        $status = 'оплачено';
				        $statusClass = 'or';
				        break;
                                    case 3:
				        $status = 'на доставке';
				        $statusClass = 'blue';
				        break;
                                    case 4:
				        $status = 'доставлено';
				        $statusClass = 'gr';
				        break;
                                    
			        default:
			            $status = 'доставлено';
				        $statusClass = 'gr';
				        break;
				}
				
/**///echo $row['country'];

                $merc_sign = $site[2][$row['country']];//"X5bP9JQJauTARvht83xMKNLXY2qF";
				//echo '<!--'.$merc_sign.'-->';
				//echo '<!--'.$site[3][$row['country']].'-->';
				//echo '<!--'.$row['summ'].'-->';
				//echo '<!--'.$row['id'].'-->';
                $xml = "<request>      
		<version>1.2</version>
		<merchant_id>".$site[3][$row['country']]."</merchant_id>
		<result_url>http://".$site[0]['ru']."/discsucess</result_url>
		<server_url>http://".$site[0]['ru']."/discconfirm.php</server_url>
		<order_id>ORDER_".$row['id']."</order_id>
		<amount>".$row['summ']."</amount>
		<currency>".$money[4][$row['country']]."</currency>
		<description>".$site[4][$row['country']]."</description>
		<default_phone></default_phone>
		<pay_way>card</pay_way>
		</request>";
		
		$sign = base64_encode(sha1($merc_sign.$xml.$merc_sign,1));
		$xml_encoded = base64_encode($xml); 

                
                
                                
                                
                                
                                
				$this->tpl->assign(
					array(
						'ORDER_ID' => $row['id'],
						'ORDER_DATE' => $this->convertDate($row['date']),
						'ORDER_NUMBER' => $row['id'],
						'ORDER_SUMM' => $row['summ'],
						'ORDER_STATUS_CLASS' => $statusClass,
						'ORDER_STATUS' => (($row['status']==0 or $row['status']==1)?$status.'<br /><a href="/mydiscs/?d='.$row['id'].'">Удалить</a>':$status),
                                                'ORDER_CARD_XML' =>$xml_encoded,
                                                'ORDER_CARD_SIGNATURE' =>$sign,
                                                'MONEY_COUNTRY' =>$money[3][$row['country']],
                                                'CLASS_BY_H'=>(($row['status']==0 or $row['status']==1)?'':'class="hide_b_t"')
                                            
					)
				);
				
				$this->tpl->parse('DISCS_ORDER_LIST_ROW', '.discs_order_list_row');
			}
		} else {
			$this->tpl->parse('DISCS_ORDER_LIST_ROW', 'null');
			$this->tpl->parse('DISCS_ORDER_LIST_EMPTY', '.discs_order_list_empty');
		}
		$this->edPageMeta('mydiscs');
		$this->tpl->parse('CONTENT', '.discs_order_list');
		
		return true;
	}
    
	public function viewOrder($id = null)
	{
		if (isset($this->auth->id))
			$user_id = $this->auth->id;
	    
	    if (null === $id) {
	       $id = end($this->url);
	    }
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $order = $this->db->fetchRow("SELECT * FROM `order_disc` WHERE `id` = '$id'");
        
        if (!$order) {
            return false;
        }
        
        if ($order['user_id'] != $user_id) {
            return false;
        }
        
        $items = $this->db->fetchAll("SELECT `order_disc_items`.*, `discs`.`name`, `discs`.`articul`, `discs`.`cost` FROM `order_disc_items`, `discs` WHERE `order_id` = '$id' AND `order_disc_items`.`disc_id` = `discs`.`id`");
        
        if (!$items) {
            return false;
        }
        
        //$this->tpl = new Templates('tpl/print/');
        $this->tpl->set_root('tpl/print/');
        $this->tpl->define_dynamic('page', "design.tpl");
        $this->tpl->define_dynamic('null', 'page');
        
        $this->tpl->define_dynamic('item_row', 'page');
        
        $totalSumm = 0;
        $countDisc=0;
        foreach ($items as $row) {
            $this->tpl->assign(
                array(
                    'PRINT_ORDER_DISC_NAME' => $row['name'],
                    'PRINT_ORDER_DISC_ARTICUL' => $row['articul'],
                    'PRINT_ORDER_DISC_COST' => $row['cost'],
                    'PRINT_ORDER_DISC_COUNT' => $row['count'],
                    'PRINT_ORDER_DISC_SUMM' => $row['summ'],
                    'MONEY3' => ($row['country']=='ua'?'грн.':'руб.')
                )
            );
            $countDisc += $row['count'];
            $this->tpl->parse('ITEM_ROW', '.item_row');
            
            $totalSumm += $row['summ'];
        }
        
        $this->tpl->assign(
            array(
                'PRINT_ORDER_NUMBER' => $order['id'],
                'PRINT_ORDER_DATE' => $this->convertDate($order['date']),
                'PRINT_ORDER_ZIP' => $order['zip'],
                'PRINT_ORDER_CITY' => $order['city'],
                'PRINT_ORDER_ADRES' => $order['adres'],
                'PRINT_ORDER_FIO' => $order['fullname'],
                'PRINT_ORDER_PASSPORT' => $order['passport'],
                'PRINT_ORDER_PHONE' => $order['phone'],
                'PRINT_ORDER_EMAIL' => $this->auth->email,
                'PRINT_ORDER_INFO' => $order['info'],
                'PRINT_ORDER_TOTAL_SUMM' => $totalSumm,
                'ADMIN_AMWAY_TEXT' => '',
                'ADMIN_AMWAY_NUMBER' => '',
                'DISC3'=>$countDisc.' шт.',
                'FOLDER'=>($order['country']=='ua'?'/ua':''),
                'MONEY3' => ($order['country']=='ua'?'грн.':'руб.')
                
            )
        );
        if ($order['country']=='ua'){
             $this->tpl->assign(
            array(
                
                'PRINT_BOTTOM_PHONES' => '+3 063 798 00 79'
            )
        );
        }
        return true;
	}
	
	
	private function partners2()
	{
            
	    $this->setMetaTags('Партнеры');
	    
		$user = null;
		
		$structure = array(
			'user' => 0,
			'platinum' => 1,
			'emerald' => 2,
			'diamond' => 3
		);
		
		if ($this->auth->type == 'user' && !$this->_isAdmin()) {
			return false;
		}
		
		if (isset($this->url[1]) && !empty($this->url[1])) {
			$user = (int) $this->url[1];
			
			if ($user < 1) {
				return false;
			}
		}
		
		if (null === $user) {
			$list = $this->db->fetchAll("SELECT * FROM `users` WHERE `type` = 'diamond'");
		} else {
			$current = $this->db->fetchRow("SELECT * FROM `users` WHERE `id` = '$user'");
			
			if (!$current) {
				return false;
			}
			
			if ($structure[$current['type']] > $structure[$this->auth->type] && !$this->_isAdmin()) {
				return false;
			}
			
			switch ($current['type']) {
				case 'diamond' : $type = 'emerald'; break;
				case 'emerald' : $type = 'platinum'; break;
				case 'platinum' : $type = 'user'; break;
				default : return false;
					break;
			}
			
			$list = $this->db->fetchAll("SELECT * FROM `users` WHERE `type` = '$type' AND `".$current['type']."` = '".$current['surname']."'");
			//echo "SELECT * FROM `users` WHERE `type` = '$type' AND `".$current['type']."` = '".$current['surname']."'";
		}
		
		$this->tpl->define_dynamic('partners', 'partners.tpl');
		$this->tpl->define_dynamic('partners_row', 'partners');
		
		//print_r($list);
		if ($list) {
    		foreach ($list as $row) {
    		    if (!isset($type)) {
    		        $type = 'emerald';
    		    }
    		    
    		    $this->tpl->assign(
                    array(
                        'PARTNER_ID' => $row['id'],
                        'PARTNER_NAME' => $row['surname'].' '.$row['name'],
                        'PARTNER_COUNT_SUB' => $this->db->fetchOne("SELECT COUNT(`id`) FROM `users` WHERE `type` = '$type' AND `".$row['type']."` = '".$row['surname']."'")
                    )
    		    );
    		    
    		    $this->tpl->parse('PARTNERS_ROW', '.partners_row');
    		}
    		
    		$this->tpl->parse('CONTENT', '.partners');
		} else {
		    $this->tpl->assign('CONTENT', 'Пользователей не найдено');
		}
		
		return true;
	}
	
	
    public function forgotpassword() {
    	$this->tpl->define_dynamic('_forgotpassword', 'users.tpl');
		$this->tpl->define_dynamic('forgotpassword', '_forgotpassword');
		
		$this->setMetaTags('Восстановление пароля');
        $this->setWay('Восстановление пароля');
		
        $email = $this->getVar('email', '');
		
		$users = null;
		
		if (!empty($_POST)) {
		    if (null === $email) {
		        $this->addErr('Введите email');
		    }  else {
		    	$user = $this->db->fetchAll("SELECT * FROM `users` WHERE `email` = '$email'");
		    	if (empty($user)) {
		    		$this->addErr('Пользователь с таким E-Mail: ('.$email.') не найден');
		    	}  	
		    	
		    }	    
			
		}
		
		$this->tpl->assign(array('EMAIL'=>$email));
		
		if (isset($user[0])) {
			$user = $user[0];
		}
		
		
		if (!empty($_POST) && !$this->_err) {
			$host = str_replace('www.', '', $_SERVER['HTTP_HOST']);		    
			$password = $this->paswordGen();
			
			$this->logger->setUser($user['name']);
			$this->logger->addLogRow('Восстановление пароля', serialize($user));
			
			$this->db->update('users', array('password'=>crypt($password, $this->_salt)), "id='".$user['id']."'");
			
		    $mail = new Zend_Mail('Windows-1251');
		    $mail->setBodyHtml("Пользователь ".$user['surname'] ." ".$user['name'] ." ".$user['patronymic']." изменил пароль.<br> Новый пароль:  $password");
		    $mail->setFrom('Upline24@'.$host, $host);
		    $mail->addTo($this->settings['feedback_email'], $user['name']);
		    $mail->setSubject("Оповещение о смене пользователем пароля");
		    $mail->send();

		
		    $mail = new Zend_Mail('Windows-1251');
		    $mail->setBodyHtml("Вы востановили пароль на сайте $host. <br>Логин: $email<br>Пароль: $password");
		    $mail->setFrom('Upline24@'.$host, $host);
		    $mail->addTo($email,$user['name']);
		    $mail->setSubject("Восстановление пароля");
		    $mail->send();
		    
		    
            $this->tpl->assign('CONTENT', '<div class="text_block">{NEW_PASSWORD_SUCCESS}</div><meta http-equiv="refresh" content="3;URL=/enter">');
		}
		
		if ($this->_err) {
		    $this->viewErr();
		}
		
		if (empty($_POST) || $this->_err) {
		    $this->tpl->parse('CONTENT', '.forgotpassword');
		}
		
		return true;
    }
        
    public function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
        unset($_SESSION['rememberMeGo']);
        SetCookie("rememberMe", "NO");
        SetCookie("ABRA", "");
        SetCookie("CODABRA", "");
        //var_dump($_COOKIE);
        //die;
        $this->redirect('/');
        
        exit;
    }
    
    public function remind()
    {
		global $money, $site;
        $this->tpl->define_dynamic('auth', 'auth.tpl');
        $this->tpl->define_dynamic('remind_password', 'auth');
        
        $email		= $this->getVar('email', '', $_POST);
        $code       = $this->getVar('code', '', $_POST);
        $captcha    = $this->getVar('code_confirm', null, $_SESSION);
        
        $error = '';
        
        if (!empty($_POST) && isset($_POST['remind'])) {
        	if (!$email || !$code || !$captcha) {
                $error .= '<p>Необходимо заполнить все поля</p>';
            }
            
            if (!$error) {
			    $validate = new Zend_Validate_EmailAddress();
			    if (!$validate->isValid($email)) {
			    	$error .= '<p>E-mail адрес введен неверно</p>';
			    }
			    
			    if ($captcha !== $code) {
			    	$error .= '<p>Неверный код подтверждения</p>';
			    }
            }
		    
		    if (!$error) {
                $user = $this->db->fetchRow("SELECT * FROM `users` WHERE `email` = '$email' AND `privilege` <> 'administrator'");
                if (!$user) {
                    $error .= '<p>E-mail адрес введен неверно</p>';
                }
            }
		}
		
		if (!empty($_POST) && isset($_POST['remind']) && !$error) {
		    $date = mktime();
		    
		    $remind_code = '';
		    $remind_code .= crypt($date, $this->_salt);
		    $remind_code .= crypt($email, $this->_salt);
		    $remind_code .= crypt(session_id(), $this->_salt);
		    
		    $prototype = array('q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', 'A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'Z', 'X', 'C', 'V', 'B', 'N', 'M', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
		    
            $temp = "";
            for ($i=0; $i<strlen($remind_code); $i++) {
                if (in_array($remind_code[$i], $prototype)) $temp .= $remind_code[$i];
            }
            
            $remind_code = $temp;
		    
		    $data = array(
                'remind_date' => $date,
                'remind_code' => $remind_code
		    );
		    
		    $this->logger->addLogRow('Запрос смены пароля', serialize($user));
		    
		    $n = $this->db->update('users', $data, "id = ".$user['id']);
		    
		    $subject = $this->getVar('remind_subject', 'Запрос восстановления пароля на сайте http://'.$_SERVER['HTTP_HOST'].'/', $this->settings);
            
            $body = $this->getVar('remind_message', '%RESTORE_LINK%', $this->settings);
            $body = str_replace("%RESTORE_LINK%", '<a href="http://'.$_SERVER['HTTP_HOST'].'/restore?code='.$remind_code.'">http://'.$_SERVER['HTTP_HOST'].'/restore?code='.$remind_code.'</a>', $body);
		    
		    $mail = new Zend_Mail('Windows-1251');
		    $mail->setBodyHtml($body);
		    $mail->setFrom('Upline24@'.$site[0][$_SESSION['countryDName']], 'Upline24');
		    $mail->addTo($email, $user['name'].' '.$user['surname']);
		    $mail->setSubject($subject);
		    $mail->send();
		    
		    $this->redirect('/remind-sent');
		    exit;
		}
		
		$this->tpl->assign('REMIND_ERROR', $error);
		
        $this->tpl->assign(
			array(
				'REMIND_EMAIL' => $email,
				'REMIND_CODE' => ''
			)
		);
		$this->tpl->parse('CONTENT', '.remind_password');
        
        return true;
    }
    
    public function restore()
    {
        global $money, $site;
        
        if (isset($this->url[1]) && !empty($this->url[1])) {
            return $this->error404();
        }
        
        $error = '';
        
        $id = $this->getVar('code');
        
        if (null === $id) {
        	return $this->error404();
        }
        
        $this->tpl->define_dynamic('auth', 'auth.tpl');
        $this->tpl->define_dynamic('restore_password', 'auth');
        
        if (!preg_match('/^[A-Za-z0-9]*$/', $id)) {
            return $this->error404();
        }
        
        $user = $this->db->fetchRow("SELECT * FROM `users` WHERE `remind_code` = '".$id."'");
        
        if (!$user) {
            return $this->error404();
        }
        
        if (mktime() - $user['remind_date'] > 3600) {
        	$error .= '<p>Код восстановления устарел. Воспользуйтесь <a href="/remind">формой восстановления пароля</a> заново</p>';
            
        	$this->tpl->assign('CONTENT', $error);
        	return true;
        }
        
        if (!empty($_POST)) {
            $password = $this->getVar('password', null, $_POST);
            $confirm = $this->getVar('confirm', null, $_POST);
            
            if ($password !== $confirm) {
                $error .= '<p>Введеные пароли не совпадают</p>';
            }
        }
        
        if (!empty($_POST) && !$error) {
            //$p = str_replace('@', '', $user['email']);
            //$p = substr($p, 0, 6);
            
            
            
            // Символы, которые будут использоваться в пароле.
            //$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
            // Количество символов в пароле.
            //$max=4;
            // Определяем количество символов в $chars
            //$size=StrLen($chars)-1;
            // Определяем пустую переменную, в которую и будем записывать символы.
            //$password=null;
            // Создаём пароль.
            /*
            while($max--)
            $password.=$chars[rand(0,$size)];
            
            
            
            $p = $p.$password;
            $data = array(
                'password' => crypt($p, $this->_salt),
                'remind_code' => ''
            );
             * 
             */
            $data = array(
                'password' => crypt($password, $this->_salt),
                'remind_code' => ''
            );
            
            $this->logger->addLogRow('Смена пароля', serialize($user));
            
            $n = $this->db->update('users', $data, "id = ".$user['id']);
         
            $subject = $this->getVar('restore_subject', 'Изменение на сайте: http://'.$_SERVER['HTTP_HOST'].'/', $this->settings);
                
           $body = $this->getVar('restore_message', '<strong>Логин:</strong> %LOGIN%<br /><strong>Новый пароль:</strong> %PASSWORD%', $this->settings);
           //$body = $this->getVar('restore_message', '<strong>Логин:</strong> %LOGIN%<br /><strong>Новый пароль:</strong> '.$p, $this->settings);
           $body = str_replace("%LOGIN%", $user['email'], $body);
           $body = str_replace("%PASSWORD%", $password, $body);
           //$body = str_replace("%PASSWORD%", $p, $body);
		    
		    $mail = new Zend_Mail('Windows-1251');
		    $mail->setBodyHtml($body);
		    $mail->setFrom('Upline24@'.$site[0][$_SESSION['countryDName']], 'Upline24');
		    $mail->addTo($user['email'], $user['name']);
		    $mail->setSubject($subject);
		    $mail->send();
		    
		    $this->redirect('/restore-succes');
                    //$this->edPageSys('restore-succes');
		    exit;
        }
        
        $this->tpl->assign('RESTORE_ERROR', $error);
        
        $this->tpl->assign(
			array(
				'RESTORE_CODE' => $id,
				'RESTORE_PASSWORD' => '',
				'RESTORE_CONFIRM' => ''
			)
		);
		            
		$this->tpl->parse('CONTENT', '.restore_password');
        
        return true;
    }
    public function mysettings ($form=''){
		if (isset($this->auth->id)){
			$user_id = $this->auth->id; 
		}		
        $adr = '<div id="adr_md_1" class="no_distinguish">
            <a href="#" onClick="slot(1, \'new\', '.($form=='form'?'1':'0').'); return false;" name="">
            <div class="ms_add_adr">
                <div class="ms_add_adr_inner_text">
                    <div class="ms_add_adr_img"><img src="/img/add_adr.gif"></div>
                    <div class="ms_add_adr_text">Добавить адрес</div>
                </div>
            </div>    
        </a></div>'; 
        $adr2 = '<div id="adr_md_2" class="no_distinguish">
            <a href="#" onClick="slot(2, \'new\', '.($form=='form'?'1':'0').'); return false;" name="">
            <div class="ms_add_adr">
                <div class="ms_add_adr_inner_text">
                    <div class="ms_add_adr_img"><img src="/img/add_adr.gif"></div>
                    <div class="ms_add_adr_text">Добавить адрес</div>
                </div>
            </div>    
        </a></div>';
        $adr3 = '<div id="adr_md_3" class="no_distinguish">
            <a href="#" onClick="slot(3, \'new\', '.($form=='form'?'1':'0').'); return false;" name="">
            <div class="ms_add_adr">
                <div class="ms_add_adr_inner_text">
                    <div class="ms_add_adr_img"><img src="/img/add_adr.gif"></div>
                    <div class="ms_add_adr_text">Добавить адрес</div>
                </div>
            </div>    
        </a></div>';
        $this->tpl->assign( array(
            'GLOBAL_USER_ID'=>'<input type="hidden" name="global_user_id" id="global_user_id" value="'.$user_id.'">',
            'GLOBAL_FORM_ID'=>'<input type="hidden" id="global_form" name="global_form" value="'.($form=='form'?'1':'0').'">',
                'ADR_1' => $adr,
                'ADR_2' => $adr2,
                'ADR_3' => $adr3,
                'CLASS_ACTIVE_RU'    =>  ($_SESSION['countryDName']=='ru'?' class="acrive"':' class="noacrive"'),
                'CLASS_ACTIVE_UA'    =>  ($_SESSION['countryDName']=='ua'?' class="acrive"':' class="noacrive"'),

            ));
        $listAdr = $this->db->fetchAll("SELECT * FROM `user_adr` WHERE `user_id` = '$user_id' AND `country`='".$_SESSION['countryDName']."' ORDER BY `slot`");
        $i=0;
        if (!empty($listAdr)) {
            
            foreach ($listAdr as $info) {
            $i++;    
//                $adr = '<div id="adr_md_'.$info['slot'].'" class="no_distinguish">
//                <a href="#" onClick="'.($form=='form'?'check_slot('.$info['slot'].'); return false;':'slot('.$info['slot'].', \'edit\', 0)').'" name="">                    
//                        <div class="ms_add_adr">
//                            <div class="ms_add_adr_content">
//                                <div class="ms_add_adr_name">'.(strlen($info['adr_name'])>25?substr($info['adr_name'],0,25).'...':$info['adr_name']).'</div>
//                                <div class="ms_add_adr_fio">'.$info['fio'].'</div>
//                                <div class="ms_add_adr_adr">'.$info['city'].', '.$info['adr'].'</div>
//                            </div>
//                            <a href="#" onClick="slot('.$info['slot'].', \'edit\', '.($form=='form'?'1':'0').'); return false;">
//                                <div class="ms_add_adr_inner_ed">
//                                    <div class="ms_edit_adr_eadr"><img src="/img/edit_adr.gif" ></div>
//                                    <div class="ms_edit_adr_etext">Редактировать адрес</div>
//                                </div>
//                            </a>
//                        </div>    
//                    </a></div>';
$adr = '<div id="adr_md_'.$info['slot'].'" class="no_distinguish">
                <a href="#" onClick="'.($form=='form'?'check_slot('.$info['slot'].'); return false;':'slot('.$info['slot'].', \'edit\', 0)').'" name="">                    
                    
                        <div class="ms_add_adr">
                            <div class="ms_add_adr_content">
                                <div class="ms_add_adr_name">'.(strlen($info['adr_name'])>25?substr($info['adr_name'],0,25).'...':$info['adr_name']).'</div>
                                <div class="ms_add_adr_fio">'.$info['fio'].'</div>
                                <div class="ms_add_adr_adr">'.$info['city'].', '.$info['adr'].'</div>
                            </div>
                            <span onClick="slot('.$info['slot'].', \'edit\', '.($form=='form'?'1':'0').'); return false;">
                                <div class="ms_add_adr_inner_ed">
                                    <div class="ms_edit_adr_eadr"><img src="/img/edit_adr.gif" ></div>
                                    <div class="ms_edit_adr_etext">Редактировать адрес</div>
                                </div>
                            </span>
                        </div>    
                    </a></div>';
                //echo $info['fio'];
                $this->tpl->assign( array(
                    'ADR_'.$info['slot'] => $adr,
                ));
            }
            
            
        }
        
        //echo $form;
        if ($form!='form') {
            $this->tpl->define_dynamic('mysettings', 'mysettings.tpl');
            $this->tpl->define_dynamic('list', 'mysettings');
            $this->tpl->parse('CONTENT', '.list');
            $this->edPageMeta('mysettings');
            $meta = $this->db->fetchRow("SELECT * FROM `meta_tags` WHERE `href` = 'mysettings'");
            $this->setMetaTags($meta);
        }
        
	
        
	return true;
    }
    protected function edPageMeta($href){
        $ed = $this->db->fetchRow("SELECT `ed1`,`ed2` FROM `meta_tags` WHERE `href` = '$href'");
        $this->tpl->assign( array(
            'ED1'=>($ed['ed1']!=''?$ed['ed1']:''),
            'ED2'=>($ed['ed2']!=''?$ed['ed2']:'')
            ));
        return true;
    }
    protected function edPageSys($href){
        $ed = $this->db->fetchRow("SELECT `ed1`,`ed2` FROM `system` WHERE `href` = '$href'");
        $this->tpl->assign( array(
            'ED1'=>($ed['ed1']!=''?$ed['ed1']:''),
            'ED2'=>($ed['ed2']!=''?$ed['ed2']:'')
            ));
        return true;
    }
    protected function edPageF($href){
        $ed = $this->db->fetchRow("SELECT `ed1`,`ed2` FROM `system` WHERE `href` = '$href'");
        $this->tpl->assign( array(
            'ED1'=>($ed['ed1']!=''?$ed['ed1']:''),
            'ED2'=>($ed['ed2']!=''?$ed['ed2']:'')
            ));
        return true;
    }
    
}