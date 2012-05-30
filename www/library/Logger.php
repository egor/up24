<?php

class Upline_Logger
{
    private $_mode = 'simple'; // simple, advanced
    
    private $_db = null;
    
    private $_site = null;
    
    private $request = null;
    
    private $user = null;
    
    private $controller = null;
    
    private $action = null;
    
    private $_lastId = null;
    
    /*private $logging = array(
        'Content' => array(
            'forgotpassword',
            'logout',
            'remind',
            'restore'
        ),
        'Admin' => array(
            '*add*',
            '*edit*',
            '*delete*',
            '*update*',
            '*import*',
            '*export*'
        )
    );*/
    
    public function __construct($site = null, $options = array())
    {
        return '';
        $this->setSite($site);
        $this->setOptions($options);
        $this->initDb();
        //echo '<!-- ';
        //var_dump($this->request);
        //echo '--> ';
    }
    
    protected function setSite($siteName)
    {
        if (null === $siteName) {
            return $this;
        }
        
        $this->_site = htmlspecialchars($siteName);
        return $this;
    }
    
    protected function setOptions($options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    
    private function initDb()
    {
        $config = array(
            'adapter' => 'PDO_MYSQL',
            'params' => array(
                'host'              => 'localhost',
                'username'          => 'site_logger',
                'password'          => '6iP2pNEE',
                'dbname'            => 'site_logger',
                'driver_options'    => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'),
                'profiler'          => false
            )
        );
        
        try {
            $database = Zend_Db::factory($config['adapter'], $config['params']);
            $database->getConnection();
        } catch (Zend_Db_Adapter_Exception $e) {
            //throw new Exception('��������, ������������ ��������� ���������� ��� ����� �� ��������');
        } catch (Zend_Exception $e) {
            //throw new Exception('��������, ������� �������� ���������� ������ �������� ��������� �������');
        }
        
        $this->_db = $database;
        return $this;
    }
    
    public function setMode($mode = 'simple')
    {
        if ($mode == 'simple' || $mode = 'advanced') {
            $this->_mode = $mode;
        }
        return $this;
    }
    
    public function setRequest($request)
    {
        $this->request = $request;
    }
    
    public function setUser($user)
    {
        $this->user = $user;
    }
    
    public function setController($controller)
    {
        $this->controller = $controller;
    }
    
    public function setAction($action)
    {
        $this->action = $action;
    }
    
    
    
    
    public function addLogRow($title = '', $backup = '', $type = 'main', $useArray = true)
    {
        return '';
        if ($type == 'main') {
            $data = array(
                'date' => mktime(),
                'title' => mb_convert_encoding($title,'utf-8','windows-1251'),
                'request' => implode('/', $this->request),
                'userId' => isset($this->user->id) ? $this->user->id : 0,
                'userName' => isset($this->user->surname) ? mb_convert_encoding($this->user->surname,'utf-8','windows-1251') : (isset($this->user['surname']) ? mb_convert_encoding($this->user['surname'],'utf-8','windows-1251') : ''),
                'backup' => mb_convert_encoding($backup,'utf-8','windows-1251'),
                'site' => $this->_site
            );
            
            $this->_db->insert('upline', $data);
            
            $this->_lastId = $this->_db->lastInsertId();
        } elseif ($type == 'sub') {
            if (is_array($backup) && $useArray) {
                foreach ($backup as $row) {
                    $data = array(
                        'date' => mktime(),
                        'title' => mb_convert_encoding($title,'utf-8','windows-1251'),
                        'request' => implode('/', $this->request),
                        'userId' => isset($this->user->id) ? $this->user->id : 0,
                        'userName' => isset($this->user->surname) ? mb_convert_encoding($this->user->surname,'utf-8','windows-1251') : (isset($this->user['surname']) ? mb_convert_encoding($this->user['surname'],'utf-8','windows-1251') : ''),
                        'backup' => mb_convert_encoding(serialize($row),'utf-8','windows-1251'),
                        'site' => $this->_site,
                        'sub' => $this->_lastId,
                        'type' => $type
                    );
                    
                    $this->_db->insert('upline', $data);
                }
            } else {
                $data = array(
                    'date' => mktime(),
                    'title' => mb_convert_encoding($title,'utf-8','windows-1251'),
                    'request' => implode('/', $this->request),
                    'userId' => isset($this->user->id) ? $this->user->id : 0,
                    'userName' => isset($this->user->surname) ? mb_convert_encoding($this->user->surname,'utf-8','windows-1251') : (isset($this->user['surname']) ? mb_convert_encoding($this->user['surname'],'utf-8','windows-1251') : ''),
                    'backup' => mb_convert_encoding(serialize($backup),'utf-8','windows-1251'),
                    'site' => $this->_site,
                    'sub' => $this->_lastId,
                    'type' => $type
                );
                
                $this->_db->insert('upline', $data);
            }
        } else {
            return $this;
        }
    }
}