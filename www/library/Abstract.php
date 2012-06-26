<?php

require_once PATH . 'library/Templates.php';
abstract class Main_Abstract
{
    private $_config = null;
    
    private $_locale = null;
    
    private $_way = null;
    
    private $sepWay = ' <img src="/img/arrow.gif" width="3" height="3" alt="" /> ';
    
    protected $db = null;
    
    protected $tpl = null;
    
    protected $logger = null;
    
    protected $settings = null;
    
    protected $lookups = null;
    
    protected $auth = null;
    
    protected $url = array();
    
    protected $basePath = '';
    
    protected $lang = '';
    
    protected $getParam = array();
    
    protected $_err = '';
    
    protected $_salt = 'p3k272If';
    protected $_cookieLiveTime = 200000000;
    
    private $_allowModules = array(
                                'feedback',
                                'enter',
                                'registration',
                                'remind',
                                'restore',
                                'news',
                                'events',
                                'purchases',
                                'disc',
                                'mydiscs'
                                    );
                                    
    //private $allowedPassSymbol = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890$#^&*()_+\=-[]<>?/';
    private $allowedPassSymbol = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
    
    public $myCountry = '';
    
    public function __construct(array $config)
    {
        global $money, $site;
        if ($config instanceof Zend_Config) {
            $this->_config = $config->toArray();
        } else {
            if (!is_array($config)) {
                $config = (array) $config;
            }
            
            $this->_config = $config;
        }
        
        if (empty($this->_config)) {
            throw new Exception('Config cannot be empty!');
        }
        
        $this->_locale = new Zend_Locale('ru_RU');
        Zend_Date::setOptions(array('format_type' => 'php'));
        
        $this->getRegistry();
        
        $this->connectionToDatabase();
        $this->checkAuth();
        $this->loadTemplates();
        $this->loadSettings();
        $this->loadLookups();
        $this->loadMetaTags();
        
        $this->checkUser();
        
        $this->checkCountry();
        $this->activeMenu();
        $this->loadHorisontalMenu();
        
        $usersCount = $this->db->fetchOne("SELECT COUNT(`id`) FROM `users` WHERE `privilege` <> 'administrator'");
        
        if ($usersCount) {
            $this->tpl->assign('USERS_COUNT', number_format($usersCount, 0, '', ' '));
        } else {
            $this->tpl->assign('USERS_COUNT', 0);
        }
                
        $this->tpl->assign('SITE0', $site[0][$_SESSION['countryDName']]);
        $this->tpl->assign('SITE1', $site[1][$_SESSION['countryDName']]);
        $this->tpl->assign('SITE2', $site[2][$_SESSION['countryDName']]);
        $this->tpl->assign('SITE3', $site[3][$_SESSION['countryDName']]);
        $this->tpl->assign('SITE4', $site[4][$_SESSION['countryDName']]);
        $this->tpl->assign('SITE5', $site[5][$_SESSION['countryDName']]);
        $this->tpl->assign('SITE6', $site[6][$_SESSION['countryDName']]);
        $this->tpl->assign('SITE7', $site[7][$_SESSION['countryDName']]);
        
        $this->tpl->assign('MONEY0', $money[0][$_SESSION['countryDName']]);
        $this->tpl->assign('MONEY1', $money[1][$_SESSION['countryDName']]);
        $this->tpl->assign('MONEY2', $money[2][$_SESSION['countryDName']]);
        $this->tpl->assign('MONEY3', $money[3][$_SESSION['countryDName']]);
        $this->tpl->assign('MONEY4', $money[4][$_SESSION['countryDName']]);
        //echo $money[0]; die;
    }

    public function finalise()
    {
        //$this->tpl->assign('WAY', $this->_way);
        $this->tpl->assign('BASE_PATH', $this->basePath);
        //$this->tpl->assign('AUTH_BUTTON_ID', (!$this->_isAuth() ? '2' : ''));
        $this->tpl->parse('PAGE', 'page');
        $this->tpl->prnt();
    }
    
    public function error404()
    {
        $error = $this->db->fetchRow("SELECT `body`, `header`, `title`, `keywords`, `description` FROM `system` WHERE `href` = '404'");
        
        if (!$error) {
            throw new Exception('Unexpected Error');
        }
        
        $this->setMetaTags($error);
        $this->setWay($error['header']);
        
        $this->tpl->assign('CONTENT', stripslashes($error['body']));
        $this->edPageSysA('404');
        return true;
    }
    
    private function getRegistry()
    {
        $array = Zend_Registry::get('run');
        //print_r($array);
        //var_dump($this->auth);
        if (!is_array($array)) {
            throw new Exception('Unexpected Error: Base variables not defined');
        }
        
        foreach ($array as $key => $value) {
            $this->$key = $value;
            
            /*if (isset($this->$key)) {
                $this->$key = $value;
            } else {
                var_dump($this->$key);
                throw new Exception("Variable \"$key\" is not defined on Abstract Class!");
            }*/
        }
    }
    
    private function connectionToDatabase()
    {
        try {
            $database = Zend_Db::factory($this->_config['database']['adapter'], $this->_config['database']['params']);
            $database->getConnection();
        } catch (Zend_Db_Adapter_Exception $e) {
            throw new Exception('возможно, неправильные параметры соединения или СУРБД не запущена');
        } catch (Zend_Exception $e) {
            throw new Exception('возможно, попытка загрузки требуемого класса адаптера потерпела неудачу');
        }
        
        $this->db = $database;
    }
    
    private function checkAuth()
    {
        /*$auth = Zend_Auth::getInstance();
        
        if ($auth->hasIdentity()) {
            $this->auth = $auth->getIdentity();
        }*/
        //var_dump($this->auth);
    }
    
    private function loadTemplates()
    {
        if ($this->_isAuth()) {
            $templates = new Templates('tpl/auth/');
        } else {
            $templates = new Templates('tpl/no_auth/');
        }
        $templates->define_dynamic('page', "design.tpl");
        $templates->define_dynamic('null', 'page');
        
        $this->tpl = $templates;
        
        if ($this->_isAuth()) {
            $this->tpl->define_dynamic('menu_item', 'page');
        }
    }
    
    private function loadSettings()
    {
        if (null === $this->db) {
            throw new Exception('Database not initialised!');
        }
        
        $settings = $this->db->fetchAll("SELECT `key`, `value` FROM `settings`");
        
        if ($settings) {
            $this->settings = array();
            
            foreach ($settings as $set) {
                $this->settings[$set['key']] = $set['value'];
            }
        }
    }
    
    private function loadLookups()
    {
        $lookups = $this->db->fetchAssoc("SELECT * FROM `lookups` WHERE `language` = '".$this->lang."' ORDER BY `position`");
        
        if ($lookups) {
            $this->lookups = $lookups;
            foreach ($lookups as $item) {
                $this->lookups[$item['key']] = $item;
                $this->tpl->assign(strtoupper($item['key']), stripslashes($item['value']));
                
                if ($item['key'] == 'FIRST_WAY') {
                    $this->setWay($item['value'], 'http://' . $_SERVER['HTTP_HOST'] . '/');
                }
            }
        }
    }
    
    private function loadMetaTags()
    {
        if (in_array($this->url[0], $this->_allowModules)) {
            $meta = $this->db->fetchRow("SELECT * FROM `meta_tags` WHERE `href` = '".$this->url[0]."' AND `language` = '".$this->lang."'");
            
            if ($meta) {
                $this->setMetaTags($meta);
                $this->setWay(stripslashes($meta['header']), $this->basePath . $meta['href'] . '/');
                
                if ($this->url[0] == 'catalog' && isset($this->url[1])) {
                    $sub_meta = $this->db->fetchRow("SELECT * FROM `meta_tags` WHERE `href` = '".$this->url[1]."' AND `language` = '".$this->lang."'");
                    
                    if ($sub_meta) {
                        $this->setMetaTags($sub_meta);
                        $this->setWay(stripslashes($sub_meta['header']), $this->basePath . $meta['href'] . '/' . $sub_meta['href'] . '/');
                        $this->tpl->assign('CONTENT', '<div class="text_block">'.stripslashes($sub_meta['body']).'</div>');
                    }
                } else {
                    if (!isset($this->url[1])) {
                        if ($this->url[0] == 'registration') {
                            $this->tpl->assign('META_BODY', stripslashes($meta['body']));
                        } else {
                            $this->tpl->assign('CONTENT', '<div class="text_block">'.stripslashes($meta['body']).'</div>');
                        }
                    }
                }
            }
        }
    }
    
    private function checkUser()
    {
       //var_dump($_COOKIE); echo '!';
       if(isset($_COOKIE['rememberMe']) and $_COOKIE['rememberMe']=='YES'){

       $password=$_COOKIE['ABRA'];
       $email=$_COOKIE['CODABRA'];       
       $validate = new Zend_Validate_EmailAddress();
                    
       if ($validate->isValid($email)) {
                        
                        $authAdapter = new Zend_Auth_Adapter_DbTable($this->db);
            		    $authAdapter->setTableName('users');
            		    $authAdapter->setIdentityColumn('email');
            		    $authAdapter->setCredentialColumn('password');
            		    
            		    $authAdapter->setIdentity($email);
            		    $authAdapter->setCredential($password);
            		    
            		    $auth = Zend_Auth::getInstance();
            		    $result = $auth->authenticate($authAdapter);
            		    
            		    if ($result->isValid()) { 
            		        $data = $authAdapter->getResultRowObject(null, 'password');
            		        $auth->getStorage()->write($data);
                                if (isset($_SESSION['rememberMeGo']) and $_SESSION['rememberMeGo']!=1){
                                    $_SESSION['rememberMeGo']=1;
                                    $this->redirect('/news/');
                                    die;
                                }
                     }}}
        $this->tpl->assign('WELC_ERR', '');
        $this->tpl->assign('WELC_ERR2', '');
        
        if (null === $this->auth) {
            
            if ((!empty($_POST) && isset($_POST['auth'])) OR (!empty($_POST) && isset($_POST['mw_auth']))) {
                if (isset($_POST['mw_auth'])){
                    $email = $this->getVar('mw_email', null, $_POST);
                    $password = $this->getVar('mw_password', null, $_POST);
                    //Запомнить меня ("" или 1)
                    $rMe = $this->getVar('mw_remember_me', null, $_POST);
                } else {
                    $email = $this->getVar('email', null, $_POST);
                    $password = $this->getVar('password', null, $_POST);
                    //Запомнить меня ("" или 1)
                    $rMe = $this->getVar('remember_me', null, $_POST);
                }
                $_POST = null;
                
                if (null !== $email && null !== $password) {
                    
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
                                else {
                                    //Запомнить меня
                                    if ($rMe==1) { //die;
                                        //$_SESSION['rememberMe']="YES";
                                    //echo $this->_cookieLiveTime; die;
                                        SetCookie("rememberMe","YES",time()+$this->_cookieLiveTime);
                                        SetCookie("ABRA",crypt($password, $this->_salt),time()+$this->_cookieLiveTime);
                                        SetCookie("CODABRA",$email,time()+$this->_cookieLiveTime);
                                        //var_dump($_COOKIE);
                                        //die;
                                        
                                    }
                                    //header ('loacation:/news/');
                                    $this->redirect('/news/');
                                    
                                }

            		    }
                            else{
                                
                                /*$this->tpl->assign('WELC_ERR', '<div class="err_login_or_pass">неверный логин или пароль</div>
                            <div style="width:310px;  margin:0 auto;" >
                            <a href="/remind" style="float:left; color:#ff0000;" >Забыли пароль?</a> <a href="/feedback" style="float:right; color:#ff0000;" >Форма обратной связи</a></div><br><br>');*/
                                $this->tpl->assign('WELC_ERR2', $this->modalWin());
                                //$this->tpl->assign('CONTENT', '<center>неверный логин или пароль</center>');
                            }
                            
                    }
                    else{
                        
                        $this->tpl->assign('WELC_ERR2', $this->modalWin());
                        //$this->tpl->assign('CONTENT', '<center>неверный логин или пароль</center>');
                       
                         //$this->tpl->define_dynamic('_mwerr', 'mwerr.tpl');
            //$this->tpl->define_dynamic('err_mw', '_mwerr');
            
            //$mw_err = $this->tpl->prnt_to_var('err_mw');
            //var_dump($mw_err);
            //$this->tpl->parse('CONTENT', 'err_mw');
            
            //$this->tpl->parse('WELC_ERR', 'err_mw');
            //echo '<pre>';
            //var_dump($this->tpl->print_dtpl_name());
            //exit();
            
            //var_dump($this->tpl->prnt_to_var('err_mw'));
            //$this->tpl->assign('USER_FULL_NAME', $this->auth->name.' '.$this->auth->surname);
            
            //if ($this->auth->privilege === 'administrator') {
                //$this->tpl->parse('LOGIN_BLOCK', 'user_block');
            //} 
                    }                   
                }
            }
            
            $this->tpl->define_dynamic('auth', 'auth.tpl');
            $this->tpl->define_dynamic('auth_block', 'auth');
            
            if ($this->settings['use_auth'] != '1') {
                $this->tpl->assign('LOGIN_BLOCK', '');
            } else {
                $this->tpl->parse('LOGIN_BLOCK', 'auth_block');
            }
        } else {
            $this->tpl->define_dynamic('auth', 'auth.tpl');
            $this->tpl->define_dynamic('admin_block', 'auth');
            $this->tpl->define_dynamic('user_block', 'auth');
            $this->tpl->define_dynamic('auth_block', 'auth');
            
            $this->tpl->assign('USER_FULL_NAME', $this->auth->name.' '.$this->auth->surname);
            
            if ($this->auth->privilege === 'administrator') {
                $this->tpl->parse('LOGIN_BLOCK', 'user_block');
            } else {
                $this->tpl->parse('ADMIN_MAIN_MENU', 'null');
                
                if ($this->auth->type == 'user') {
                	$this->tpl->parse('USER_MAIN_MENU_PARTNERS', 'null');
                }
                
                if ($this->auth->status != '1') {
                    $this->tpl->parse('USER_MAIN_MENU', 'null');
                }
                
                if ($this->settings['use_auth'] != '1') {
                    $this->tpl->assign('LOGIN_BLOCK', '');
                } elseif ($this->auth->status === 2) {
                    $this->tpl->parse('LOGIN_BLOCK', 'auth_block');
                } else {
                    $this->tpl->parse('LOGIN_BLOCK', 'user_block');
                }
            }
        }
        if ($this->url[0]=='news' and (!isset($this->auth->status) or $this->auth->status<=0)){
                $this->tpl->parse('LOGIN_BLOCK', 'auth_block');
                $_SESSION['user_go_to_news']='/'.$this->url[0].'/'.(!empty($this->url[1])?$this->url[1].'/':'');
                $this->redirect('/enter');
            die;
        }
        return;
        
        $auth = Zend_Auth::getInstance();
        
        if ($auth->hasIdentity()) {
            //$this->auth = $auth->getIdentity();
            
            $this->tpl->define_dynamic('auth', 'auth.tpl');
            $this->tpl->define_dynamic('admin_block', 'auth');
            $this->tpl->define_dynamic('user_block', 'auth');
            $this->tpl->define_dynamic('auth_block', 'auth');
            
            if ($this->auth->privilege === 'administrator') {
                $this->tpl->parse('LOGIN_BLOCK', 'admin_block');
            } else {
                if ($this->settings['use_auth'] != '1') {
                    $this->tpl->assign('LOGIN_BLOCK', '');
                } elseif ($this->auth->status === 2) {
                    $this->tpl->parse('LOGIN_BLOCK', 'auth_block');
                } else {
                    $this->tpl->parse('LOGIN_BLOCK', 'user_block');
                }
            }
        } else {
            if (!empty($_POST) && isset($_POST['auth'])) {
                $email = $this->getVar('email', null, $_POST);
                $password = $this->getVar('password', null, $_POST);
                
                
                $_POST = null;
                
                if (null !== $email && null !== $password) {
                    $validate = new Zend_Validate_EmailAddress();
                    if ($validate->isValid($email)) {
                        $authAdapter = new Zend_Auth_Adapter_DbTable($this->db);
            		    $authAdapter->setTableName('users');
            		    $authAdapter->setIdentityColumn('email');
            		    $authAdapter->setCredentialColumn('password');
            		    
            		    $authAdapter->setIdentity($email);
            		    $authAdapter->setCredential(crypt($password, $this->_salt));
            		    
            		    //$auth = Zend_Auth::getInstance();
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
            		    }
                    }
                }
            }
            
            $this->tpl->define_dynamic('auth', 'auth.tpl');
            $this->tpl->define_dynamic('auth_block', 'auth');
            if ($this->settings['use_auth'] != '1') {
                $this->tpl->assign('LOGIN_BLOCK', '');
            } else {
                $this->tpl->parse('LOGIN_BLOCK', 'auth_block');
            }
        }
    }
    
    private function loadHorisontalMenu()
    {
        if (!$this->_isAuth()) {
            $this->tpl->parse('MENU_ITEM', 'null');
            return true;
        }
        
        if (!$this->_isAdmin()) {
            if ($this->auth->status != '1') {
                $this->tpl->parse('MENU_ITEM', 'null');
                return true;
            }
        }
        
        $menu = $this->db->fetchAll("SELECT `href`, `header`, `type` FROM `page` WHERE `menu` = 'yes' AND `visibility` = '1' ORDER BY `position` DESC, `header` ASC");
        //echo $this->url[0];
        foreach ($menu as $row) {
            $this->tpl->assign(
                array(
                    'MENU_ITEM_HREF' => ($row['type'] == 'link' ? '' : '/').$row['href'],
                    'MENU_ITEM_HEADER' => $row['header'],
                    'MENU_ITEM_ACTIVE'=>(($this->url[0]==str_replace('/', '', $row['href']))?' class="active" ':'')
                )
            );
            
            $this->tpl->parse('MENU_ITEM', '.menu_item');
        }
        
        if (sizeof($menu) < 1) {
            $this->tpl->parse('MENU_ITEM', 'null');
        }
    }
    
    protected function _isAdmin()
    {
        if (null === $this->auth) {
            return false;
        }
        
        if (!isset($this->auth->privilege) || $this->auth->privilege !== 'administrator') {
            return false;
        }
        
        return true;
    }
    
    protected function _isAuth()
    {    
        if (null === $this->auth) {
        	
            return false;
        }
        
        if (!isset($this->auth->privilege)) {
        	
            return false;
        }    
        
        return true;
    }
    
    protected function redirect($url = '', $isHeader = true, $time = 0)
    {
        if (!$url) {
            return false;
        }
        
        if (!headers_sent() && $isHeader) {
            header('Location: '.$url);
        } else {
            print "<meta http-equiv='refresh' content='$time;URL=$url'>";
            
            if ($isHeader) {
                exit;
            }
        }
    }
    
    protected function generatePassword($length = 4)
    {
        if (!is_int($length)) {
            $length = (int) $length;
        }
        
        if ($length < 3) {
            $length = 7;
        }
        
        $password = '';
            
        for ($i=0; $i<$length; $i++) {
            $password .= $this->allowedPassSymbol{mt_rand(0,strlen($this->allowedPassSymbol)-1)};
        }
        
        return $password;
    }
    
    protected function checkRegisterPassword($password = null, $confirm = null)
    {
        if (null === $password) {
            return $this->addErr('Поле "Пароль" не заполнено', true);
        }
        
        if (null === $confirm) {
            return $this->addErr('Поле "Подтверждение пароля" не заполнено', true);
        }
        
        if (preg_match("/([\s])/", $password)) {
            return $this->addErr('Поле "Пароль" содержит недопустимые символы', true);
        }
        
        if ($password !== $confirm) {
            return $this->addErr('Введенные пароли не совпадают', true);
        }
        
        if (strlen($password) < 5) {
            return $this->addErr('Пароль не может быть короче 5-ти символов', true);
        }
        
        $strong = 0;
        if (preg_match("/([0-9]+)/", $password)) {
            $strong++;
        }
        
        if (preg_match("/([a-z]+)/", $password)) {
            $strong++;
        }
        
        if (preg_match("/([A-Z]+)/", $password)) {
            $strong++;
        }
        
        if (preg_match("/([а-я]+)/", $password)) {
            $strong++;
        }
        
        if (preg_match("/([А-Я]+)/", $password)) {
            $strong++;
        }
        
        if (preg_match("/\W/", $password)) {
            $strong++;
        }
        
        if (preg_match("/([a-z]+)/", $password)) {
            $strong++;
        }
        
        switch ($strong) {
            //case 1: $this->addErr('Очень простой'); break;
//            case 2: $this->addErr('Простой'); break;
//            case 3: 
//            case 4: $this->addErr('Нормальный'); break;
//            case 5: $this->addErr('Надежный'); break;
//            case 6: 
//            case 7: $this->addErr('Очень надежный'); break;
//            default : return $this->addErr('Введеный "Пароль" очень простой. Введите более сложный пароль'); break;
        }
        
        return true;
    }
    
    protected function setMetaTags($meta = null)
    {
        if (null === $meta) {
            return true;
        }
        
        if (is_array($meta)) {
            $this->tpl->assign(
                array(
                    'TITLE' => stripslashes($meta['title']),
                    'KEYWORDS' => isset($meta['keywords']) ? stripslashes($meta['keywords']) : stripslashes($meta['title']),
                    'DESCRIPTION' => isset($meta['description']) ? stripslashes($meta['description'])  : stripslashes($meta['title']),
                    'HEADER' => isset($meta['header']) ? stripslashes($meta['header']) : stripslashes($meta['title'])
                )
            );
        } else {
            $this->tpl->assign(
                array(
                    'TITLE' => $meta,
                    'KEYWORDS' => $meta,
                    'DESCRIPTION' => $meta,
                    'HEADER' => $meta
                )
            );
        }
    }
    
    protected function setMetaTagsAdmin($meta = null, $admin = '')
    {
        if (null === $meta) {
            return true;
        }
        
        if (is_array($meta)) {
            $this->tpl->assign(
                array(
                    'TITLE' => stripslashes($meta['title']),
                    'KEYWORDS' => isset($meta['keywords']) ? stripslashes($meta['keywords']) : stripslashes($meta['title']),
                    'DESCRIPTION' => isset($meta['description']) ? stripslashes($meta['description'])  : stripslashes($meta['title']),
                    'HEADER' => $admin.(isset($meta['header']) ? stripslashes($meta['header']) : stripslashes($meta['title']))
                )
            );
        } else {
            $this->tpl->assign(
                array(
                    'TITLE' => $meta,
                    'KEYWORDS' => $meta,
                    'DESCRIPTION' => $meta,
                    'HEADER' => $admin.$meta
                )
            );
        }
    }
    
    protected function setWay($title = null, $url = null)
    {    	
        if (null === $title || empty($title)) {
            return true;
        }
        
        $sep = ($this->_way) ? $this->sepWay : '';
        
        if (null === $url) {
            $this->_way .= $sep . stripslashes($title);
        } else {
            $this->_way .= ($sep . '<a href="'.$url.'">'.stripslashes($title).'</a>');
        }
    }
    
    protected function getAdminAdd($target = null, $id = null)
    {
        if (!$this->_isAdmin()) {
            return '';
        }
        
        if (null === $target) {
            return '';
        }
        
        $admin = '';
        
        if ($target == 'news') {
            $admin .= '<a href="'.$this->basePath.'admin/addnews/" title="Добавить новость"><img src="/img/admin_icons/add_page.png" border="0" alt="Добавить новость"></a>';
        } elseif ($target == 'disc') {
            $admin .= '<a href="'.$this->basePath.'admin/adddisc/" title="Добавить диск"><img src="/img/admin_icons/add_page.png" border="0" alt="Добавить диск"></a>';
        } elseif ($target == 'hall') {
            $admin .= '<a href="'.$this->basePath.'admin/addhall/" title="Добавить зал"><img src="/img/admin_icons/add_page.png" border="0" alt="Добавить зал"></a>&nbsp;';
        } elseif($target == 'events') {
            $admin .= '<a href="'.$this->basePath.'admin/addevent/" title="Добавить семинар"><img src="/img/admin_icons/add_page.png" border="0" alt="Добавить семинар"></a>&nbsp;';
        } else {
            if (null === $id) {
                return '';
            }
            
            $admin .= '<a href="'.$this->basePath.'admin/addsection/'.$id.'" title="Создать раздел"><img src="/img/admin_icons/add_group.png" border="0" alt="Создать раздел"></a>&nbsp;';
            $admin .= '<a href="'.$this->basePath.'admin/addpage/'.$id.'" title="Создать страницу"><img src="/img/admin_icons/add_page.png" border="0" alt="Создать страницу"></a>&nbsp;';
            $admin .= '<a href="'.$this->basePath.'admin/addlink/'.$id.'" title="Создать ссылку"><img src="/img/admin_icons/add_link.png" border="0" alt="Создать ссылку"></a>';
        }
        
        return '<div class="type_block_admin">'.$admin.'</div><br clear="all">';
    }
    
    protected function getAdminEdit($target = null, $id = null, $hasDelete = true)
    {
        if (!$this->_isAdmin()) {
            return '';
        }
        
        if (null === $target || null === $id) {
            return '';
        }
        
        $admin = '<a href="'.$this->basePath.'admin/edit'.$target.'/'.$id.'" title="Редактировать"><img src="/img/admin_icons/edit.png" width="12" height="12" alt="Редактировать" /></a>';
        
        if ($hasDelete) {
            $admin .= '&nbsp;&nbsp;';
            $admin .= '<a href="'.$this->basePath.'admin/delete'.$target.'/'.$id.'" title="Удалить" onClick="return confirm(\'Вы уверены что хотите удалить?\'); return false;"><img src="/img/admin_icons/delete.png" width="12" height="12" alt="Удалить" /></a>&nbsp;&nbsp;';
        }
        
        return $admin;
    }
    
    protected function getFullUri($page = null)
    {
        if (null === $page) {
            return '';
        }
        
        $uri = '/';
        
        if ($page['level'] == '0') {
            return $uri;
        }
        
        $level = $page['level'];
        
        while ($level != 0) {
            $tmp = $this->db->fetchRow("SELECT `href`, `level` FROM `page` WHERE `id` = '$level'");
            
            if (!$tmp) {
                die();
            }
            
            $uri = '/'.$tmp['href'].$uri;
            $level = $tmp['level'];
        }
        
        return $uri;
    }
    
    protected function convertDate($date = null, $format = "d.m.Y")
    {
        if (null === $date || !is_numeric($date)) {
            $date = mktime();
        }
                
        $d = new Zend_Date($date, false, $this->_locale);
        
        return $d->toString($format);
    }
    
    protected function loadPaginator($allPages = null, $pages = null, $url = null)
    { 
        if (null === $allPages || null === $pages || null === $url) {
            return '';
        }
        
        if ($pages > $allPages) {
            $pages = 1;
        }
        
        $symbol = (eregi("[?]", $url) ? '&' : '?');
        
        $start = $pages - 8;
        
        
        if ($start < 1) {
            $start = 1;
        }
        
        $end = $pages + 8;
        
        if ($end > $allPages) {
            $end = $allPages;
        }
        
        $navbar = '<div class="pager">';
        
        
        if ($pages > 1) {
            $navbar .= '<a href="'.$url.'">Первая</a>';
            $navbar .= '<span class="pad"><a href="'.$url.((($pages-1) == 1)?(""):($symbol."page=".($pages-1))).'">««</a></span>';
        }
        
        for ($i=$start; $i<=$end; $i++) {
            if ($pages == $i) {
                $navbar .= '<span>'.$i.'</span>';
            } elseif ($i > 1) {
                $navbar .= '<a href="'.$url.$symbol."page=".$i.'">'.$i.'</a>';
            } else {
                $navbar .= '<a href="'.$url.'">'.$i.'</a>';
            }
            
            if ($i < $end) {
                $navbar .= '  ·  ';
            }
        }
        
        if ($pages < $allPages) {
            $navbar .= '<span class="pad"><a href="'.$url.$symbol."page=".($pages+1).'">»»</a></span>';
            $navbar .= '<a href="'.$url.$symbol."page=".$allPages.'">Последняя</a>';
        }
        
        $navbar .= "<br /><br /></div>";
        
        return $navbar;
    }
    
    protected function ru2Lat($str)
    {
        $rus = array('ё','ж','ц','ч','ш','щ','ю','я','Ё','Ж','Ц','Ч','Ш','Щ','Ю','Я', 'Ї', 'ї', 'Є', 'є', 'І', 'і');
        $lat = array('yo','zh','tc','ch','sh','sh','yu','ya','YO','ZH','TC','CH','SH','SH','YU','YA', 'YI', 'yi', 'E', 'e', 'I', 'i');
        $prototype = array('q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', 'A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L', 'Z', 'X', 'C', 'V', 'B', 'N', 'M', '-', '_', ' ', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', ':', '/', '.', '?', '&');
        
        /*if ($type == 'link') {
            array_push($prototype, ':', '/', '.', '?', '&');
        }*/
        
        $str = str_replace($rus,$lat,$str);
        $str = strtr($str,
        "АБВГДЕЗИЙКЛМНОПРСТУФХЪЫЬЭабвгдезийклмнопрстуфхъыьэ",
        "ABVGDEZIJKLMNOPRSTUFH_I_Eabvgdezijklmnoprstufh_i_e");
        
        $size = strlen($str);
        
        $temp = "";
        for ($i=0; $i<$size; $i++) {
            if(in_array($str[$i], $prototype)) $temp .= $str[$i];
        }
        
        $str = $temp;
        
        $str = str_ireplace(' ', '-', $str);
        
        return (strtolower($str));
    }
    
    protected function getVar($key = null, $def = null, $array = null)
    {
        if (null !== $array) {
            if (!isset($array)) {
                return $def;
            }
            
        	if (!get_magic_quotes_gpc()) {
        	    
	        	if (isset($array[$key])) {
	        		if (is_string($array[$key])) {
	        			return addslashes(trim($array[$key]));
	        		} else {
	        			return $array[$key];
	        		}
	        	} else {
	        		return $def;
	        	}
        	}
        	if (isset($array[$key])) {
        		if (is_string($array[$key])) {
        			return trim($array[$key]);
        		} else {
        			return $array[$key];
        		}
        	} else {
        		return $def;
        	}
            return isset($array[$key]) && $array[$key] ? addslashes(trim($array[$key])) : $def;
        }
     
        
        if (isset($_POST[$key])) {
        	if (!get_magic_quotes_gpc()) {
        		return $_POST[$key]  || (is_numeric($_POST[$key])) ? trim($_POST[$key]) : $def;
        	}
            return $_POST[$key]  || (is_numeric($_POST[$key])) ? addslashes(trim($_POST[$key])) : $def;
        } elseif (isset($_GET[$key])) {
        	if (!get_magic_quotes_gpc()) {
        		return $_GET[$key] ? trim($_GET[$key]) : $def;
        	}
            return $_GET[$key] ? addslashes(trim($_GET[$key])) : $def;
        } elseif (isset($_FILES[$key])) {        	
            return $_FILES[$key] ? $_FILES[$key] : $def;
        } elseif (isset($_SERVER[$key])) {
        	if (!get_magic_quotes_gpc()) {
        		return $_SERVER[$key] ? $_SERVER[$key] : $def;
        	}
            return $_SERVER[$key] ? addslashes($_SERVER[$key]) : $def;
        } else {
            return $def;
        }
        
    }
    
    protected function checkPhone($phone)
    {
        $search = '/[^0-9]/';
        $replace = '';
        
        $phone = preg_replace($search, $replace, $phone);
        
        return '+'.$phone;
    }
    
    
    protected function generateCode()
    {
        $code = mt_rand(10000,99999999);
        
        $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `tickets` WHERE `code` = '$code'");
        
        if ($count != 0) {
            return $this->generateCode();
        }
        
        return $code;
    }
    
    protected function generateScannerCode()
    {
        $code = mt_rand(10000000,99999999999);
        
        $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `tickets_code` WHERE `code` = '$code'");
        
        if ($count != 0) {
            return $this->generateScannerCode();
        }
        
        return $code;
    }
    
    
    public function numberToString($number)
    {
		global $money, $site;
        $_1_2[1]="одна ";
        $_1_2[2]="две ";
         
        $_1_19[1]="один ";
        $_1_19[2]="два ";
        $_1_19[3]="три ";
        $_1_19[4]="четыре ";
        $_1_19[5]="пять ";
        $_1_19[6]="шесть ";
        $_1_19[7]="семь ";
        $_1_19[8]="восемь ";
        $_1_19[9]="девять ";
        $_1_19[10]="десять ";
        $_1_19[11]="одиннацать ";
        $_1_19[12]="двенадцать ";
        $_1_19[13]="тринадцать ";
        $_1_19[14]="четырнадцать ";
        $_1_19[15]="пятнадцать ";
        $_1_19[16]="шестнадцать ";
        $_1_19[17]="семнадцать ";
        $_1_19[18]="восемнадцать ";
        $_1_19[19]="девятнадцать ";
         
        $des[2]="двадцать ";
        $des[3]="тридцать ";
        $des[4]="сорок ";
        $des[5]="пятьдесят ";
        $des[6]="шестьдесят ";
        $des[7]="семьдесят ";
        $des[8]="восемдесят ";
        $des[9]="девяносто ";
        
        $hang[1]="сто ";
        $hang[2]="двести ";
        $hang[3]="триста ";
        $hang[4]="четыреста ";
        $hang[5]="пятьсот ";
        $hang[6]="шестьсот ";
        $hang[7]="семьсот ";
        $hang[8]="восемьсот ";
        $hang[9]="девятьсот ";
        
        $namerub[1]="целая ";
        $namerub[2]=$money[1][$_SESSION['countryDName']]." ";//"рубля ";
        $namerub[3]=$money[2][$_SESSION['countryDName']]." ";//"рублей ";
        
        $nametho[1]="тысяча ";
        $nametho[2]="тысячи ";
        $nametho[3]="тысяч ";
        
        $namemil[1]="миллион ";
        $namemil[2]="миллиона ";
        $namemil[3]="миллионов ";
        
        $namemrd[1]="миллиард ";
        $namemrd[2]="миллиарда ";
        $namemrd[3]="миллиардов ";
        
        //$kopeek[1]="сотая ";
        //$kopeek[2]="сотых ";
        //$kopeek[3]="сотых ";
        
        $L = $number;
        
        $s=" ";
        $s1=" ";
        $s2=" ";
        $kop=intval( ( $L*100 - intval( $L )*100 ));
        $L=intval($L);
        if($L>=1000000000){
            $many=0;
            $this->semantic(intval($L / 1000000000),$s1,$many,3);
            $s.=$s1.$namemrd[$many];
            $L%=1000000000;
        }
     
        if($L >= 1000000){
            $many=0;
            $this->semantic(intval($L / 1000000),$s1,$many,2);
            $s.=$s1.$namemil[$many];
            $L%=1000000;
            if($L==0){
                $s.=$money[2]." ";//"рублей ";
            }
        }
     
        if($L >= 1000){
            $many=0;
            $this->semantic(intval($L / 1000),$s1,$many,1);
            $s.=$s1.$nametho[$many];
            $L%=1000;
            if($L==0){
                $s.=$money[2]." ";//"рублей ";
            }
        }
     
        if($L != 0){
            $many=0;
            $this->semantic($L,$s1,$many,0);
            $s.=$s1.$namerub[$many];
        }
     
        if($kop > 0){
            $many=0;
            $this->semantic($kop,$s1,$many,1);
            $s.=$s1.$kopeek[$many];
        }
        else {
            $s.=" 00 копеек";
        }
     
        return $s;
    }
    
    private function semantic($i,&$words,&$fem,$f){
		global $money, $site;
        $_1_2[1]="одна ";
        $_1_2[2]="две ";
         
        $_1_19[1]="один ";
        $_1_19[2]="два ";
        $_1_19[3]="три ";
        $_1_19[4]="четыре ";
        $_1_19[5]="пять ";
        $_1_19[6]="шесть ";
        $_1_19[7]="семь ";
        $_1_19[8]="восемь ";
        $_1_19[9]="девять ";
        $_1_19[10]="десять ";
        $_1_19[11]="одиннацать ";
        $_1_19[12]="двенадцать ";
        $_1_19[13]="тринадцать ";
        $_1_19[14]="четырнадцать ";
        $_1_19[15]="пятнадцать ";
        $_1_19[16]="шестнадцать ";
        $_1_19[17]="семнадцать ";
        $_1_19[18]="восемнадцать ";
        $_1_19[19]="девятнадцать ";
         
        $des[2]="двадцать ";
        $des[3]="тридцать ";
        $des[4]="сорок ";
        $des[5]="пятьдесят ";
        $des[6]="шестьдесят ";
        $des[7]="семьдесят ";
        $des[8]="восемдесят ";
        $des[9]="девяносто ";
        
        $hang[1]="сто ";
        $hang[2]="двести ";
        $hang[3]="триста ";
        $hang[4]="четыреста ";
        $hang[5]="пятьсот ";
        $hang[6]="шестьсот ";
        $hang[7]="семьсот ";
        $hang[8]="восемьсот ";
        $hang[9]="девятьсот ";
        
        $namerub[1]="целая ";
        $namerub[2]=$money[1][$_SESSION['countryDName']]." ";//"рубля ";
        $namerub[3]=$money[2][$_SESSION['countryDName']]." ";//рублей ";
        
        $nametho[1]="тысяча ";
        $nametho[2]="тысячи ";
        $nametho[3]="тысяч ";
        
        $namemil[1]="миллион ";
        $namemil[2]="миллиона ";
        $namemil[3]="миллионов ";
        
        $namemrd[1]="миллиард ";
        $namemrd[2]="миллиарда ";
        $namemrd[3]="миллиардов ";
        
        //$kopeek[1]="сотая ";
        //$kopeek[2]="сотых ";
        //$kopeek[3]="сотых ";
        
        //global $_1_2, $_1_19, $des, $hang, $namerub, $nametho, $namemil, $namemrd;
        
        $words="";
        $fl=0;
        if($i >= 100){
            $jkl = intval($i / 100);
            $words.=$hang[$jkl];
            $i%=100;
        }
        if($i >= 20){
            $jkl = intval($i / 10);
            $words.=$des[$jkl];
            $i%=10;
            $fl=1;
        }
        switch($i){
            case 1: $fem=1; break;
            case 2:
            case 3:
            case 4: $fem=2; break;
            default: $fem=3; break;
        }
        if( $i ){
            if( $i < 3 && $f > 0 ){
                if ( $f >= 2 ) {
                    $words.=$_1_19[$i];
                }
                else {
                    $words.=$_1_2[$i];
                }
            }
            else {
                $words.=$_1_19[$i];
            }
        }
    }
    
    
    protected function addErr($str = null, $returnVal = false)
    {
        if (null !== $str)
        {
            if ($this->_err != '')
            {
                $this->_err .= '<br />';
            }
            
            $this->_err .= $str;
        }
        
        if ($returnVal) {
            return false;
        }
    }
    
    protected function viewErr() {
        if ($this->_err != '')
        {
            $this->tpl->define_dynamic('_err', 'err.tpl');
            $this->tpl->define_dynamic('err', '_err');
            $this->tpl->assign('ERR', $this->_err);
            $this->tpl->parse('CONTENT', '.err');
            //return "<div style='color: red; font: Bold 11px Tahoma; padding: 0 0 35px 0;'>".$this->_err.'</div>';
        }
        
        return true;
	}
    
	protected function viewMessage($message) {
	    $this->tpl->define_dynamic('_err', 'err.tpl');
        $this->tpl->define_dynamic('mess', '_err');
        $this->tpl->assign('ERR', $message);
        $this->tpl->parse('CONTENT', '.mess');
	}
        
        protected function activeMenu() {
        //var_dump($this->url[0]); die;
        $this->tpl->assign(
                array(
                    'ACTIVE_S' => (($this->url[0]=='events' OR $this->url[0]=='viewevent') ?' class="act_m" ':''),
                    'ACTIVE_D' => (($this->url[0]=='disc' OR $this->url[0]=='viewdisc')?' class="act_m" ':''),
                    'ACTIVE_P' => (($this->url[0]=='partners')?' class="act_m" ':''),
                    'ACTIVE_DOC' => (($this->url[0]=='documents')?' class="act_m" ':''),
                    'ACTIVE_T' => (($this->url[0]=='purchases')?' class="act_m" ':''),
                    'ACTIVE_DISC' => (($this->url[0]=='mydiscs')?' class="act_m" ':''),
                    'ACTIVE_MS' => (($this->url[0]=='mysettings')?' class="act_m" ':''),
                )
            );
        
        $this->tpl->assign( array(
            'ED1'=>'',
            'ED2'=>''
            ));
	}
        
    protected function checkCountry(){
       
        //var_dump($_SESSION); die;
        //echo $_SESSION['privilege']; die;
        if (isset($_GET['country'])){
            if ($_GET['country']=='ua'){
                $_SESSION['countryDName']='ua';
            } else {
                $_SESSION['countryDName']='ru';
            }
        } else {
            if (!isset($_SESSION['countryDName']) or empty($_SESSION['countryDName']))
                $_SESSION['countryDName']='ru';
        }
        if ($_SESSION['countryDName']!='ru' and $_SESSION['countryDName']!='ua'){
            $_SESSION['countryDName']='ru';
        }
        //$_SESSION['country']['nameDName'] ='123';

        $this-> addInfoByCountry();
        
    }
    protected function addInfoByCountry(){
        if ($_SESSION['countryDName']=='ua') { 
            $_SESSION['countryName'] = 'Украина';
        } else {
            $_SESSION['countryName'] = 'Россия';
        }
        
    }
    protected function modalWin() {
        
    //your bunny wrote        
        $content = '<div id="boxes">
<div id="dialog" class="window">
<a href="#" class="close"/><img src="/img/modal/close.jpg"></a>
<div class="mw_context">
    <form method="post" action="/">
<table>
    <tr>
        <td colspan="2">    
            <div class="mw_err_m">Введен неверный электронный адрес или пароль</div>
        </td>
    </tr>
    <tr>
        <td colspan="2">    
            <div class="mw_text">Электронный адрес</div>
        </td>
    </tr>
    <tr>
        <td colspan="2">     
            <input class="mw_input" type="text" name="mw_email">
        </td>
    </tr>            
    <tr>
        <td colspan="2">    
            <div class="mw_text">Пароль</div>
        </td>
    </tr>
    <tr>
        <td colspan="2">    
            <input class="mw_input" type="password" name="mw_password">
        </td>
    </tr>
    <tr>
        <td colspan="2">  
            <div class="mw_rem"><table><tr><td><input id="remember_me"  type="checkbox" value="1" name="mw_remember_me"></td><td><label for="remember_me">&nbsp;&nbsp;Запомнить меня</label></td></tr></table></div>
        </td>
    </tr>
    <tr>
        <td class="mw_sub" colspan="2">    
            <input type="submit" value="Войти" class="mw_submit2" name="mw_auth" />
        </td>
    </tr>
    <tr>
        <td><a class="mw_link" href="">Сообщить о проблеме</a></td>
        <td><a class="mw_link mw_link_right" href="/remind">Забыли пароль?</a></td>
    </tr>
</table>   
        </form>
</div>
</div>

  <div id="mask"></div>

</div>';
        return $content;
        
    }
        protected function edPageSysA($href){
        $ed = $this->db->fetchRow("SELECT `ed1`,`ed2` FROM `system` WHERE `href` = '$href'");
        $this->tpl->assign( array(
            'ED1'=>($ed['ed1']!=''?$ed['ed1']:''),
            'ED2'=>($ed['ed2']!=''?$ed['ed2']:'')
            ));
        return true;
    }
}