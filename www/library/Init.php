<?php

class Init
{
    private $_config = null;
    
    private $_url = null;
    
    private $_basePath = null;
    
    private $_lang = null;
    
    private $_getParam = null;
    
    private $_auth = null;
    
    public function __construct($config)
    {
        if ($config instanceof Zend_Config) {
            $this->_config = $config->toArray();
        } else {
            if (!is_array($config)) {
                $config = (array) $config;
            }
            
            $this->_config = $config;
        }
        
        $this->modRewrite();
        $this->checkAuth();
        
        
        
        $this->dispatch();
    }
    
    private function dispatch()
    {
        $dir = PATH . 'library/';
        $fileName = $dir . 'Content' . '.php';
        $className = 'Content';
        $action = $this->_url[0];
        
        if (null !== $this->_auth) {
            if ($this->_auth->privilege !== 'administrator') {
                if ($action != 'logout' && $action != 'feedback') {
                    switch ($this->_auth->status) {
                        case '0' :
                            $className = 'Content';
                            $action = 'banned';
                            $this->_url = array('banned');
                            break;
                        case '1' :
                            break;
                        case '2' :
                            $className = 'Content';
                            $action = 'process';
                            $this->_url = array('process');
                            break;
                        default :
                            throw new Exception('Unexpected Error');
                            break;
                    }
                }
            }
        }
        
        if (file_exists($dir . ucfirst(strtolower($this->_url[0])) . '.php')) {
            $fileName = $dir . ucfirst(strtolower($this->_url[0])) .'.php';
            $className = ucfirst(strtolower($this->_url[0]));
            
            $action = (isset($this->_url[1]) && !empty($this->_url[1])) ? $this->_url[1] : 'main';
        } else {
            if (!file_exists($fileName)) {
                throw new Exception('Base Class not found');
            }
        }
        
        require_once "Logger.php";
            
        $logger = new Upline_Logger('upline24.ru', array('controller' => $className, 'action' => $action, 'request' => $this->_url, 'user' => $this->_auth));
        
        $runConfig = array(
                'url' => $this->_url,
                'basePath' => $this->_basePath,
                'lang' => $this->_lang,
                'getParam' => $this->_getParam,
                'auth' => $this->_auth,
                'logger' => $logger
        );
        
        Zend_Registry::set('run', $runConfig);
        
        require_once $fileName;
        
        $controller = new $className($this->_config);
        
        if (!$controller->factory()) {
            $this->redirect('404');
        }
        
        //echo '<!-- '.$dir . ucfirst(strtolower($this->_url[0])) . '.php'.' '.$className.'->'.$action.'(); -->';
        
        if (method_exists($controller, $action)) {
            if (!$controller->$action()) {
                $this->redirect('404');
            }
        } else {
            if (!$controller->main()) {
                $this->redirect('404');
            }
        }
        
        $controller->finalise();
    }
    
    private function modRewrite()
    {
        $request = substr($_SERVER['REQUEST_URI'], 1);
        $getParam = array();
        
        if (!empty($request)) {
            $request = explode('?', $request);
            
            if (isset($request[1]) && !empty($request[1])) {
                $getParam = $this->extractGetParam($request[1]);
            }
            
            $request = explode('/', iconv('UTF-8', 'cp1251', urldecode($request[0])));
            
            if (end($request) === '') {
                array_pop($request);
            }
            
            $temp = array();
            foreach ($request as $val) {
                $temp[] = addslashes($val);
            }
            
            $request = $temp;
        } else {
            $request[] = 'index';
        }
        
        $request = $this->checkLang($request);
            
        if ($request[0] == '404') {
            $request[0] = 'error404';
        }
        
        $this->_url = $request;
        $this->_getParam = $getParam;
    }
    
    private function extractGetParam($str = null)
    {
        if (null === $str) {
            return array();
        }
        
        $returnArray = array();
        
        $strArray = explode('&', $str);
        
        foreach ($strArray as $param) {
            $get = explode('=', $param);
            
            if (isset($get[1]) && !empty($get[1])) {
                $returnArray[$get[0]] = urldecode($get[1]);
            }
        }
        
        return $returnArray;
    }
    
    private function checkLang($url = null)
    {
        if (null === $url) {
            return '';
        }
        
        $basePath = '/';
        $lang = $this->_config['language']['defaultLanguage'];
        
        if (array_key_exists($url[0], $this->_config['language']['allowLanguage'])) {
            $lang = $this->_config['language']['allowLanguage'][$lang];
            
            if ($lang != $this->_config['language']['defaultLanguage']) {
                $basePath .= $lang.'/';
            } else {
                $basePath .= ($this->_config['language']['useDefLangPath'] ? $lang.'/' : '');
            }
            
            $url = array_shift($url);
        }
        
        $this->_basePath = $basePath;
        $this->_lang = $lang;
        
        return $url;
    }
    
    private function redirect($url = null)
    {
        if (null === $url) {
            throw new Exception('Error redirect function!');
        }
        
        if (!headers_sent()) {
            header("location: " . $this->_basePath . $url);
        } else {
            throw new Exception('Unexpected Error');
        }
    }
    
    private function checkAuth()
    {
        $auth = Zend_Auth::getInstance();
        
        if ($auth->hasIdentity()) {
            $this->_auth = $auth->getIdentity();
        }
    }

}