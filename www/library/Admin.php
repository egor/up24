<?php

require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

class Admin extends Main_Abstract implements Main_Interface
{
    
    public function factory()
    {
        if (!$this->_isAdmin()) {
            return false;
        }
        
        $this->tpl->set_root('tpl/');
        $this->tpl->define_dynamic('page', "admin/main.tpl");
        $this->tpl->define_dynamic('null', 'page');
        $this->tpl->define_dynamic('edit', 'admin/edit.tpl');
        
        return true;
    }
    
    public function main()
    {
        $this->setMetaTags('Админ-панель');
        
        $this->tpl->assign('CONTENT', '');
        
        
        return true;
    }
    
    
    public function addnews()
    {
        $this->setMetaTags('Добавление новости');
        $this->setWay('Добавление новости');
        
        $this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('pic', 'edit');
		$this->tpl->define_dynamic('adress', 'edit');
		$this->tpl->define_dynamic('meta', 'edit');
		$this->tpl->define_dynamic('visible', 'edit');
		$this->tpl->define_dynamic('news', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
                $this->tpl->define_dynamic('ed_f', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		$href = $this->ru2Lat($this->getVar('adm_href', ''));
		$date = $this->getVar('date', date('d.m.Y'));
		$preview = $this->getVar('preview', '');
		$header = $this->getVar('header', '');
		$title = $this->getVar('title', '');
		$keywords = $this->getVar('keywords', '');
		$description = $this->getVar('description', '');
		$visible = $this->getVar('visible', 1);
		$body = $this->getVar('body', '');
		$ed1 = $this->getVar('ed1', '');
                $ed2 = $this->getVar('ed2', '');
                
		$visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }
        
        if (!empty($_POST)) {
            $referer = $this->getVar('HTTP_REFERER', '');
            
            if (!$href) {
                $this->addErr('Не заполнен адрес');
            } else {
                $count = $this->db->fetchOne('SELECT COUNT(`id`) FROM `news` WHERE `href` = "'.$href.'"');
                
                if ($count > 0) {
                    $this->addErr('Элемент с таким адресом уже существует');
                }
            }
            
            if (!$header) {
                $this->addErr('Поле "Header" не заполнено');
            }
        } else {
            $referer = $this->getVar('HTTP_REFERER', $this->basePath);
        }
        
        if (!empty($_POST) && !$this->_err) {
            $date = explode('.', $date);
            
            $date = mktime(date('H'), date('i'), date('s'), $date[1], $date[0], $date[2]);
            
            $data = array(
                'href'          => $href,
                'date'          => $date,
                'preview'       => $preview,
                'header'        => $header,
                'title'         => $title,
                'keywords'      => $keywords,
                'description'   => $description,
                'visibility'    => $visible,
                'body'          => $body,
                'ed1'          => $ed1,
                'ed2'          => $ed2
            );
            
            $id = $this->db->fetchOne("SELECT MAX(`id`) FROM `news`") + 1;
            
            if ($pic = $this->getVar('pic')) {
            	if (!empty($pic['name']) && $pic['error'] == 0) {
                	if (file_exists(PATH.'images/news/'.$id.'-'.$pic['name'])) {
                    	@unlink(PATH.'images/news/'.$id.'-'.$pic['name']);
                    }
                    
                    if (!$this->uploadContentImage($pic['tmp_name'], './images/news/'.$id.'-'.$pic['name'], 140, 105, 100)) {
                        $this->addErr('Во время загрузки картинки произошла ошибка');
                    }
                }
                
                $data['pic'] = $id.'-'.$pic['name'];
            }
        }
            
        if (!empty($_POST) && !$this->_err) {
            $this->logger->addLogRow('Добавление новости', serialize($data));
            
            $this->db->insert("news", $data);
            
            $referer = (!empty($referer) && $referer != '{REFERER}') ? $referer : $this->basePath;
            $content = "Новость успешно добавлена<meta http-equiv='refresh' content='2;URL=$referer'>";
            
            $this->viewMessage($content);
        }
        
        if ($this->_err) {
            $this->viewErr();
        }
        
        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(
                array(
                    'ADM_HREF' => $href,
                    'ADM_DATE' => $this->convertDate($date),
                    'ADM_PREVIEW' => $preview,
                    'ADM_BODY' => $body,
                    'VISIBLE_S' => $visible_s,
                    'ADM_HEADER' => $header,
                    'ADM_TITLE' => $title,
                    'ADM_KEYWORDS' => $keywords,
                    'ADM_DESCRIPTION' => $description,
                    'REFERER' => $referer,
                    'SHOW_PIC'=>'',
                    'ED1'           =>  $ed1,
                    'ED2'           =>  $ed2,
                    
                )
            );
		
            $this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.adress');
			$this->tpl->parse('CONTENT', '.pic');
			$this->tpl->parse('CONTENT', '.visible');
			$this->tpl->parse('CONTENT', '.news');
			$this->tpl->parse('CONTENT', '.meta');
			$this->tpl->parse('CONTENT', '.body');
                        $this->tpl->parse('CONTENT', '.ed_f');
			$this->tpl->parse('CONTENT', '.end');
        }
        
        return true;
    }
    
    public function editnews()
    {
        $id = end($this->url);
        
        if (!ctype_digit($id)) {
            return $this->error404();
        }
        
        $news = $this->db->fetchRow("SELECT * FROM `news` WHERE `id` = '$id'");
        
        if (!$news) {
            return $this->error404();
        }
        
        $this->setMetaTags('Редактирование новости');
        $this->setWay('Редактирование новости');
        
        $this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('pic', 'edit');
		$this->tpl->define_dynamic('adress', 'edit');
		$this->tpl->define_dynamic('meta', 'edit');
		$this->tpl->define_dynamic('visible', 'edit');
		$this->tpl->define_dynamic('news', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
                $this->tpl->define_dynamic('ed_f', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
        
        $href = $news['href'];
		$date = $this->convertDate($news['date'], 'd.m.Y');
		$preview = $news['preview'];
		$header = $news['header'];
		$title = $news['title'];
		$keywords = $news['keywords'];
		$description = $news['description'];
		$visible = $news['visibility'];
		$body = $news['body'];
		$ed1 = $news['ed1'];
                $ed2 = $news['ed2'];
                
		if (!empty($_POST)) {
		    $referer = $this->getVar('HTTP_REFERER', '');
		    
		$href = $this->ru2Lat($this->getVar('adm_href', ''));
    		$date = $this->getVar('date', date('d.m.Y'));
    		$preview = $this->getVar('preview', '');
    		$header = $this->getVar('header', '');
    		$title = $this->getVar('title', '');
    		$keywords = $this->getVar('keywords', '');
    		$description = $this->getVar('description', '');
    		$visible = $this->getVar('visible', 1);
    		$body = $this->getVar('body', '');
    		$ed1 = $this->getVar('ed1', '');
                $ed2 = $this->getVar('ed2', '');
    		if (!$href) {
                $this->addErr('Не заполнен адрес');
            } else {
                $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `news` WHERE `href` = '$href' AND `language` = '".$this->lang."' AND `id` <> '".$id."'");
                
                if ($count > 0) {
                    $this->addErr('Элемент с таким адресом уже существует');
                }
            }
            
            if (!$header) {
                $this->addErr('Поле "Header" не заполнено');
            }
		} else {
		    $referer = $this->getVar('HTTP_REFERER', $this->basePath);
		}
		
		$visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }
        
        if (!empty($_POST) && !$this->_err) {
            $new_date = explode('.', $date);
            
            $new_date = mktime(date('H'), date('i'), date('s'), $new_date[1], $new_date[0], $new_date[2]);
            
            $data = array(
                'href'          => $href,
                'date'          => $new_date,
                'preview'       => $preview,
                'header'        => $header,
                'title'         => $title,
                'keywords'      => $keywords,
                'description'   => $description,
                'visibility'    => $visible,
                'body'          => $body,
                'ed1'           => $ed1,
                'ed2'           => $ed2,
            );
            
            if ($pic = $this->getVar('pic')) {             
            	if (!empty($pic['name']) && $pic['error'] == 0) {
            		if (file_exists(PATH.'images/news/'.$id.'-'.$pic['name'])) {
                    	@unlink(PATH.'images/news/'.$id.'-'.$pic['name']);
                    }
                    
                    if (!$this->uploadContentImage($pic['tmp_name'], './images/news/'.$id.'-'.$pic['name'], 135, 100, 100)) {
                        $this->addErr('Во время загрузки картинки произошла ошибка');
                    }
            		
            		$data['pic'] = $id.'-'.$pic['name'];
            	} elseif(isset($data['pic'])) {
            		unset($data['pic']);
            	}
                
            }
        }
            
        if (!empty($_POST) && !$this->_err) {
            $this->logger->addLogRow('Редактирование новости', serialize($news));
            
            $n = $this->db->update('news', $data, "id = $id");
            
            $referer = (!empty($referer) && $referer != '{REFERER}') ? $referer : $this->basePath;
            $content = "Новость успешно отредактирована<meta http-equiv='refresh' content='2;URL=$referer'>";
            
            $this->viewMessage($content);
        }
        
        if ($this->_err) {
            $this->viewErr();
        }
        
        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(
                array(
                    'ADM_HREF' => $href,
                    'ADM_DATE' => $date,
                    'ADM_PREVIEW' => $preview,
                    'ADM_BODY' => $body,
                    'VISIBLE_S' => $visible_s,
                    'ADM_HEADER' => $header,
                    'ADM_TITLE' => $title,
                    'ADM_KEYWORDS' => $keywords,
                    'ADM_DESCRIPTION' => $description,
                    'REFERER' => $referer,
                    'SHOW_PIC'=>'',
                    'ED1'               =>  (!empty($ed1)?$ed1:''),
                    'ED2'               =>  (!empty($ed2)?$ed2:''),
                )
            );
		
            $this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.adress');
			$this->tpl->parse('CONTENT', '.pic');
			$this->tpl->parse('CONTENT', '.visible');
			$this->tpl->parse('CONTENT', '.news');
			$this->tpl->parse('CONTENT', '.meta');
			$this->tpl->parse('CONTENT', '.body');
                        $this->tpl->parse('CONTENT', '.ed_f');
			$this->tpl->parse('CONTENT', '.end');
        }
        
        return true;
    }
    
    public function deletenews()
    {
        $id = end($this->url);
        
        if (!ctype_digit($id)) {
            return $this->error404();
        }
        
        $news = $this->db->fetchRow("SELECT * FROM `news` WHERE `id` = '$id'");
        
        if (!$news) {
            return $this->error404();
        }
        
        $this->setMetaTags('Удаление новости');
        $this->setWay('Удаление новости');
        
        if ($news['pic'] != '' && file_exists('./images/news/'.$news['pic'])) {
            @unlink('./images/news/'.$news['pic']);
        }
        
        $this->logger->addLogRow('Удаление новости', serialize($news));
        
        $n = $this->db->delete('news', "id = $id");
        
        $referer = $this->getVar('HTTP_REFERER', $this->basePath);
        
        $content = "Новость успешно удалена<meta http-equiv='refresh' content='2;URL=$referer'>";
		$this->viewMessage($content);
		
		return true;
    }
    
    
    
    public function menu()
    {
        $this->setMetaTags('Горизонтальное меню');
        $this->setWay('Горизонтальное меню');
        
        $menus = $this->db->fetchAll("SELECT `id`, `header`, `href`, `type` FROM `page` WHERE `level` = '0' AND `menu` = 'yes' ORDER BY `position`, `header`");
        
        $this->viewMessage($this->getAdminAdd('page', 'menu'));
        
        if ($menus) {
            $this->tpl->define_dynamic('_list', 'admin/list.tpl');
            $this->tpl->define_dynamic('list', '_list');
            $this->tpl->define_dynamic('list_row', 'list');
            
            foreach ($menus as $menu) {
                $this->tpl->assign(
                    array(
                        'PAGE_ADM' => $this->getAdminEdit('page', $menu['id']),
                        'PAGE_ADRESS' => $menu['type'] == 'link' ? $menu['href'] : $this->basePath . $menu['href'],
                        'PAGE_NAME' => stripslashes($menu['header'])
                    )
                );
                
                $this->tpl->parse('LIST_ROW', '.list_row');
            }
            
            $this->tpl->parse('CONTENT', '.list');
        } else {
            $this->viewMessage('Разделов не найдено');
        }
        
        return true;
    }
    
    public function addlink()
    {
		return $this->addpages('link');
	}
	
	public function addpage()
	{
		return $this->addpages('page');
	}
	
	public function addsection()
	{
		return $this->addpages('section');
	}
	
	private function addpages($type = null)
	{ 
	    if (null === $type) {
	        return $this->error404();
	    }
	    
	    $id = end($this->url);
	    
	    if ($id == 'menu') {
	        $level = 0;
	        $menu = 'yes';
	    } elseif (ctype_digit($id) && $id > 0) {
	        $level = $id;
	        $menu = 'no';
	    } else {
	        return $this->error404();
	    }
	    
	    switch ($type) {
	        case 'section' : $meta = 'Добавление нового раздела'; break;
	        case 'page' : $meta = 'Добавление новой страницы'; break;
	        case 'link' : $meta = 'Добавление новой сылки'; break;
	        default : return $this->error404(); break;
	    }
	    
	    $this->setMetaTags($meta);
        $this->setWay($meta);
	    
	    $this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('adress', 'edit');
                $this->tpl->define_dynamic('pic', 'edit');
		$this->tpl->define_dynamic('pos', 'edit');
		$this->tpl->define_dynamic('meta', 'edit');
		$this->tpl->define_dynamic('header', 'edit');
		$this->tpl->define_dynamic('visible', 'edit');
		$this->tpl->define_dynamic('preview', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
                $this->tpl->define_dynamic('ed_f', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		$href = $this->ru2Lat($this->getVar('adm_href', ''));
		$position = $this->getVar('position', 9999);
		$preview = $this->getVar('preview', '');
		$header = $this->getVar('header', '');
		$title = $this->getVar('title', '');
		$keywords = $this->getVar('keywords', '');
		$description = $this->getVar('description', '');
		$visible = $this->getVar('visible', 1);
		$body = $this->getVar('body', '');
                $ed1 = $this->getVar('ed1', '');
                $ed2 = $this->getVar('ed2', '');
		
		$visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }
        
        if (!empty($_POST)) {
            $referer = $this->getVar('HTTP_REFERER', '');
            
            if (!$href) {
                $this->addErr('Не заполнен адрес');
            } else {
                $count = $this->db->fetchOne('SELECT COUNT(`id`) FROM `page` WHERE `href` = "'.$href.'"');
            
                if ($count > 0) {
                    $this->addErr('Элемент с таким адресом уже существует');
                }
            }
            
            if (!$header) {
                $this->addErr('Поле "Header" не заполнено');
            }
        } else {
            $referer = $this->getVar('HTTP_REFERER', $this->basePath);
        }
        
        if (!empty($_POST) && !$this->_err) {            
            $data = array(
                'href'          => $href,
                'position'      => $position,
                'preview'       => $preview,
                'header'        => $header,
                'title'         => $title,
                'keywords'      => $keywords,
                'description'   => $description,
                'visibility'    => $visible,
                'body'          => $body,
                'level'         => $level,
                'menu'          => $menu,
                'type'          => $type,
                'ed1'          => $ed1,
                'ed2'          => $ed2
            );
            if ($pic = $this->getVar('pic')) {
            	if (!empty($pic['name']) && $pic['error'] == 0) {
                	if (file_exists(PATH.'images/pages/'.$id.'-'.$pic['name'])) {
                    	@unlink(PATH.'images/pages/'.$id.'-'.$pic['name']);
                        $data['pic']=$id.'-'.$pic['name'];
                    }
                    
                    if (!$this->uploadContentImage($pic['tmp_name'], './images/pages/'.$id.'-'.$pic['name'], 140, 105, 100)) {
                        $this->addErr('Во время загрузки картинки произошла ошибка');
                    }
            }
            }  
            $this->logger->addLogRow($meta, serialize($data));
          
            $this->db->insert("page", $data);
            
            $referer = (!empty($referer) && $referer != '{REFERER}') ? $referer : $this->basePath;
            $content = "Элемент успешно добавлен<meta http-equiv='refresh' content='2;URL=$referer'>";
            
            $this->viewMessage($content);
        }
        
        if ($this->_err) {
            $this->viewErr();
        }
        
        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(
                array(
                    'ADM_HREF' => $href,
                    'ADM_POSITION' => $position,
                    'ADM_PREVIEW' => $preview,
                    'ADM_BODY' => $body,
                    'VISIBLE_S' => $visible_s,
                    'ADM_HEADER' => $header,
                    'ADM_TITLE' => $title,
                    'ADM_KEYWORDS' => $keywords,
                    'ADM_DESCRIPTION' => $description,
                    'REFERER'           =>  $referer,
                    'ED1'           =>  $ed1,
                    'ED2'           =>  $ed2,
                    'SHOW_PIC'          =>  ''
                )
            );
		
            $this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.adress');
                        $this->tpl->parse('CONTENT', '.pic');
			if ($type == 'link') $this->tpl->parse('CONTENT', '.header');
			$this->tpl->parse('CONTENT', '.pos');
			$this->tpl->parse('CONTENT', '.visible');
			if ($type != 'link') $this->tpl->parse('CONTENT', '.meta');
			if ($menu == 'no' && $type != 'link') $this->tpl->parse('CONTENT', '.preview');
			if ($type != 'link') {
                            $this->tpl->parse('CONTENT', '.body');
                            $this->tpl->parse('CONTENT', '.ed_f');
                        }
			$this->tpl->parse('CONTENT', '.end');
        }
        
        return true;
	}
    
	public function editpage()
	{ 
	    $id = end($this->url);
	    
	    if (ctype_digit($id) && $id > 0) {
	        $page = $this->db->fetchRow("SELECT * FROM `page` WHERE `id` = '$id'");
	        $type = $page['type'];
	    } else {
	        return $this->error404();
	    }
	    
	    if (!$page) {
	        return $this->error404();
	    }
	    
	    $id = $page['id'];
	    
	    switch ($type) {
	        case 'section' : $meta = 'Редактирование раздела'; break;
	        case 'page' : $meta = 'Редактирование страницы'; break;
	        case 'link' : $meta = 'Редактирование ссылки'; break;
	        default : return $this->error404(); break;
	    }
	    
	    $this->setMetaTags($meta);
        $this->setWay($meta);
	    
	    $this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('adress', 'edit');
                $this->tpl->define_dynamic('pic', 'edit');
		$this->tpl->define_dynamic('pos', 'edit');
		$this->tpl->define_dynamic('meta', 'edit');
		$this->tpl->define_dynamic('header', 'edit');
		$this->tpl->define_dynamic('visible', 'edit');
		$this->tpl->define_dynamic('preview', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
                $this->tpl->define_dynamic('ed_f', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		$href = $page['href'];
		$position = $page['position'];
		$preview = $page['preview'];
		$header = $page['header'];
		$title = $page['title'];
		$keywords = $page['keywords'];
		$description = $page['description'];
		$visible = $page['visibility'];
		$body = $page['body'];
                $ed1 = $page['ed1'];
                $ed2 = $page['ed2'];
                $showPic = (!empty($page['pic'])?'<tr><td colspan="2"><img src="/images/pages/'.$page['pic'].'"><br><a href="/admin/delpicpage/'.$id.'" onclick="return window.confirm(\'Удалить изображение?\')">Удалить</a></td></tr>':'');
				
		if (!empty($_POST)) {
		    $href = $this->ru2Lat($this->getVar('adm_href', ''));
	        $visible = $this->getVar('visible', 1);
    		$position = $this->getVar('position', 9999);
    		$preview = $this->getVar('preview', '');
    		$header = $this->getVar('header', '');
    		$title = $this->getVar('title', '');
    		$keywords = $this->getVar('keywords', '');
    		$description = $this->getVar('description', '');
    		$visible = $this->getVar('visible', 1);
    		$body = $this->getVar('body', '');
                $ed1 = $this->getVar('ed1', '');
                $ed2 = $this->getVar('ed2', '');
    		
    		$referer = $this->getVar('HTTP_REFERER', '');
    		
    		if (!$href) {
                $this->addErr('Не заполнен адрес');
            } else {
                $count = $this->db->fetchOne("SELECT COUNT(`id`) FROM `page` WHERE `href` = '$href' AND `id` <> $id");
                
                if ($count > 0) {
                    $this->addErr('Элемент с таким адресом уже существует');
                }
            }
            
            if (!$header) {
                $this->addErr('Поле "Header" не заполнено');
            }
		} else {
		    $referer = $this->getVar('HTTP_REFERER', $this->basePath);
		}
		
		$visible_s = '';
		
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }
        
        if (!empty($_POST) && !$this->_err) {
            $this->logger->addLogRow($meta, serialize($page));
            
            $data = array(
                'href'          => $href,
                'position'      => $position,
                'preview'       => stripslashes($preview),
                'header'        => $header,
                'title'         => $title,
                'keywords'      => $keywords,
                'description'   => $description,
                'visibility'    => $visible,
                'ed1'           => $ed1,
                'ed2'           => $ed2,
                'body'          => stripslashes($body)
            );
            //Загрузка картинки
             if ($pic = $this->getVar('pic')) {             
            	if (!empty($pic['name']) && $pic['error'] == 0) {
            		if (file_exists(PATH.'images/pages/'.$id.'-'.$pic['name'])) {
                    	@unlink(PATH.'images/pages/'.$id.'-'.$pic['name']);
                    }
                    
                    if (!$this->uploadContentImage($pic['tmp_name'], './images/pages/'.$id.'-'.$pic['name'], 135, 100, 100)) {
                        $this->addErr('Во время загрузки картинки произошла ошибка');
                    }
            		
            		$data['pic'] = $id.'-'.$pic['name'];
            	} elseif(isset($data['pic'])) {
            		unset($data['pic']);
            	}
                
            }
            $n = $this->db->update('page', $data, "id = $id");
            
            
            
            $referer = (!empty($referer) && $referer != '{REFERER}') ? $referer : $this->basePath;
            $content = "Элемент успешно изменен<meta http-equiv='refresh' content='2;URL=$referer'>";
            //$content = "Элемент успешно изменен";
            
            $this->viewMessage($content);
        }
		
        if ($this->_err) {
            $this->viewErr();
        }
        
        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(
                array(
                    'ADM_HREF'          =>  $href,
                    'ADM_POSITION'      =>  $position,
                    'ADM_PREVIEW'       =>  stripslashes($preview),
                    'ADM_BODY'          =>  stripslashes($body),
                    'VISIBLE_S'         =>  $visible_s,
                    'ADM_HEADER'        =>  $header,
                    'ADM_TITLE'         =>  $title,
                    'ADM_KEYWORDS'      =>  $keywords,
                    'ADM_DESCRIPTION'   =>  $description,
                    'REFERER'           =>  $referer,
                    'ED1'               =>  $ed1,
                    'ED2'               =>  $ed2,
                    'SHOW_PIC'          =>  $showPic
                )
            );
		
            $this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.adress');
                        $this->tpl->parse('CONTENT', '.pic');
			if ($type == 'link') $this->tpl->parse('CONTENT', '.header');
			$this->tpl->parse('CONTENT', '.pos');
			$this->tpl->parse('CONTENT', '.visible');
			if ($type != 'link') $this->tpl->parse('CONTENT', '.meta');
			if ($type != 'link' && $page['level'] != 0) $this->tpl->parse('CONTENT', '.preview');
			if ($type != 'link') { 
                            $this->tpl->parse('CONTENT', '.body');
                            $this->tpl->parse('CONTENT', '.ed_f');
                        }
			$this->tpl->parse('CONTENT', '.end');
        }
        
        return true;
	}
	
	public function deletepage()
	{
	    $id = end($this->url);
	    
	    if (!ctype_digit($id)) {
	        return $this->error404();
	    }
	    
	    $page = $this->db->fetchRow("SELECT * FROM `page` WHERE `id` = '$id'");
	    
	    if (!$page) {
	        return $this->error404();
	    }
	    
	    switch ($page['type']) {
	        case 'section' : $meta = 'Удаление раздела'; break;
	        case 'page' : $meta = 'Удаление страницы'; break;
	        case 'link' : $meta = 'Удаление ссылки'; break;
	        default : return $this->error404(); break;
	    }
	    
	    $this->setMetaTags($meta);
        $this->setWay($meta);
        
        $this->logger->addLogRow($meta, serialize($page));
        
        $n = $this->db->delete('page', "id = $id");
        
        if ($page['type'] == 'section') {
            $this->deleteSubPages($page);
        }
        
        $referer = $this->getVar('HTTP_REFERER', $this->basePath);
        
        $content = "Элемент(ы) успешно удален(ы)<meta http-equiv='refresh' content='2;URL=$referer'>";
		$this->viewMessage($content);
		
		return true;
	}
	
	private function deleteSubPages($page = null)
	{
	    if (null === $page) {
	        return $this->error404();
	    }
	    
	    $pages = $this->db->fetchAll("SELECT `id`, `type` FROM `page` WHERE `level` = '".$page['id']."'");
	    
	    $n = $this->db->delete('page', "level = ".$page['id']);
	    
	    $this->logger->addLogRow('Вложенное удаление страниц/разделов', $pages, 'sub');
	    
	    if ($pages) {
	        foreach ($pages as $p) {
	            if ($p['type'] == 'section') {
	                $this->deleteSubPages($p);
	            }
	        }
	    }
	}
    
	
	public function hall()
	{
	    $this->setMetaTags('Каталог залов');
	    
	    $select = $this->db->select();
	    $select->from('hall', array('*'));
	    $select->join('city', 'hall.city_id = city.id', array('city_name' => 'city.name'));
            $select->where('hall.country = ?', $_SESSION['countryDName']);
	    $select->order('city_name');
	    
	    $hall = $this->db->fetchAll($select);
	    
	    $this->tpl->define_dynamic('hall', "admin/hall.tpl");
	    $this->tpl->define_dynamic('list', 'hall');
            $this->tpl->define_dynamic('country', "list");
            $this->tpl->define_dynamic('list_row', 'list');
            
        $this->viewMessage($this->getAdminAdd('hall'));
        $this->tpl->parse('CONTENT', '.country');
        if ($hall) {
            $currentCity = $hall[0]['city_name'];
            
            $this->tpl->assign('GROUP_CITY', $currentCity);
            
            $increment = 0;
            $count = sizeof($hall);
            
            foreach ($hall as $row) {
                if ($currentCity != $row['city_name']) {
                    $currentCity = $row['city_name'];
                    $this->tpl->parse('CONTENT', '.list');
                    $this->tpl->parse('LIST_ROW', 'null');
                    $this->tpl->assign('GROUP_CITY', $currentCity);
                }
                
                $this->tpl->assign(
                    array(
                        'HALL_ADM' => $this->getAdminEdit('hall', $row['id']),
                        'HALL_NAME' => $row['name'],
                        'HALL_ADRESS' => '/admin/viewhall/'.$row['id'],
                        'HALL_ID' => $row['id']
                    )
                );
                
                $this->tpl->parse('LIST_ROW', '.list_row');
                
                $increment++;
                
                if ($increment == $count) {
                    $this->tpl->parse('CONTENT', '.list');
                }
            }
        } else {
            $this->viewMessage('Залов не найдено');
        }
	
        $this->tpl->assign(
            array(
                'CLASS_ACTIVE_RU'    =>  ($_SESSION['countryDName']=='ru'?' class="acrive"':' class="noacrive"'),
                'CLASS_ACTIVE_UA'    =>  ($_SESSION['countryDName']=='ua'?' class="acrive"':' class="noacrive"')

    	        )
    	    ); 
        
	    return true;
	}
	
	public function viewhall()
	{
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $select = $this->db->select();
	    $select->from('hall', array('*'));
	    $select->join('city', 'hall.city_id = city.id', array('city_name' => 'city.name'));
	    $select->where('hall.id = ?', $id);
	    
	    $hall = $this->db->fetchAll($select);
        
        if (!$hall || sizeof($hall) != 1) {
            return false;
        }
        
        $hall = $hall[0];
        
        $sectors = $this->db->fetchAll("SELECT * FROM `sectors` WHERE `hall_id` = '$id' ORDER BY `id`");
        
        //$this->setMetaTags('Зал: '.$hall['name']);
        $this->setMetaTagsAdmin('Зал: '.$hall['name'], $this->getAdminEdit('hall', $hall['id']));
        
        $this->tpl->define_dynamic('hall', "admin/hall.tpl");
        $this->tpl->define_dynamic('detail', "hall");
        $this->tpl->define_dynamic('sectors_row', "detail");
        $this->tpl->define_dynamic('sectors_row_empty', "detail");
        $this->tpl->define_dynamic('sector_confirm', "detail");
        
        $this->tpl->assign(
            array(
                'HALL_ID' => $hall['id'],
                'HALL_NAME' => $hall['name'],
                'HALL_CITY' => $hall['city_name'],
                'HALL_ADRES' => $hall['adres'],
                'HALL_PREVIEW' => stripslashes($hall['preview']),
                'HALL_PIC' => $hall['pic'],
                'HALL_PIC_H3' => (!empty($hall['pic'])?'Описание зала':''),
            )
        );
        
        if ($sectors) {
            $this->tpl->parse('SECTORS_ROW_EMPTY', 'null');
            
            foreach ($sectors as $row) {
                $this->tpl->assign(
                    array(
                        'SECTOR_ID' => $row['id'],
                        'SECTOR_NAME' => $row['name'],
                        'SECTOR_COUNT' => $row['count']
                    )
                );
                
                $this->tpl->parse('SECTORS_ROW', '.sectors_row');
            }
        } else {
            $this->tpl->parse('SECTORS_ROW', 'null');
            $this->tpl->parse('SECTOR_CONFIRM', 'null');
        }
        
        $this->tpl->parse('CONTENT', '.detail');
        
        return true;
	}
	
	public function addhall()
	{
	    $this->setMetaTags('Добавление зала');
	    
	    $this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('adress', 'edit');
		$this->tpl->define_dynamic('name', 'edit');
		//$this->tpl->define_dynamic('sectors_ajax', 'edit');
		//$this->tpl->define_dynamic('sectors', 'edit');
		//$this->tpl->define_dynamic('sectors_count', 'edit');
		$this->tpl->define_dynamic('preview', 'edit');
		$this->tpl->define_dynamic('hallcity', 'edit');
		$this->tpl->define_dynamic('adres', 'edit');
		$this->tpl->define_dynamic('hallpic', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		$name = $this->getVar('name', '');
		$preview = $this->getVar('preview', '');
		$city_id = $this->getVar('city_id', 0);
		$city_name = $this->getVar('city_name', '');
		$adres = $this->getVar('adres', '');
		$pic = $this->getVar('hallpic', null, $_FILES);
		/*$sectorCount = $this->getVar('sector_count', 0);
		$sectors = array();
		
		if ($sectorCount > 0) {
		    for ($i=0; $i<$sectorCount; $i++) {
		        $sectors[$i] = array(
                    'name' => $this->getVar('sector_name_'.$i, ''),
                    'count' => $this->getVar('sector_count_'.$i, 0)
		        );
		    }
		}*/
		
		if (!empty($_POST)) {
		    if (!$name) {
                $this->addErr('Не заполнено "Название"');
            } else {
                $count = $this->db->fetchOne('SELECT COUNT(`id`) FROM `hall` WHERE `name` = "'.$name.'"');
            
                if ($count > 0) {
                    $this->addErr('Элемент с таким названием уже существует');
                }
            }
            
            if (!$city_name && $city_id < 1) {
                $this->addErr('Выберите город из списка или укажите новый');
            }
            
            if (!$this->_err) {
                if ($city_name) {
                    //echo "SELECT * FROM `city` WHERE `name` = '$city_name' AND `country`='".$_SESSION['countryDName']."'";
                    $city = $this->db->fetchRow("SELECT * FROM `city` WHERE `name` = '$city_name' AND `country`='".$_SESSION['countryDName']."'");
                    
                    if ($city) {
                        $city_id = $city['id'];
                    } else {
                        $data = array(
                            'name' => addslashes($city_name),
                            'href' => $this->ru2Lat($city_name)
                        );
                        
                        $this->db->insert('city', $data);
                        
                        $city_id = $this->db->lastInsertId();
                    }
                } else {
                    if (!ctype_digit($city_id)) {
                        $this->addErr('Выберите город из списка или укажите новый');
                        $city_id = 0;
                    } else {
                        $city = $this->db->fetchRow("SELECT * FROM `city` WHERE `id` = '$city_id' AND country='".$_SESSION['countryDName']."'");
                        
                        if (!$city) {
                            $this->addErr('Выберите город из списка или укажите новый');
                            $city_id = 0;
                        }
                    }
                }
            }
            
        }
        
        $hallpic = '';
        if (!empty($_POST) && !$this->_err) {
            if ($pic && $pic['error'] == 0) {
                $extension = explode('.', $pic['name']);
                $extension = end($extension);
                $pic_name = $this->ru2Lat($name).'.'.$extension;
                
                $pic = $this->uploadHallPic($pic['tmp_name'], './images/hall/', $pic_name);
                if ($pic) {
                    $hallpic = $pic_name;
                }
            } else {
                if ($pic) {
                    switch ($pic['error']) {
                        case '1' :
                        case '2' :
                        case '3' :
                            $this->addErr('Ошибка загрузки файла. Обратитесь пожалуйста к Администратору.');
                            break;
                        default: break;
                    }
                }
            }
        }
        
        if (!empty($_POST) && !$this->_err) {
            $data = array(
                'name'          => $name,
                'preview'       => stripslashes($preview),
                'city_id'       => $city_id,
                'adres'         => $adres,
                'pic'           => $hallpic,
                'country'       => $_SESSION['countryDName'],
            );
            
            $this->logger->addLogRow('Добавление зала', serialize($data));
            
            $this->db->insert("hall", $data);
            
            $hall_id = $this->db->lastInsertId();
            
            /*if (!empty($sectors)) {
                foreach ($sectors as $row) {
                    $data = array(
                        'hall_id' => $hall_id,
                        'name' => $row['name'],
                        'count' => $row['count']
                    );
                    
                    $this->db->insert('sectors', $data);
                }
            }*/
            
            $content = "Элемент успешно добавлен<meta http-equiv='refresh' content='2;URL=/admin/viewhall/$hall_id'>";
            
            $this->viewMessage($content);
        }
        
        if ($this->_err) {
            $this->viewErr();
        }
        
        if (empty($_POST) || $this->_err) {
            $cityArray = $this->db->fetchAll("SELECT * FROM `city` WHERE `country`='".$_SESSION['countryDName']."' ORDER BY `name`");
            
            $city_list = '<option value="0"'.($city_id == 0 ? ' selected' : '').'>Выберите город</option>';
            
            if ($cityArray) {
                foreach ($cityArray as $row) {
                    $city_list .= '<option value="'.$row['id'].'"'.($row['id'] == $city_id ? ' selected' : '').'>'.$row['name'].'</option>';
                }
            }
            
            $this->tpl->assign(
                array(
                    'ADM_NAME' => $name,
                    'ADM_PREVIEW' => stripslashes($preview),
                    'ADM_CITY_NAME' => $city_name,
                    'ADM_ADRES' => $adres,
                    'ADM_CITY_LIST' => $city_list,
                    'THISHALLPICVIS'=>' display:none; '
                    //'SECTOR_COUNT' => $sectorCount
                )
            );
		
            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.sectors_count');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.name');
			$this->tpl->parse('CONTENT', '.adres');
			$this->tpl->parse('CONTENT', '.preview');
			$this->tpl->parse('CONTENT', '.hallcity');
			$this->tpl->parse('CONTENT', '.hallpic');
			
			/*if (!empty($sectors)) {
			    foreach ($sectors as $key => $row) {
			        $this->tpl->assign(
                        array(
                            'INCREMENT' => $key,
                            'SECTOR_NAME' => $row['name'],
                            'SECTOR_COUNTS' => $row['count']
                        )
			        );
			        
			        $this->tpl->parse('CONTENT', '.sectors');
			    }
			} else {
			    $this->tpl->parse('CONTENT', '.sectors_ajax');
			}*/
			
			$this->tpl->parse('CONTENT', '.end');
        }
        
        return true;
	}
	
	public function edithall()
	{
            
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $hall = $this->db->fetchRow("SELECT * FROM `hall` WHERE `id` = '$id'");
        
        if (!$hall) {
            return false;
        }
        
        $this->setMetaTags('Редактирование зала: '.$hall['name']);
        
        $this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('adress', 'edit');
		$this->tpl->define_dynamic('name', 'edit');
		$this->tpl->define_dynamic('preview', 'edit');
		$this->tpl->define_dynamic('hallcity', 'edit');
		$this->tpl->define_dynamic('adres', 'edit');
		$this->tpl->define_dynamic('hallpic', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		$name = $this->getVar('name', '', $hall);
                $pic = $this->getVar('pic', '', $hall);
		$preview = $this->getVar('preview', '', $hall);
		$city_id = $this->getVar('city_id', 0, $hall);
		$city_name = $this->getVar('city_name', '', $hall);
		$adres = $this->getVar('adres', '', $hall);
                if (isset($_GET['del']) and $_GET['del']=='pichall'){
                    if ($hall['pic']) {
                        if (file_exists('./images/hall/'.$hall['pic'])) {
                            @unlink('./images/hall/'.$hall['pic']);
                        }
                    }
                    $data = array('pic' => '');
                    $this->db->update('hall', $data, '`id` = "'.$id.'"');
                    $pic='';
                    header ('location: /admin/edithall/'.$id);
                }
		if (!empty($_POST)) {
		    $name = $this->getVar('name', '');
    		$preview = $this->getVar('preview', '');
    		$city_id = $this->getVar('city_id', 0);
    		$city_name = $this->getVar('city_name', '');
    		$adres = $this->getVar('adres', '');
    		$pic = $this->getVar('hallpic', null, $_FILES);
		}
		
		if (!empty($_POST)) {
		    if (!$name) {
                $this->addErr('Не заполнено "Название"');
            } else {
                $count = $this->db->fetchOne('SELECT COUNT(`id`) FROM `hall` WHERE `name` = "'.$name.'" AND `id` <> "'.$id.'"');
            
                if ($count > 0) {
                    $this->addErr('Элемент с таким названием уже существует');
                }
            }
            
            if (!$city_name && $city_id < 1) {
                $this->addErr('Выберите город из списка или укажите новый');
            }
            
            if (!$this->_err) {
                if ($city_name) {
                    $city = $this->db->fetchRow("SELECT * FROM `city` WHERE `name` = '$city_name'");
                    
                    if ($city) {
                        $city_id = $city['id'];
                    } else {
                        $data = array(
                            'name' => addslashes($city_name),
                            'href' => $this->ru2Lat($city_name)
                        );
                        
                        $this->db->insert('city', $data);
                        
                        $city_id = $this->db->lastInsertId();
                    }
                } else {
                    if (!ctype_digit($city_id)) {
                        $this->addErr('Выберите город из списка или укажите новый');
                        $city_id = 0;
                    } else {
                        $city = $this->db->fetchRow("SELECT * FROM `city` WHERE `id` = '$city_id'");
                        
                        if (!$city) {
                            $this->addErr('Выберите город из списка или укажите новый');
                            $city_id = 0;
                        }
                    }
                }
            }
            
        }
        
        $hallpic = $hall['pic'];
        if (!empty($_POST) && !$this->_err) {
            if ($pic && $pic['error'] == 0) {
                $extension = explode('.', $pic['name']);
                $extension = end($extension);
                $pic_name = $this->ru2Lat($name).'.'.$extension;
                
                $pic = $this->uploadHallPic($pic['tmp_name'], './images/hall/', $pic_name);
                if ($pic) {
                    $hallpic = $pic_name;
                }
            } else {
                if ($pic) {
                    switch ($pic['error']) {
                        case '1' :
                        case '2' :
                        case '3' :
                            $this->addErr('Ошибка загрузки файла. Обратитесь пожалуйста к Администратору.');
                            break;
                        default: break;
                    }
                }
            }
        }
        
        if (!empty($_POST) && !$this->_err) {
            $this->logger->addLogRow('Редактирование зала', serialize($hall));
            
            $data = array(
                'name'          => $name,
                'preview'       => stripslashes($preview),
                'city_id'       => $city_id,
                'adres'         => $adres,
                'pic'           => $hallpic
            );
          
            $this->db->update("hall", $data, "id = '$id'");
            
            //$hall_id = $this->db->lastInsertId();
            
            /*if (!empty($sectors)) {
                foreach ($sectors as $row) {
                    $data = array(
                        'hall_id' => $hall_id,
                        'name' => $row['name'],
                        'count' => $row['count']
                    );
                    
                    $this->db->insert('sectors', $data);
                }
            }*/
            
            $content = "Элемент успешно добавлен<meta http-equiv='refresh' content='2;URL=/admin/viewhall/$id'>";
            
            $this->viewMessage($content);
        }
        
        if ($this->_err) {
            $this->viewErr();
        }
        
        if (empty($_POST) || $this->_err) {
            $cityArray = $this->db->fetchAll("SELECT * FROM `city` ORDER BY `name`");
            
            $city_list = '<option value="0"'.($city_id == 0 ? ' selected' : '').'>Выберите город</option>';
            
            if ($cityArray) {
                foreach ($cityArray as $row) {
                    $city_list .= '<option value="'.$row['id'].'"'.($row['id'] == $city_id ? ' selected' : '').'>'.$row['name'].'</option>';
                }
            }
            
            $this->tpl->assign(
                array(
                    'ADM_NAME' => $name,
                    'ADM_PREVIEW' => stripslashes($preview),
                    'ADM_CITY_NAME' => $city_name,
                    'ADM_ADRES' => $adres,
                    'ADM_CITY_LIST' => $city_list,
                    'THISHALLPIC'=>'<img src="/images/hall/'.$pic.'">',
                    'THISHALLID'=>$id,
                    'THISHALLPICVIS'=>($pic==''?' display:none; ':' display:block; ')
                )
            );
		
            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.sectors_count');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.name');
			$this->tpl->parse('CONTENT', '.adres');
			$this->tpl->parse('CONTENT', '.preview');
			$this->tpl->parse('CONTENT', '.hallcity');
			$this->tpl->parse('CONTENT', '.hallpic');
			$this->tpl->parse('CONTENT', '.end');
        }
        
        return true;
	}
	
	public function deletehall()
	{
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $hall = $this->db->fetchRow("SELECT * FROM `hall` WHERE `id` = '$id'");
        $sectors = $this->db->fetchAll("SELECT * FROM `sectors` WHERE `hall_id` = '$id'");
        
        if (!$hall) {
            return false;
        }
        
        $this->setMetaTags('Удаление зала');
        
        $this->logger->addLogRow('Удаление зала', serialize($hall));
        $this->logger->addLogRow('Удаление секторов зала', $sectors, 'sub');
        
        $this->db->delete("hall", "id = '$id'");
        $this->db->delete("sectors", "hall_id = '$id'");
        
        if ($hall['pic']) {
            if (file_exists('./images/hall/'.$hall['pic'])) {
                @unlink('./images/hall/'.$hall['pic']);
            }
        }
        
        $content = "Элемент успешно удален<meta http-equiv='refresh' content='2;URL=/admin/hall/'>";
            
        $this->viewMessage($content);
        
        return true;
	}
	
	public function addsectors()
	{
            $this->tpl->assign('ROW_LIST_E', '');
            $this->tpl->assign('ADD_ROW', '');
            $this->tpl->assign('BACK_PAGE', '');
            
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $hall = $this->db->fetchRow("SELECT * FROM `hall` WHERE `id` = '$id' AND `country`='".$_SESSION['countryDName']."'");
        
        if (!$hall) {
            return false;
        }
        
        $this->setMetaTags('Добавление секторов');
        
        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('sectors', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		if (!empty($_POST)) {
		    $array = $_POST['sector'];
		    
		    foreach ($array as $key => $row) {
		        if (!empty($row['name']) && !empty($row['count'])) {
		            $data = array(
                        'hall_id' => $id,
                        'name' => $row['name'],
                        'count' => (int) $row['count'],
                        //'country'=>$_SESSION['countryDName']
		            );
		            
		            $this->logger->addLogRow('Добавление секторов', serialize($data));
		            
		            $this->db->insert('sectors', $data);
                            $idRow = $this->db->lastInsertId();
                            for ($i=0; $i<((int) $row['count']); $i++){
                                    $data = array(
                                        'id_sector' => $idRow,
                                        'id_hall' => $id,
                                        'row_name' => ($i+1),    
                                        'first_location' => 0,
                                        'count_location' => 0
                                    );
                                $this->logger->addLogRow('Добавление рядов', serialize($data));
                                $this->db->insert('series', $data);
                               // echo $i.'<br>';
                            }		            
                            //die;
                            //echo $this->db->lastInsertId(); die;
		        }
		    }
		    
		    $content = "Элементы успешно добавлены<meta http-equiv='refresh' content='2;URL=/admin/viewhall/$id'>";
            
            $this->viewMessage($content);
            
            return true;
		}
		
		$this->tpl->parse('CONTENT', '.start');
		
		$name = '';
		$count = 0;
		
		for ($i=0; $i<5; $i++) {
		    $this->tpl->assign('INCREMENT', $i);
		    $this->tpl->assign(
                array(
                    'SECTOR_NAME' => $name,
                    'SECTOR_COUNT' => $count
                )
            );
            $this->tpl->parse('CONTENT', '.sectors');
		}
        $this->tpl->parse('CONTENT', '.end');
        
        return true;
	}
	
	public function updatesectors()
	{
	    $this->setMetaTags('Редактирование секторов');
	    
	    if (!empty($_POST)) {
	        if (!isset($_POST['hall_id'])) {
	            return false;
	        }
	        
	        if (!ctype_digit($_POST['hall_id']) || $_POST['hall_id'] < 1) {
	            return false;
	        }
	        
	        $this->logger->addLogRow('Редактирование секторов', serialize($_POST), 'main', false);
	        
	        $hall_id = $_POST['hall_id'];
	        unset($_POST['hall_id']);
	        
	        foreach ($_POST as $key => $value) {
	            $sector = explode('sector_count_', $key);
	            
	            if (!ctype_digit($sector[1]) || $sector[1] < 1) {
	                continue;
	            }
	            
	            $data = array('count' => (int) $value);
	            
	            $this->db->update('sectors', $data, '`id` = "'.$sector[1].'"');
	        }
	        
	        $content = "Элементы успешно изменены<meta http-equiv='refresh' content='2;URL=/admin/viewhall/$hall_id'>";
            
            $this->viewMessage($content);
	    }
	    
	    $content = "<meta http-equiv='refresh' content='2;URL=/admin/viewhall/$hall_id'>";
            
        $this->viewMessage($content);
	    
	    return true;
	}
	
	public function editsector()
	{
            $this->tpl->assign(
                array(
                    'ADD_ROW' => ' <a title="Добавить сектора" href="/admin/addrow/{SECTOR_ID}">
                                    <img height="32" width="32" title="Добавить ряд" alt="Добавить ряд" src="/img/admin_icons/add_page.png">
                                    </a>',
                )
            );  
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $sector = $this->db->fetchRow("SELECT * FROM `sectors` WHERE `id` = '$id'");
        
        if (!$sector) {
            return false;
        }
        
        $this->setMetaTags('Редактирование сектора');
        
        $name = $this->getVar('name', '', $sector);
        $count = $this->getVar('count', 0, $sector);
        
        if (!empty($_POST)) {
            $this->logger->addLogRow('Редактирование сектора', serialize($sector));
            
            $name = $this->getVar('name', '', $_POST['sector'][0]);
            $count = $this->getVar('count', '', $_POST['sector'][0]);
            
            $data = array(
                'name' => $name,
                'count' => $count
            );
            
            $this->db->update('sectors', $data, "`id` = '$id'");
            
            $content = "Элемент успешно изменен<meta http-equiv='refresh' content='2;URL=/admin/viewhall/".$sector['hall_id']."'>";
            
            $this->viewMessage($content);
            
            return true;
        }
        $rowListEPrint = ' ';
        $rowListE = $this->db->fetchAll("SELECT * FROM `series` WHERE `id_sector` = '$id'");
        //echo "SELECT * FROM `series` WHERE `id_sector` = '$id'";
        if ($rowListE){
            $rowListEPrint .='<tr><td colspan="2">Ряды сектора</td></tr>';
            foreach ($rowListE as $value) {
                $rowListEPrint .= '<tr><td>Ряд '.$value['row_name'].' ('.$value['first_location'].') ('.$value['count_location'].') </td>
                    <td><a href="/admin/editrow/'.$value['id'].'" title="Редактировать"><img src="/img/admin_icons/edit.png" width="16" height="16" alt="Редактировать" title="Редактировать" /></a>&nbsp;&nbsp;&nbsp;<a href="/admin/deleterow/'.$value['id'].'" title="Удалить"><img src="/img/admin_icons/delete.png" width="16" height="16" alt="Удалить" title="Удалить" /></a></td></tr>';
            }
        }
        
        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('sectors', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
        
        $this->tpl->assign(
            array(
                'INCREMENT' => '0',
                'SECTOR_NAME' => $name,
                'SECTOR_COUNT' => $count,
                'SECTOR_ID'=>$id,
                'ROW_LIST_E' => (!empty($rowListEPrint)?$rowListEPrint:''),
                'BACK_PAGE' => '<a href="/admin/viewhall/'.$value['id_hall'].'">Вернуться назад</a>'
            )
        );
        
        $this->tpl->parse('CONTENT', '.start');
		$this->tpl->parse('CONTENT', '.sectors');
        $this->tpl->parse('CONTENT', '.end');
        
        
        return true;
	}
	
	public function deletesector()
	{
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $sector = $this->db->fetchRow("SELECT * FROM `sectors` WHERE `id` = '$id'");
        
        if (!$sector) {
            return false;
        }
        
        $this->setMetaTags('Удаление сектора');
        
        $this->logger->addLogRow('Удаление сектора', serialize($sector));
        
        $this->db->delete("sectors", "id = '$id'");
        
        $content = "Элемент успешно удален<meta http-equiv='refresh' content='2;URL=/admin/viewhall/".$sector['hall_id']."'>";
            
        $this->viewMessage($content);
        
        return true;
	}
	
	
	public function events()
	{
	    $this->setMetaTags('Семинары');
	    
	    $this->viewMessage($this->getAdminAdd('events', ''));
	    
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
	    
	    $this->tpl->define_dynamic('events', "admin/events.tpl");
	    $this->tpl->define_dynamic('filter', "events");
	    $this->tpl->define_dynamic('list', "events");
            $this->tpl->define_dynamic('country', "events");
	    $this->tpl->define_dynamic('list_row', "list");
	    
	    $event_types = $this->db->fetchAll("SELECT * FROM `event_types` WHERE `country` = '".$_SESSION['countryDName']."' ORDER BY `position`");
	    $city = $this->db->fetchAll("SELECT * FROM `city` WHERE `country` = '".$_SESSION['countryDName']."' ORDER BY `name` ");
	    
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
	    
	    if ($this->convertDate($filter_start) != $this->convertDate($filter_end) && $filter_end > $filter_start) {
	        $select->where('date >= ?', array("$filter_start"));
	        $select->where('date <= ?', array("$filter_end"));
	        $navGet .= 'filter_start='.$this->convertDate($filter_start).'&filter_end='.$this->convertDate($filter_end).'&';
	    }
	    
	    if ($filter_city != 'all') {
	        $select->where('city_id = ?', array("$filter_city"));
	        $navGet .= 'filter_city='.$filter_city.'&';
	    }
	    $select->where('events.country = ?', $_SESSION['countryDName']);
	    if ($navGet == '?') $navGet = '';
	    if ($navGet != '') $navGet = substr($navGet, 0, -1);
	    
	    $select->order('date DESC');
	    
	    $sql = $select->__toString();
	    //echo $sql; die();
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
                
                $navbar = $this->loadPaginator((int) ceil($count_rows/$num_rows), (int) $page, $this->basePath.'admin/events/'.$navGet);
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
    	    $select->where('events.country = ?', $_SESSION['countryDName']);
    	    $select->order('events.date DESC');
    	    $select->limit($num_rows, $start);
    	    //echo $select->__toString();
    	    $events = $this->db->fetchAll($select);
    	    
    	    foreach ($events as $row) {
    	        $pic = '<div class="img">&nbsp;</div>';
    	        if ($row['pic'] && file_exists('./images/events/'.$row['pic'])) {
    	            $pic = '<div class="img"><img src="/images/events/'.$row['pic'].'" width="118" height="100" alt="'.$row['name'].'" title="'.$row['name'].'" /></div>';
    	        }
    	        $times = $this->convertDate($row['date'], 'H:i');
    	        $this->tpl->assign(
    	           array(
    	               'EVENT_ID' => $row['id'],
    	               'EVENT_NAME' => $this->getAdminEdit('event', $row['id']).$row['name'],
    	               'EVENT_DATE' => $this->convertDate($row['date']),
                       'EVENT_DATE2' => ($row['date']>=$row['date2']?'':' - '.$this->convertDate($row['date2'])),
    	               'EVENT_CITY' => $row['city_name'],
    	               'EVENT_ADRES' => $row['adres'],
    	               'EVENT_TIME' => ((!empty($times) and $times!='00:00')?'<p>Время начала:<strong>'.$times.'</strong></p>':''),
    	               'EVENT_TYPE' => $row['type_name'],
    	               'EVENT_PREVIEW' => stripslashes($row['preview']),
    	               'EVENT_PIC' => ($pic!=''?'<a href="/admin/viewevent/'.$row['id'].'">'.$pic.'</a>':'')
    	           )
    	        );
    	        
    	        $this->tpl->parse('LIST_ROW', '.list_row');
    	    }
    	    $this->tpl->assign(
    	           array(
                       'CLASS_ACTIVE_RU'    =>  ($_SESSION['countryDName']=='ru'?' class="acrive"':' class="noacrive"'),
                       'CLASS_ACTIVE_UA'    =>  ($_SESSION['countryDName']=='ua'?' class="acrive"':' class="noacrive"')

    	           )
    	        );

    	    $this->tpl->parse('CONTENT', '.list');
	    } else {
	        $this->viewMessage('Семинаров не найдено');
	    }
	    
	    return true;
	}
	
	public function viewevent()
	{
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        if (isset($_POST['close'])){
           $data = array(
                'no_sell_tickets'   =>  '1'
            );
            $this->db->update("events", $data, "id = '$id'");
        }
        if (isset($_POST['open'])){
           $data = array(
                'no_sell_tickets'   =>  '0'
            );
            $this->db->update("events", $data, "id = '$id'");
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
	    
	    $this->setMetaTagsAdmin($event['name'], $this->getAdminEdit('event', $event['id']));
	    
	    $this->tpl->define_dynamic('events', "admin/events.tpl");
	    $this->tpl->define_dynamic('detail', "events");
	    $this->tpl->define_dynamic('sectors_row', "detail");
	    
	    $pic = '';
	    if ($event['pic'] && file_exists('./images/events/'.$event['pic'])) {
	        $pic = '<div class="img"><img src="/images/events/'.$event['pic'].'" width="118" height="100" alt="'.$event['name'].'" title="'.$event['name'].'" /></div>';
	    }
	    
	    $hall_pic = '';
	    if ($event['hall_pic'] && file_exists('./images/hall/'.$event['hall_pic'])) {
	        $hall_pic = '<div class="fl_left">
        <img src="/images/hall/'.$event['hall_pic'].'" width="249" height="142" alt="'.$event['hall_name'].'" title="'.$event['hall_name'].'" />
        <p><a href="/images/hall/big/'.$event['hall_pic'].'" title="Увеличить" target="_blanc">Увеличить <img src="/img/plus.gif" width="12" height="12" alt="Увеличить" title="Увеличить" /></a></p>
    </div>';
	    }
	    $times = $this->convertDate($event['date'], 'H:i');
	    $this->tpl->assign(
	       array(
	           'EVENT_ID' => $event['id'],
	           'EVENT_PIC' => $pic,
	           'EVENT_DATE' => $this->convertDate($event['date']),
                   'EVENT_DATE2' => ($event['date']>=$event['date2']?'':' - '.$this->convertDate($event['date2'])),
	           'EVENT_CITY' => $event['city_name'],
	           'EVENT_ADRES' => $event['adres'],
	           'EVENT_TIME' => ((!empty($times) and $times!='00:00')?'<p>Время начала:<strong>'.$times.'</strong></p>':''),
	           'EVENT_TYPE' => $event['type_name'],
	           'EVENT_PREVIEW' => stripslashes($event['preview']),
	           'EVENT_BODY' => stripslashes($event['body']),
	           'HALL_PIC' => $hall_pic,
			   'HALL_PIC_H3' => (!empty($hall_pic)?'Описание зала':''),
	           'HALL_PREVIEW' => stripslashes($event['hall_preview']),
                   'SUBMIT_NAME' => ($event['no_sell_tickets']==1?'open':'close'),
                   'SUBMIT_VALUE' => ($event['no_sell_tickets']==1?'Отарыть продажу билетов':'Закрыть продажу билетов')
	       )
	    );
	    
	    $select = $this->db->select();
	    $select->from('sectors', array('*'));
	    $select->joinLeft('sectors_event', 'sectors_event.sector_id = sectors.id AND event_id = "'.$event['id'].'"', array('reservation', 'event_id'));
	    $select->where('hall_id = ?', array("".$event['hall_id'].""));
	    $select->order('sectors.id ASC');
	    
	    $sectors = $this->db->fetchAll($select);
	    //print_r($sectors);
	    if ($sectors) {
	    	foreach ($sectors as $row) {
	    	    $available = (int) $row['count']-$row['reservation'];
	    	    
	    	    $sector_info = '';
	    	    if ($this->auth->id == 1) {
	    	        $sector_info = 's:'.$row['id'].';e:'.$event['id'].';&nbsp;&nbsp;&nbsp;';
	    	    }
	    	    
	    		$this->tpl->assign(
	    			array(
	    				'SECTOR_ROW_ID' => $row['id'],
	    				'SECTOR_ROW_NAME' => $sector_info.$row['name'],
	    				'SECTOR_ROW_COUNT' => $row['count'],
	    				'SECTOR_ROW_AVAILABLE' => $available
	    			)
	    		);
	    		
	    		$this->tpl->parse('SECTORS_ROW', '.sectors_row');
	    	}
	    } else {
	    	$this->tpl->parse('SECTORS_ROW', 'null');
	    }
	    
	    $this->tpl->parse('CONTENT', '.detail');
	    
	    return true;
	}
	
	public function addevent()
	{
	    $this->setMetaTags('Добавление семинара');
	    
	    $this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('name', 'edit');
		$this->tpl->define_dynamic('preview', 'edit');
		$this->tpl->define_dynamic('hallselect', 'edit');
		$this->tpl->define_dynamic('eventpic', 'edit');
		$this->tpl->define_dynamic('eventtype', 'edit');
		$this->tpl->define_dynamic('eventdatetime', 'edit');
                $this->tpl->define_dynamic('eventdatetime2', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
                $this->tpl->define_dynamic('ed_f', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		$name = $this->getVar('name', '');
		$preview = $this->getVar('preview', '');
		$body = $this->getVar('body', '');
                $ed1 = $this->getVar('ed1', '');
                $ed2 = $this->getVar('ed2', '');
		$hall_id = $this->getVar('hall_id', 0);
		$type_id = $this->getVar('type_id', 0);
		$pic = $this->getVar('eventpic', null, $_FILES);
		$date = $this->getVar('eventdate', date('d.m.Y'));
                $date2 = $this->getVar('eventdate2', date('d.m.Y'));
		$time = $this->getVar('eventtime', '');
				
		if (!empty($_POST)) {
		    if (!$name) {
                $this->addErr('Не заполнено "Название"');
            } else {
                $count = $this->db->fetchOne('SELECT COUNT(`id`) FROM `events` WHERE `name` = "'.$name.'"');
            
                if ($count > 0) {
                    $this->addErr('Элемент с таким названием уже существует');
                }
            }
            
            $dateValidator = new Zend_Validate_Date(array('format' => 'dd.mm.yyyy'));
            if (!$date || !$dateValidator->isValid($date)) {
            	$this->addErr('Не правильная дата проведения семинара');
            }
            
            $timeValidator = new Zend_Validate_Date(array('format' => 'HH:ii'));
            //if (!$time || !$timeValidator->isValid($time)) {
            //	$this->addErr('Не указано время начала семинара');
            //}
            
            if (!$hall_id || $hall_id < 1) {
                $this->addErr('Выберите зал из списка');
            } else {
            	$hall = $this->db->fetchOne("SELECT COUNT(`id`) FROM `hall` WHERE `id` = '$hall_id'");
            	
            	if ($hall != 1) {
            		$this->addErr('Выберите зал из списка');
            		$hall_id = 0;
            	}
            }
            
            if (!$type_id || $type_id < 1) {
            	$this->addErr('Выберите тип семинара из списка');
            } else {
            	$type = $this->db->fetchOne("SELECT COUNT(`id`) FROM `event_types` WHERE `id` = '$type_id'");
            	
            	if ($type != 1) {
            		$this->addErr('Выберите тип семинара из списка');
            		$type_id = 0;
            	}
            }
        }
        
        $eventpic = '';
        if (!empty($_POST) && !$this->_err) {
            if ($pic && $pic['error'] == 0) {
                $extension = explode('.', $pic['name']);
                $extension = end($extension);
                $pic_name = $this->ru2Lat($name).'.'.$extension;
                
                $pic = $this->uploadEventPic($pic['tmp_name'], './images/events/', $pic_name);
                if ($pic) {
                    $eventpic = $pic_name;
                }
            } else {
                if ($pic) {
                    switch ($pic['error']) {
                        case '1' :
                        case '2' :
                        case '3' :
                            $this->addErr('Ошибка загрузки файла. Обратитесь пожалуйста к Администратору.');
                            break;
                        default: break;
                    }
                }
            }
        }
        
        if (!empty($_POST) && !$this->_err) {
        	$date = explode('.', $date);
                $date2 = explode('.', $date2);
        	if (!empty($time)) {
                    $time = explode(':', $time);
                } else {
                    $time['0'] = 0;
                    $time['1'] = 0;
                }
        	
        	$date = mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
                $date2 = mktime($time[0], $time[1], 0, $date2[1], $date2[0], $date2[2]);
        	
            $data = array(
                'hall_id'		=> $hall_id,
                'type_id'		=> $type_id,
                'name'			=> $name,
                'date'			=> $date,
                'date2'			=> $date2,
                'date_create'           => mktime(),
                'preview'		=> stripslashes($preview),
                'body'			=> stripslashes($body),
                'ed1'			=> $ed1,
                'ed2'			=> $ed2,
                'pic'			=> $eventpic,
                'country'               => $_SESSION['countryDName']
            );
          
            $this->logger->addLogRow('Добавление семинара', serialize($data));
            
            $this->db->insert("events", $data);
            
            $event_id = $this->db->lastInsertId();
            
            $sectors = $this->db->fetchAll("SELECT * FROM `sectors` WHERE `hall_id` = '$hall_id'");
            
            if ($sectors) {
            	foreach ($sectors as $row) {
            		$data = array(
            			'sector_id' => $row['id'],
            			'event_id' => $event_id
            		);
            		
            		$this->db->insert('sectors_event', $data);
            	}
            }
            
            $content = "Элемент успешно добавлен<meta http-equiv='refresh' content='2;URL=/admin/viewevent/$event_id'>";
            
            $this->viewMessage($content);
        }
        
        if ($this->_err) {
            $this->viewErr();
        }
        
        if (empty($_POST) || $this->_err) {
            $hallArray = $this->db->fetchAll("SELECT * FROM `hall` WHERE `country`='".$_SESSION['countryDName']."' ORDER BY `city_id`, `name`");
            
            $hall_list = '<option value="0"'.($hall_id == 0 ? ' selected' : '').'>Выберите зал</option>';
            
            if ($hallArray) {
                foreach ($hallArray as $row) {
                    $hall_list .= '<option value="'.$row['id'].'"'.($row['id'] == $hall_id ? ' selected' : '').'>'.$row['name'].'</option>';
                }
            }
            
            $typeArray = $this->db->fetchAll("SELECT * FROM `event_types` WHERE `country`='".$_SESSION['countryDName']."'");
            
            $type_list = '<option value="0"'.($type_id == 0 ? ' selected' : '').'>Выберите тип</option>';
            
            if ($typeArray) {
                foreach ($typeArray as $row) {
                    $type_list .= '<option value="'.$row['id'].'"'.($row['id'] == $type_id ? ' selected' : '').'>'.$row['name'].'</option>';
                }
            }
            
            $this->tpl->assign(
                array(
                    'ADM_NAME' => $name,
                    'ADM_PREVIEW' => stripslashes($preview),
                    'ADM_BODY' => stripslashes($body),
                    'ADM_HALL_LIST' => $hall_list,
                    'ADM_TYPE_LIST' => $type_list,
                    'ADM_DATE' => $date,
                    'ADM_DATE2' => $date2,
                    'ADM_TIME' => $time,
                    'ED1' => $ed1,
                    'ED2' => $ed2
                )
            );
			
            $this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.name');
			$this->tpl->parse('CONTENT', '.eventpic');
			$this->tpl->parse('CONTENT', '.preview');
			$this->tpl->parse('CONTENT', '.hallselect');
			
			$this->tpl->parse('CONTENT', '.eventtype');
			$this->tpl->parse('CONTENT', '.eventdatetime');
			$this->tpl->parse('CONTENT', '.body');
                        $this->tpl->parse('CONTENT', '.ed_f');
			
			$this->tpl->parse('CONTENT', '.end');
        }
        
        return true;
	}
	
	public function editevent()
	{
		$id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $event = $this->db->fetchRow("SELECT * FROM `events` WHERE `id` = '$id'");
        
        if (!$event) {
        	return false;
        }
        
	    $this->setMetaTags('Редактирование семинара: '.$event['name']);
	    
	    $this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('name', 'edit');
		$this->tpl->define_dynamic('preview', 'edit');
		$this->tpl->define_dynamic('hallselect', 'edit');
		$this->tpl->define_dynamic('eventpic', 'edit');
		$this->tpl->define_dynamic('eventtype', 'edit');
		$this->tpl->define_dynamic('eventdatetime', 'edit');
                $this->tpl->define_dynamic('eventdatetime2', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
                $this->tpl->define_dynamic('ed_f', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		$name = $this->getVar('name', '', $event);
		$preview = $this->getVar('preview', '', $event);
		$body = $this->getVar('body', '', $event);
		$hall_id = $this->getVar('hall_id', 0, $event);
		$type_id = $this->getVar('type_id', 0, $event);
		$date = $this->getVar('date', null, $event);
                $date2 = $this->getVar('date2', null, $event);
                $ed1 = $this->getVar('ed1', null, $event);
                $ed2 = $this->getVar('ed2', null, $event);
		
		if (null === $date) {
                    $date = date('d.m.Y');
                    $date2 = date('d.m.Y');
                    $time = '00:00';
                    //$time = '';
		} else {
                    //echo $date; die;
                    $time = $this->convertDate($date, 'H:i');
                    $date = $this->convertDate($date);
                    $date2 = $this->convertDate($date2);
                    //$time = $this->convertDate($date, 'H:i');
                    //echo $time;
		}
		
		if (!empty($_POST)) {
			$name = $this->getVar('name', '');
			$preview = $this->getVar('preview', '');
			$body = $this->getVar('body', '');
                        $ed1 = $this->getVar('ed1', '');
                        $ed2 = $this->getVar('ed2', '');
			$hall_id = $this->getVar('hall_id', 0);
			$type_id = $this->getVar('type_id', 0);
			$pic = $this->getVar('eventpic', null, $_FILES);
			$date = $this->getVar('eventdate', date('d.m.Y'));
                        $date2 = $this->getVar('eventdate2', date('d.m.Y'));
			//$time = $this->getVar('eventtime', '12:00');
                        $time = $this->getVar('eventtime', '');
		
		    if (!$name) {
                $this->addErr('Не заполнено "Название"');
            } else {
                $count = $this->db->fetchOne('SELECT COUNT(`id`) FROM `events` WHERE `name` = "'.$name.'" AND `id` <> "'.$id.'"');
            
                if ($count > 0) {
                    $this->addErr('Элемент с таким названием уже существует');
                }
            }
            
            $dateValidator = new Zend_Validate_Date(array('format' => 'dd.mm.yyyy'));
            if (!$date || !$dateValidator->isValid($date)) {
            	$this->addErr('Не правильная дата проведения семинара');
            }
            
            $timeValidator = new Zend_Validate_Date(array('format' => 'HH:ii'));
            //if (!$time || !$timeValidator->isValid($time)) {
            //	$this->addErr('Не указано время начала семинара');
            //}
            
            if (!$hall_id || $hall_id < 1) {
                $this->addErr('Выберите зал из списка');
            } else {
            	$hall = $this->db->fetchOne("SELECT COUNT(`id`) FROM `hall` WHERE `id` = '$hall_id'");
            	
            	if ($hall != 1) {
            		$this->addErr('Выберите зал из списка');
            		$hall_id = 0;
            	}
            }
            
            if (!$type_id || $type_id < 1) {
            	$this->addErr('Выберите тип семинара из списка');
            } else {
            	$type = $this->db->fetchOne("SELECT COUNT(`id`) FROM `event_types` WHERE `id` = '$type_id'");
            	
            	if ($type != 1) {
            		$this->addErr('Выберите тип семинара из списка');
            		$type_id = 0;
            	}
            }
        }
        
        $eventpic = $event['pic'];
        if (!empty($_POST) && !$this->_err) {
            if ($pic && $pic['error'] == 0) {
                $extension = explode('.', $pic['name']);
                $extension = end($extension);
                $pic_name = $this->ru2Lat($name).'.'.$extension;
                
                $pic = $this->uploadEventPic($pic['tmp_name'], './images/events/', $pic_name);
                if ($pic) {
                    $eventpic = $pic_name;
                }
            } else {
                if ($pic) {
                    switch ($pic['error']) {
                        case '1' :
                        case '2' :
                        case '3' :
                            $this->addErr('Ошибка загрузки файла. Обратитесь пожалуйста к Администратору.');
                            break;
                        default: break;
                    }
                }
            }
        }
        
        if (!empty($_POST) && !$this->_err) {
            $this->logger->addLogRow('Редактирование семинара', serialize($event));
            
        	$date = explode('.', $date);
                $date2 = explode('.', $date2);
                if (!empty($time)){
                    $time = explode(':', $time);
                }else{
                    $time[0]=0; $time[1]=0;
                }
        	
        	$date = mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
                $date2 = mktime($time[0], $time[1], 0, $date2[1], $date2[0], $date2[2]);
        	
            $data = array(
                'hall_id'		=> $hall_id,
                'type_id'		=> $type_id,
                'name'			=> $name,
                'date'			=> $date,
                'date2'			=> $date2,
                'preview'		=> stripslashes($preview),
                'body'			=> stripslashes($body),
                'ed1'			=> $ed1,
                'ed2'			=> $ed2,
                'pic'			=> $eventpic
            );
          
            $this->db->update("events", $data, "id = '$id'");
            
            $content = "Элемент успешно изменен<meta http-equiv='refresh' content='2;URL=/admin/viewevent/$id'>";
            
            $this->viewMessage($content);
        }
        
        if ($this->_err) {
            $this->viewErr();
        }
        
        if (empty($_POST) || $this->_err) {
            $hallArray = $this->db->fetchAll("SELECT * FROM `hall` ORDER BY `city_id`, `name`");
            
            $hall_list = '<option value="0"'.($hall_id == 0 ? ' selected' : '').'>Выберите зал</option>';
            
            if ($hallArray) {
                foreach ($hallArray as $row) {
                    $hall_list .= '<option value="'.$row['id'].'"'.($row['id'] == $hall_id ? ' selected' : '').'>'.$row['name'].'</option>';
                }
            }
            //echo $_SESSION['countryDName'];
            $typeArray = $this->db->fetchAll("SELECT * FROM `event_types` WHERE `country`='".$_SESSION['countryDName']."'");
            
            $type_list = '<option value="0"'.($type_id == 0 ? ' selected' : '').'>Выберите тип</option>';
            
            if ($typeArray) {
                foreach ($typeArray as $row) {
                    $type_list .= '<option value="'.$row['id'].'"'.($row['id'] == $type_id ? ' selected' : '').'>'.$row['name'].'</option>';
                }
            }

            //echo $date;
            $this->tpl->assign(
                array(
                    'ADM_NAME' => $name,
                    'ADM_PREVIEW' => stripslashes($preview),
                    'ADM_BODY' => stripslashes($body),
                    'ADM_HALL_LIST' => $hall_list,
                    'ADM_TYPE_LIST' => $type_list,
                    'ADM_DATE' => $date,
                    'ADM_DATE2' => $date2,
                    'ADM_TIME' => ($time!='00:00'?$time:''),
                    'ED1' => $ed1,
                    'ED2' => $ed2
                )
            );
			
            $this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.name');
			$this->tpl->parse('CONTENT', '.eventpic');
			$this->tpl->parse('CONTENT', '.preview');
			$this->tpl->parse('CONTENT', '.hallselect');
			
			$this->tpl->parse('CONTENT', '.eventtype');
			$this->tpl->parse('CONTENT', '.eventdatetime');
			$this->tpl->parse('CONTENT', '.body');
			$this->tpl->parse('CONTENT', '.ed_f');
			$this->tpl->parse('CONTENT', '.end');
        }
        
        return true;
	}
	
	public function deleteevent()
	{
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $event = $this->db->fetchRow("SELECT * FROM `events` WHERE `id` = '$id'");
        $sectors = $this->db->fetchAll("SELECT * FROM `sectors_event` WHERE `event_id` = '$id'");
        
        if (!$event) {
        	return false;
        }
        
	    $this->setMetaTags('Удаление семинара: '.$event['name']);
	    
	    $this->logger->addLogRow('Удаление семинара', serialize($event));
        $this->logger->addLogRow('Удаление секторов семинара', $sectors, 'sub');
	    
	    $this->db->delete("events", "id = '$id'");
	    $this->db->delete("sectors_event", "event_id = '$id'");
	    
	    if ($event['pic'] && file_exists('./images/events/'.$event['pic'])) {
	    	@unlink('./images/events/'.$event['pic']);
	    }
	    
	    $content = "Элемент успешно удален<meta http-equiv='refresh' content='2;URL=/admin/events/'>";
            
        $this->viewMessage($content);
        
        return true;
	}
	
	public function eventtype()
	{
	    $this->setMetaTags('Типы семинаров');
	    
	    if (!empty($_POST)) {
	        $this->logger->addLogRow('Типы семинаров', serialize($_POST));
	        
	        foreach ($_POST as $key => $value) {
	            $data = array(
	               'cost' => (float) $value
	            );
	            
	            $this->db->update('event_types', $data, "`id` = '$key'");
	        }
	        
	        $this->tpl->assign('CONTENT', '<p>Цены успешно изменены</p>');
            
            $this->redirect('/admin/eventtype/', false, 2);
            
            return true;
	    }
	    
	    $this->tpl->define_dynamic('event_type', "admin/event_type.tpl");
	    //$this->tpl->define_dynamic('event_type', "_event_type");
	    $this->tpl->define_dynamic('event_type_row', "event_type");
	    
	    $types = $this->db->fetchAll("SELECT * FROM `event_types` ORDER BY `position`");
	    
	    foreach ($types as $row) {
	        $this->tpl->assign(
	           array(
	               'TYPE_ID' => $row['id'],
	               'TYPE_NAME' => $row['name'],
	               'TYPE_COST' => (float) $row['cost'],
                       'COUNTRY_NAME' => $row['country'],
                       'MONEY_NAME' => ($row['country']=='ru'?'руб':'грн')
	           )
	        );
	        
	        $this->tpl->parse('EVENT_TYPE_ROW', '.event_type_row');
	    }
	    
	    $this->tpl->parse('CONTENT', 'event_type');
	    
	    return true;
	}
	
	
	
	public function unpaid()
	{
		$this->setMetaTags('Неоплаченные заказы');
		
		$select = $this->db->select();
		$select->from('events', array('id', 'name', 'date', 'pic'));
		$select->join('hall', 'events.hall_id = hall.id', array('adres'));
		$select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
		$select->join('event_types', 'events.type_id = event_types.id', array('event_type' => 'name'));
		$select->join('sectors', 'hall.id = sectors.hall_id', array());
		$select->join('tickets', 'tickets.event_id = events.id AND tickets.sector_id = sectors.id', array('ticket_id' => 'id', 'count', 'payment', 'ticket_date' => 'date', 'ticket_number' => 'code','nb'=>'number'));
		$select->join('users', 'tickets.user_id = users.id', array('number'));
		//$select->where('tickets.user_id = ?', array("$user_id"));
		$select->where('events.date > ?', array("".mktime().""));
		//$select->where('tickets.date < ?', array("".mktime(date('H'), date('i'), date('s'), date('m'), date('d')-3, date('Y')).""));
		$select->where('tickets.payment = ?', array('0'));
                $select->where('tickets.ids = ?', '0');
		$select->order('ticket_date DESC');
                $select->order('tickets.id ASC');
		//echo $select->__toString();
		$tickets = $this->db->fetchAll($select);
		
		$this->tpl->define_dynamic('unpaid', "admin/unpaid.tpl");
		$this->tpl->define_dynamic('list', "unpaid");
		$this->tpl->define_dynamic('list_row', "list");
		$oldNumber='';
		if ($tickets) {
			foreach ($tickets as $row) {
                            if ($oldNumber!=$row['nb']){
                                $oldNumber=$row['nb'];
				$pic = '';
				if ($row['pic'] && file_exists('./images/events/'.$row['pic'])) {
					$pic = '<div class="img"><img src="/images/events/'.$row['pic'].'" width="118" height="100" alt="'.$row['name'].'" title="'.$row['name'].'" /></div>';
				}
				$times = $this->convertDate($row['date'], 'H:i');
                                echo $times;
				$this->tpl->assign(
					array(
						'EVENT_ID' => $row['id'],
						'EVENT_NAME' => $row['name'],
						'TICKET_NUMBER' => $row['ticket_number'],
						'TICKET_DATE' => $this->convertDate($row['ticket_date']),
						'EVENT_DATE' => $this->convertDate($row['date']),
                                                //'EVENT_DATE2' => ($row['date']>=$row['date2']?'':' - '.$this->convertDate($row['date2'])),
                                                'EVENT_DATE2' => '',
						'EVENT_CITY' => $row['city_name'],
						'EVENT_ADRES' => $row['adres'],
						'EVENT_TIME' => ((!empty($times) or $times!='00:00')?'<p>Время начала:<strong>'.$times.'</strong></p>':''),//$this->convertDate($row['date'], 'H:i'),
						'EVENT_TYPE' => $row['event_type'],
						'EVENT_TICKETS' => $row['count'],
						'USER_NUMBER' => $row['number'],
						'TICKET_ID' => $row['ticket_id'],
						'EVENT_PIC' => $pic
					)
				);
				
				$this->tpl->parse('LIST_ROW', '.list_row');
			}
                }
			$this->tpl->parse('CONTENT', '.list');
		} else {
			$this->viewMessage('Неоплаченных заказов не найдено');
		}
		
		return true;
	}
	/*
	public function ticketprint()
	{
	    $id = $this->getVar('ticketId', null, $_POST);
        //echo $id; die;
        if (null === $id || !ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            //die('1');
            return false;
        }
        
        $select = $this->db->select();
        $select->from('tickets', array('id', 'count', 'payment', 'ticket_date' => 'date', 'row', 'location', 'lastNumber'));
        $select->join('events', 'events.id = tickets.event_id', array('event_id' => 'id', 'event_name' => 'name', 'event_date' => 'date', 'pic'));
        $select->join('event_types', 'event_types.id = type_id', array('event_type' => 'name'));
        $select->join('hall', 'hall.id = events.hall_id', array('adres'));
        $select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
        $select->join('sectors', 'sectors.id = tickets.sector_id', array('sector_name' => 'name'));
        $select->join('users', 'users.id = tickets.user_id', array('user_id' => 'id', 'user_name' => 'name', 'surname', 'number'));
        $select->where('tickets.id = ?', array("$id"));
        
        $ticket = $this->db->fetchAll($select);
        
        if (!$ticket || sizeof($ticket) != 1) {
            //die('2');
        	return false;
        }
        
        $ticket = $ticket[0];
        $ticketId = $ticket['id'];
        
        $scannerCode = $this->db->fetchAll("SELECT * FROM `tickets_code` WHERE `ticket_id` = '$ticketId'");
        
        if (!$scannerCode) {
            die('2');
            return false;
        }
        
        require_once "library/Pdf.php";
        //die('12');
        //var_dump($ticket); die;
        $myPdf = new My_Pdf($ticket, $scannerCode);
        $pdfString = $myPdf->__toString();
        
        /*$pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        
        
        $page->drawRectangle(30, 820, 566.2, 672.6, Zend_Pdf_Page::SHAPE_DRAW_STROKE);
        
        $image = Zend_Pdf_Image::imageWithPath('./images/events/big/'.$ticket['pic']);
        $page->drawImage($image, 38.9, 687.2, 132.7, 796.2);
        
        $pdf->pages[] = $page;
        $pdfString = $pdf->render(); * /
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
	*/
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
	public function ticketdelete()
	{
		$id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $ticket = $this->db->fetchRow("SELECT `payment`, `event_id`, `sector_id` FROM `tickets` WHERE `id` = '$id' LIMIT 1");
        
        if (!$ticket) {
        	return false;
        }
        
        $this->logger->addLogRow('Удаление заказа', serialize($ticket));
        
        if ($ticket['payment'] == 1) {
            $this->viewMessage("Заказ уже оплачен. Удаление навозможно.");
        }
        
        $this->setMetaTags('Удаление заказа');
	    $this->db->delete("tickets", "id = '$id'");
            $this->db->delete("tickets_code", "ticket_id = '$id'");
            $delOther = $this->db->fetchAll("SELECT `id` FROM `tickets` WHERE `ids`='$id'");
            if ($delOther){
                foreach ($delOther as $iCanDel) {
                    $this->db->delete("tickets", "id = '".$iCanDel['id']."'");
                    $this->db->delete("tickets_code", "ticket_id = '".$iCanDel['id']."'");
                }
            }
            
	    
	    $available = (int) $ticket['reservation'] - $ticket['count'];
	    $this->db->update("sectors_event", array('reservation' => $available), "event_id = '".$ticket['event_id']."' AND sector_id = '".$ticket['sector_id']."'");
	    
	    $content = "Элемент успешно удален<meta http-equiv='refresh' content='2;URL=/admin/unpaid/'>";
            
        $this->viewMessage($content);
        
        return true;
	}
	
	public function importpaid()
	{
		$this->setMetaTags('Импорт оплаченных заказов');
        
        $this->tpl->define_dynamic('import', "admin/importpaid.tpl");
        $this->tpl->define_dynamic('import_form', "import");
        
        $this->tpl->parse('IMPORT_FORM', '.import_form');
        
        if (!empty($_POST) && isset($_POST['import'])) {
            $file = $this->getVar('import', null, $_FILES);
            
            if (null === $file) {
            	$this->addErr('Выберите файл для загрузки');
            }
            
        	if (!$this->_err && $file['error'] == '4') {
        		$this->addErr('Выберите файл для загрузки');
            }
            
            if (!$this->_err && $file['error'] != '0') {
            	$this->addErr('Ошибка загрузки файла на сервер');
            }
            
            if (!$this->_err) {
            	set_include_path(PATH . 'excel/');
                include_once PATH . 'excel/PHPExcel/IOFactory.php';
                $exel = PHPExcel_IOFactory::load($file['tmp_name']);
                
                $aSheet = $exel->getActiveSheet();
            
                $array = $aSheet->toArray();
                
                if (!empty($array)) {
                    array_shift($array);
                    
                    if (sizeof($array) < 1) {
                    	$this->addErr('Файл не содержит записей');
                    }
                } else {
                    $this->addErr('Файл не содержит записей');
                }
                
                if (!$this->_err) {
                    $this->logger->addLogRow('Импорт оплаченных заказов');
                    
                    foreach ($array as $row) {
                        $number = $this->getVar(0, null, $row);
                        
                        if (null === $number) {
                        	continue;
                        }
                        
                        $number = (int) $number;
                        
                        if ($number < 1) {
                        	$this->addErr('Неверное значение номера заказа: '.$row[0]);
                        	continue;
                        }
                        
                        $n = $this->db->update("tickets", array('payment' => '1'), "code = '$number'");
                        
                        if ($n == 0) {
                        	$this->addErr('Заказ не найден в базе: '.$row[0]);
                        }
                    }
                }
            }
        }
        
        $this->tpl->parse('CONTENT', 'import');
        
        if ($this->_err) {
        	$this->viewErr();
        }
        
        return true;
	}
    
	
	public function searchcode()
	{
	    $this->setMetaTags('Поиск билета по штрих-коду');
	    
	    $code = $this->getVar('code', null, $_POST);
	    
	    $this->tpl->define_dynamic('_searchcode', "admin/searchcode.tpl");
	    $this->tpl->define_dynamic('searchcode', "_searchcode");
	    $this->tpl->define_dynamic('searchcode_empty', "searchcode");
	    $this->tpl->define_dynamic('searchcode_form', "searchcode");
	    $this->tpl->define_dynamic('searchcode_list', "searchcode");
	    $this->tpl->define_dynamic('searchcode_list_item', "searchcode_list");
	    
	    $this->tpl->parse('CONTENT', '.searchcode_form');
	    
	    if (null === $code) {
	        return true;
	    }
	    
	    $select = $this->db->select();
		
	    $select->from('tickets_code', array('code'));
	    $select->joinLeft('tickets', 'tickets_code.ticket_id = tickets.id', array('payment'));
	    $select->joinLeft('events', 'tickets.event_id = events.id', array('hall_id', 'event' => 'name', 'date', 'pic'));
	    $select->joinLeft('event_types', 'events.type_id = event_types.id', array('type' => 'name'));
	    $select->joinLeft('hall', 'events.hall_id = hall.id', array('adres'));
	    $select->joinLeft('city', 'hall.city_id = city.id', array('city' => 'name'));
	    $select->joinLeft('sectors', 'tickets.sector_id = sectors.id', array('sector' => 'name'));
	    $select->joinLeft('users', 'tickets.user_id = users.id', array('name', 'surname', 'amway' => 'number'));
	    
	    $select->where('tickets_code.code LIKE "%'.$code.'%"');
	    
	    
	    /*$select->from('events', array('id', 'name', 'date', 'pic'));
		
		$select->join('hall', 'events.hall_id = hall.id', array('adres'));
		$select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
		$select->join('event_types', 'events.type_id = event_types.id', array('event_type' => 'name'));
		$select->join('sectors', 'hall.id = sectors.hall_id', array());
		$select->join('tickets', 'tickets.event_id = events.id AND tickets.sector_id = sectors.id', array('ticket_id' => 'id', 'count', 'payment', 'ticket_date' => 'date', 'ticket_number' => 'code'));
		$select->join('users', 'tickets.user_id = users.id', array('number'));
		
		$select->where('events.date > ?', array("".mktime().""));*/
		
		//echo $select->__toString();
		//exit();
		$tickets = $this->db->fetchAll($select);
	    //print_r($tickets);
	    
	    if ($tickets) {
    	    require_once "library/singlePdf.php";
            
            $myPdf = new Single_Pdf($tickets);
            $pdfString = $myPdf->__toString();
            
            ini_set('max_execution_time', '360');
            header('Content-type: application/pdf');
            echo $pdfString;
            
            exit();
	    }
	    
	    $this->tpl->parse('CONTENT', '.searchcode_empty');
	    
	    return true;
	}
	
	
	public function sectorscanner()
	{
	    //print_r($_POST);
	    
	    $this->setMetaTags('Экспорт билетов для сканера');
        
        $this->logger->addLogRow('Экспорт билетов для сканера');
        
        $event = $this->getVar('event', 0, $_POST);
        $sector = $this->getVar('export', null, $_POST);
        //print_r($sector);
        
        if (!ctype_digit($event) || $event < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        if (null === $sector || empty($sector)) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $sectorList = '';
        
        $select = $this->db->select();
        $select->from('tickets', array('id', 'count', 'payment', 'ticket_date' => 'date', 'lastNumber'));
        $select->join('events', 'events.id = tickets.event_id', array('event_id' => 'id', 'event_name' => 'name', 'event_date' => 'date', 'pic'));
        $select->join('event_types', 'event_types.id = type_id', array('event_type' => 'name'));
        $select->join('hall', 'hall.id = events.hall_id', array('adres'));
        $select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
        $select->join('sectors', 'sectors.id = tickets.sector_id', array('sector_id' => 'id', 'sector_name' => 'name'));
        $select->join('users', 'users.id = tickets.user_id', array('user_id' => 'id', 'user_name' => 'name', 'surname', 'number', 'amway' => 'number'));
        $select->where('tickets.event_id = ?', array("$event"));
        
        foreach ($sector as $key => $value) {
            if ($sectorList != '') {
                $select->orWhere('tickets.sector_id = ?', array("$key"));
                $sectorList .= ', ';
            } else {
                $select->where('tickets.sector_id = ?', array("$key"));
            }
            
            $sectorList .= $key;
        }
        
        $select->order('tickets.date');
        //echo $select;
        $ticket = $this->db->fetchAll($select);
        //print_r($ticket);
        //return true;
        $locale = new Zend_Locale('ru_UA');
        Zend_Date::setOptions(array('format_type' => 'php'));
        
        //$item = '';
        
        if (!$ticket) {
            $this->tpl->assign('CONTENT', '<div class="rzd_list">Заказанных билетов для данного сектора не найдено.</div>');
            return true;
        }
        
        //$i = 1;
        $d = new Zend_Date($ticket[0]['event_date'], false, $locale);
        $tcNumber = $this->addZero($ticket[0]['event_id']);
	    $eventDate = $d->toString('d.m.Y');
	    $sector = 'Sector-'.$ticket[0]['sector_id'];
        
        $item = "ТС-$tcNumber
";
	    $item .= "$eventDate
";
	    $item .= "Sectors: $sectorList
";
        
        foreach ($ticket as $row) {
            //$d = new Zend_Date($row['ticket_date'], false, $locale);
            $scannerCode = $this->db->fetchAll("SELECT * FROM `tickets_code` WHERE `ticket_id` = '".$row['id']."'");
            if (sizeof($scannerCode) > 1) {
                foreach ($scannerCode as $sc) {
                //for ($i=0; $i<$row['count'];$i++) {
                    //$lastNumber = (int) ($row['lastNumber'] + ($i+1));
                    //$code = (int) $d->toString('dmy').$lastNumber;
                    
                    $barcode = new Zend_Barcode_Object_Ean13();
                    $barcode->setText($sc['code']);
                    $scannerCode = substr($barcode->getTextToDisplay(), 1);
                    $number = $row['number'];
                    $amway = $row['amway'];
                    $fullName = $row['user_name'].' '.$row['surname'];
                    
                    $item .= "$scannerCode;$number;$amway;$fullName;чел.;1;0
";
                }
            } elseif (sizeof($scannerCode) == 1) {
                //$code = $d->toString('dmy').($row['lastNumber']+1);
                
                $barcode = new Zend_Barcode_Object_Ean13();
                $barcode->setText($scannerCode[0]['code']);
                $scannerCode = substr($barcode->getTextToDisplay(), 1);
                $number = $row['number'];
                $amway = $row['amway'];
                $fullName = $row['user_name'].' '.$row['surname'];
                
                $item .= "$scannerCode;$number;$amway;$fullName;чел.;1;0
";
            }
            
            //$i++;
        }
        
        $destinationFolder = './ticketexport/'.$row['event_id']."_".$row['sector_id'];
        if (!is_dir($destinationFolder)) {
            mkdir($destinationFolder);
            @chmod($destinationFolder, 0777);
        }
        
        $filename = $destinationFolder."/inventory.csv";
        
        $fp = fopen($filename, 'w+');
	    
	    //fputs($fp, $file);
	    fputs($fp, $item);
	    
	    fclose($fp);
	    @chmod($filename, 0777);
        
	    //$int = 1;
	    //echo (string) $int;
	    
	    
	    $filename = $destinationFolder."/goods.csv";
	    
	    
	    $fp = fopen($filename, 'w+');
	    
	    //fputs($fp, $file);
	    //fputs($fp, $item);
	    
	    fclose($fp);
	    @chmod($filename, 0777);
	    
	    $this->tpl->define_dynamic('_list', 'admin/exportscanner.tpl');
        $this->tpl->define_dynamic('list', '_list');
        $this->tpl->define_dynamic('list_row', 'list');
        
        $this->tpl->assign(
            array(
                'FILE_URL' => substr($destinationFolder, 1).'/goods.csv',
                'FILE_NAME' => 'goods.csv'
            )
        );
        
        $this->tpl->parse('LIST_ROW', '.list_row');
        
        $this->tpl->assign(
            array(
                'FILE_URL' => substr($destinationFolder, 1).'/inventory.csv',
                'FILE_NAME' => 'inventory.csv'
            )
        );
        
        $this->tpl->parse('LIST_ROW', '.list_row');
        
        $this->tpl->parse('CONTENT', '.list');
	    
        //ini_set('max_execution_time', '360');
        //header("Content-type: application/vnd.ms-excel");
        //header("Content-disposition: attachment; filename=\"http://upline24.com.ua/ticketexport/".$row['event_id']."_".$row['sector_id'].".csv\"");
        //echo $pdfString;
        //exit();
        /*$d = new Zend_Date($ticket[0]['event_date'], false, $locale);
        $code = (int) $d->toString('dmy').$ticket[0]['lastNumber'];
        
        $barcode = new Zend_Barcode_Object_Ean13();
        $barcode->setText($code);
        $scannerCode = $barcode->getTextToDisplay();*/
        //var_dump($scannerCode);
        //var_dump($barcode->getTextToDisplay());
        
        return true;
	}
	
	
	
	public function adddisc()
    {
        $this->setMetaTags('Добавление диска ('.$_SESSION['countryName'].')');
        $this->setWay('Добавление диска ');
        
        $this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('pic', 'edit');
		$this->tpl->define_dynamic('name', 'edit');
		//$this->tpl->define_dynamic('adress', 'edit');
		$this->tpl->define_dynamic('meta', 'edit');
		$this->tpl->define_dynamic('visible', 'edit');
		$this->tpl->define_dynamic('preview', 'edit');
		$this->tpl->define_dynamic('cost', 'edit');
		$this->tpl->define_dynamic('artikul', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
                $this->tpl->define_dynamic('ed_f', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		//$href = $this->ru2Lat($this->getVar('adm_href', ''));
		//$date = $this->getVar('date', date('d.m.Y'));
		$name = $this->getVar('name', '');
		$preview = $this->getVar('preview', '');
		$header = $this->getVar('header', '');
		$title = $this->getVar('title', '');
		$keywords = $this->getVar('keywords', '');
		$description = $this->getVar('description', '');
		$visible = $this->getVar('visible', 1);
		$body = $this->getVar('body', '');
                $ed1 = $this->getVar('ed1', '');
                $ed2 = $this->getVar('ed2', '');
		$cost = $this->getVar('cost', 0);
		$articul = $this->getVar('artikul', 0);
		
		$visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }
        
        if (!empty($_POST)) {
            $referer = $this->getVar('HTTP_REFERER', '');
            
            if (!$name) {
                $this->addErr('Не заполнено название');
            }
            
            if (!$header) {
                $this->addErr('Поле "Header" не заполнено');
            }
        } else {
            $referer = $this->getVar('HTTP_REFERER', $this->basePath);
        }
        
        if (!empty($_POST) && !$this->_err) {
            $data = array(
                'name'          => $name,
                'cost'          => (float) $cost,
                'articul'       => $articul,
                'preview'       => $preview,
                'header'        => $header,
                'title'         => $title,
                'keywords'      => $keywords,
                'description'   => $description,
                'visibility'    => $visible,
                'body'          => $body,
                'ed1'          => $ed1,
                'ed2'          => $ed2,
                'country'       => $_SESSION['countryDName']
            );
            
            $this->logger->addLogRow('Добавление диска', serialize($data));
            
            $id = $this->db->fetchOne("SELECT MAX(`id`) FROM `discs`") + 1;
            
            if ($pic = $this->getVar('pic')) {
            	if (!empty($pic['name']) && $pic['error'] == 0) {
                	if (file_exists(PATH.'images/discs/'.$id.'-'.$pic['name'])) {
                    	@unlink(PATH.'images/discs/'.$id.'-'.$pic['name']);
                    }
                    
                    //if (!$this->uploadDiscPic($pic['tmp_name'], './images/disc/'.$id.'-'.$pic['name'], 140, 105, 100)) {
                    if (!$this->uploadDiscPic($pic['tmp_name'], './images/discs/', $id.'-'.$pic['name'])) {
                        $this->addErr('Во время загрузки картинки произошла ошибка');
                    }
                }
                
                $data['pic'] = $id.'-'.$pic['name'];
            }
        }
            
        if (!empty($_POST) && !$this->_err) {
            $this->db->insert("discs", $data);
            
            $referer = (!empty($referer) && $referer != '{REFERER}') ? $referer : $this->basePath;
            $content = "Диск успешно добавлен<meta http-equiv='refresh' content='2;URL=$referer'>";
            //$content = "Диск успешно добавлен";
            
            $this->viewMessage($content);
        }
        
        if ($this->_err) {
            $this->viewErr();
        }
        
        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(
                array(
                    'ADM_COST' => (float) $cost,
                    'ADM_ARTIKUL' => $articul,
                    'ADM_NAME' => $name,
                    'ADM_PREVIEW' => $preview,
                    'ADM_BODY' => $body,
                    'ED1' => $ed1,
                    'ED2' => $ed2,
                    'VISIBLE_S' => $visible_s,
                    'ADM_HEADER' => $header,
                    'ADM_TITLE' => $title,
                    'ADM_KEYWORDS' => $keywords,
                    'ADM_DESCRIPTION' => $description,
                    //'ADM_COUNTRY_NAME' => $_SESSION['country']['name'],
                    'SHOW_PIC' =>'',
                    'REFERER' => $referer
                )
            );
		
            $this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.name');
			$this->tpl->parse('CONTENT', '.artikul');
			$this->tpl->parse('CONTENT', '.cost');
			$this->tpl->parse('CONTENT', '.pic');
			$this->tpl->parse('CONTENT', '.visible');
			$this->tpl->parse('CONTENT', '.preview');
			$this->tpl->parse('CONTENT', '.meta');
			$this->tpl->parse('CONTENT', '.body');
                        $this->tpl->parse('CONTENT', '.ed_f');
			$this->tpl->parse('CONTENT', '.end');
        }
        
        return true;
    }
    
    public function editdisc()
    {
        $id = end($this->url);
        
        if (!ctype_digit($id)) {
            return $this->error404();
        }
        
        $disc = $this->db->fetchRow("SELECT * FROM `discs` WHERE `id` = '$id'");
        
        if (!$disc) {
            return $this->error404();
        }
        
        $this->setMetaTags('Редактирование диска');
        $this->setWay('Редактирование диска');
        
        $this->tpl->define_dynamic('start', 'edit');
		$this->tpl->define_dynamic('mce', 'edit');
		$this->tpl->define_dynamic('pic', 'edit');
		$this->tpl->define_dynamic('name', 'edit');
		//$this->tpl->define_dynamic('adress', 'edit');
		$this->tpl->define_dynamic('meta', 'edit');
		$this->tpl->define_dynamic('visible', 'edit');
		$this->tpl->define_dynamic('preview', 'edit');
		$this->tpl->define_dynamic('cost', 'edit');
		$this->tpl->define_dynamic('artikul', 'edit');
		$this->tpl->define_dynamic('body', 'edit');
                $this->tpl->define_dynamic('ed_f', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
        
		$name = $disc['name'];
		$preview = $disc['preview'];
		$header = $disc['header'];
		$title = $disc['title'];
		$keywords = $disc['keywords'];
		$description = $disc['description'];
		$visible = $disc['visibility'];
		$body = stripcslashes($disc['body']);
                $ed1 = $disc['ed1'];
                $ed2 = $disc['ed2'];
		$cost = $disc['cost'];
		$articul = $disc['articul'];
		
		if (!empty($_POST)) {
		    $referer = $this->getVar('HTTP_REFERER', '');
		    
		    $name = $this->getVar('name', '');
    		$preview = $this->getVar('preview', '');
    		$header = $this->getVar('header', '');
    		$title = $this->getVar('title', '');
    		$keywords = $this->getVar('keywords', '');
    		$description = $this->getVar('description', '');
    		$visible = $this->getVar('visible', 1);
    		$body = $this->getVar('body', '');
                $ed1 = $this->getVar('ed1', '');
                $ed2 = $this->getVar('ed2', '');
    		$cost = $this->getVar('cost', 0);
    		$articul = $this->getVar('artikul', 0);
    		
    		if (!$name) {
                $this->addErr('Не заполнено название');
            }
            
            if (!$header) {
                $this->addErr('Поле "Header" не заполнено');
            }
		} else {
		    $referer = $this->getVar('HTTP_REFERER', $this->basePath);
		}
		
		$visible_s = '';
        if ($visible == 1) {
            $visible_s .= "<option value='1' selected>Да</option>
            <option value='0'>Нет</option>";
        } else {
            $visible_s .= "<option value='1'>Да</option>
            <option value='0' selected>Нет</option>";
        }
        
        if (!empty($_POST) && !$this->_err) {
            $data = array(
                'name'          => $name,
                'cost'          => (float) $cost,
                'articul'       => $articul,
                'preview'       => $preview,
                'header'        => $header,
                'title'         => $title,
                'keywords'      => $keywords,
                'description'   => $description,
                'visibility'    => $visible,
                'body'          => $body,
                'ed1'          => $ed1,
                'ed2'          => $ed2
            );
            
            if ($pic = $this->getVar('pic')) {             
            	if (!empty($pic['name']) && $pic['error'] == 0) {
            		if (file_exists(PATH.'images/discs/'.$id.'-'.$pic['name'])) {
                    	@unlink(PATH.'images/discs/'.$id.'-'.$pic['name']);
                    }
                    
                    if (!$this->uploadDiscPic($pic['tmp_name'], './images/discs/', $id.'-'.$pic['name'])) {
                        $this->addErr('Во время загрузки картинки произошла ошибка');
                    }
            		
            		$data['pic'] = $id.'-'.$pic['name'];
            	} elseif(isset($data['pic'])) {
            		unset($data['pic']);
            	}
                
            }
        }
            
        if (!empty($_POST) && !$this->_err) {
            $this->logger->addLogRow('Редактирование диска', serialize($disc));
            
            $n = $this->db->update('discs', $data, "id = $id");
            
            $referer = (!empty($referer) && $referer != '{REFERER}') ? $referer : $this->basePath;
            $content = "Диск успешно изменен<meta http-equiv='refresh' content='2;URL=$referer'>";
            
            $this->viewMessage($content);
        }
        
        if ($this->_err) {
            $this->viewErr();
        }
        
        if (empty($_POST) || $this->_err) {
            $this->tpl->assign(
                array(
                    'ADM_COST' => (float) $cost,
                    'ADM_ARTIKUL' => $articul,
                    'ADM_NAME' => $name,
                    'ADM_PREVIEW' => $preview,
                    'ADM_BODY' => $body,
                    'ED1' => $ed1,
                    'ED2' => $ed2,
                    'VISIBLE_S' => $visible_s,
                    'ADM_HEADER' => $header,
                    'ADM_TITLE' => $title,
                    'ADM_KEYWORDS' => $keywords,
                    'ADM_DESCRIPTION' => $description,
                    'REFERER' => $referer
                )
            );
		
            $this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.name');
			$this->tpl->parse('CONTENT', '.artikul');
			$this->tpl->parse('CONTENT', '.cost');
			$this->tpl->parse('CONTENT', '.pic');
			$this->tpl->parse('CONTENT', '.visible');
			$this->tpl->parse('CONTENT', '.preview');
			$this->tpl->parse('CONTENT', '.meta');
			$this->tpl->parse('CONTENT', '.body');
                        $this->tpl->parse('CONTENT', '.ed_f');
			$this->tpl->parse('CONTENT', '.end');
        }
        
        return true;
    }
    
    public function deletedisc()
    {
        $id = end($this->url);
        
        if (!ctype_digit($id)) {
            return $this->error404();
        }
        
        $disc = $this->db->fetchRow("SELECT * FROM `discs` WHERE `id` = '$id'");
        
        if (!$disc) {
            return $this->error404();
        }
        
        $this->setMetaTags('Удаление диска');
        $this->setWay('Удаление диска');
        
        if ($disc['pic'] != '' && file_exists('./images/discs/'.$disc['pic'])) {
            @unlink('./images/discs/'.$disc['pic']);
        }
        
        if ($disc['pic'] != '' && file_exists('./images/discs/big/'.$disc['pic'])) {
            @unlink('./images/discs/big/'.$disc['pic']);
        }
        
        $this->logger->addLogRow('Удаление диска', serialize($disc));
        
        $n = $this->db->delete('discs', "id = $id");
        
        $referer = $this->getVar('HTTP_REFERER', $this->basePath);
        
        $content = "Диск успешно удален<meta http-equiv='refresh' content='2;URL=$referer'>";
		$this->viewMessage($content);
		
		return true;
    }
	
    public function orders()
    {
        $this->setMetaTags('Заказанные диски');
        
        $select = $this->db->select();
		$select->from('order_disc', array('*', 'summ' => 'SUM(order_disc_items.summ)'));
		$select->join('order_disc_items', 'order_disc.id = order_disc_items.order_id', array());
                
                $select->where('order_disc.country = ?', $_SESSION['countryDName']);
                
		//$select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
		//$select->join('event_types', 'events.type_id = event_types.id', array('event_type' => 'name'));
		//$select->join('sectors', 'hall.id = sectors.hall_id', array());
		//$select->join('tickets', 'tickets.event_id = events.id AND tickets.sector_id = sectors.id', array('ticket_id' => 'id', 'count', 'payment'));
		//$select->where('order_disc.user_id = ?', array("$user_id"));
		$select->group('order_disc.id');
		$select->order('date DESC');
		//$select->group('events.id');
		//echo $select->__toString();
		$purchases = $this->db->fetchAll($select);
		
		$this->tpl->define_dynamic('discs', 'admin/orders.tpl');
		$this->tpl->define_dynamic('discs_order_list', 'discs');
                $this->tpl->define_dynamic('country', 'discs');
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
			            $status = 'доставлен';
				        $statusClass = 'gr';
				        break;
				}
				
				$this->tpl->assign(
					array(
						'ORDER_ID' => $row['id'],
						'ORDER_DATE' => $this->convertDate($row['date']),
						'ORDER_NUMBER' => $row['id'],
						'ORDER_SUMM' => $row['summ'],
						'ORDER_STATUS_CLASS' => $statusClass,
						'ORDER_STATUS' => $status
					)
				);
				
				$this->tpl->parse('DISCS_ORDER_LIST_ROW', '.discs_order_list_row');
			}
		} else {
			$this->tpl->parse('DISCS_ORDER_LIST_ROW', 'null');
			$this->tpl->parse('DISCS_ORDER_LIST_EMPTY', '.discs_order_list_empty');
		}
                
                $this->tpl->assign(
    	           array(
                       'CLASS_ACTIVE_RU'    =>  ($_SESSION['countryDName']=='ru'?' class="acrive"':' class="noacrive"'),
                       'CLASS_ACTIVE_UA'    =>  ($_SESSION['countryDName']=='ua'?' class="acrive"':' class="noacrive"')

    	           )
    	        );
                
		$this->tpl->parse('CONTENT', '.country');
		$this->tpl->parse('CONTENT', '.discs_order_list');
		
		return true;
    }
    
    public function viewOrder()
	{
	    //$user_id = $this->auth->id;
	    
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $order = $this->db->fetchRow("SELECT `order_disc`.*, `users`.`number`, `users`.`email` FROM `order_disc`, `users` WHERE `users`.`id` = `order_disc`.`user_id` AND `order_disc`.`id` = '$id'");
        
        if (!$order) {
            return false;
        }
        
        /*if ($order['user_id'] != $user_id) {
            return false;
        }*/
        
        $items = $this->db->fetchAll("SELECT `order_disc_items`.*, `discs`.`name`, `discs`.`articul`, `discs`.`cost` FROM `order_disc_items`, `discs` WHERE `order_id` = '$id' AND `order_disc_items`.`disc_id` = `discs`.`id` ORDER BY `discs`.`name`");
        
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
                    'PRINT_ORDER_DISC_SUMM' => $row['summ']
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
                'PRINT_ORDER_EMAIL' => $order['email'],
                'PRINT_ORDER_INFO' => $order['info'],
                'PRINT_ORDER_TOTAL_SUMM' => $totalSumm,
                'ADMIN_AMWAY_TEXT' => '<br />Номер Amway:',
                'ADMIN_AMWAY_NUMBER' => '<br />'.$order['number'],
                'DISC3'=>$countDisc.' шт.',
                'FOLDER'=>($order['country']=='ua'?'/ua':''),
                'MONEY3' => ($order['country']=='ua'?'грн.':'руб.')

            )
        );
        
        return true;
	}
    
	
    public function feedback()
    {
        $this->tpl->define_dynamic('feedback', "admin/feedback.tpl");
        $this->tpl->define_dynamic('feedback_list', "feedback");
        $this->tpl->define_dynamic('feedback_list_row', "feedback_list");
        $this->tpl->define_dynamic('feedback_list_row_read', "feedback_list_row");
        //$this->tpl->define_dynamic('feedback_detail', "feedback");
        $this->setMetaTags('Сообщения формы обратной связи');
        
        $feedback = $this->db->fetchAll("SELECT * FROM `feedback` ORDER BY `date` DESC");
        
        if ($feedback) {
            foreach ($feedback as $row) {
                $date = $this->convertDate($row['date'], "d.m.Y H:i:s");
                $this->tpl->assign(
                    array(
                        'FEED_FIO' => $row['status'] == 1 ? '<span class="new_message">'.$row['name'].'</span>' : $row['name'],
                        'FEED_EMAIL' => $row['email'],
                        'FEED_PHONE' => $row['phone'],
                        'FEED_DATE' => $date,
                        'FEED_ID' => $row['id']
                    )
                );
                
                if ($row['status'] == '0') {
                    $this->tpl->parse('FEEDBACK_LIST_ROW_READ', 'null');
                } else {
                    $this->tpl->parse('FEEDBACK_LIST_ROW_READ', 'feedback_list_row_read');
                }
                
                $this->tpl->parse('FEEDBACK_LIST_ROW', '.feedback_list_row');
            }
            
            $this->tpl->parse('CONTENT', 'feedback_list');
        } else {
            $this->tpl->assign('CONTENT', '<p>Сообщений не найдено</p>');
        }
        
        return true;
    }
    
    public function viewfeedback()
    {
        $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $message = $this->db->fetchRow("SELECT * FROM `feedback` WHERE `id` = '$id'");
        
        if (!$message) {
            // Левый ID
            return false;
        }
        
        $data = array('status' => '0');
        
        $this->db->update('feedback', $data, "id = '$id'");
        
        $this->tpl->define_dynamic('feedback', "admin/feedback.tpl");
        $this->tpl->define_dynamic('feedback_detail', "feedback");
        
        $date = $this->convertDate($message['date'], "d.m.Y H:i:s");
        
        $this->setMetaTags('Сообщение формы обратной связи: '.$date);
        
        $this->tpl->assign(
            array(
                'FEED_FIO' => $message['name'],
                'FEED_EMAIL' => $message['email'],
                'FEED_PHONE' => $message['phone'],
                'FEED_MESSAGE' => $message['message'],
                'FEED_DATE' => $date,
                'FEED_ID' => $message['id']
            )
        );
        
        $this->tpl->parse('CONTENT', 'feedback_detail');
        
        return true;
    }
    
    public function deletefeedback()
    {
        $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $message = $this->db->fetchRow("SELECT * FROM `feedback` WHERE `id` = '$id'");
        
        if (!$message) {
            // Левый ID
            return false;
        }
        
        $this->logger->addLogRow('Удаление сообщения формы обратной связи', serialize($message));
        
        $this->setMetaTags('Удаление сообщения формы обратной связи');
        
        $this->db->delete('feedback', "id = '$id'");
        
        $this->tpl->assign('CONTENT', '<p>Сообщение успешно удалено</p>');
        
        $this->redirect('/admin/feedback/', false, 2);
        
        return true;
    }
    
    public function setreadfeedback()
    {
        $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $message = $this->db->fetchRow("SELECT * FROM `feedback` WHERE `id` = '$id'");
        
        if (!$message) {
            // Левый ID
            return false;
        }
        
        $this->setMetaTags('Сообщение формы обратной связи');
        
        $this->db->update('feedback', array('status' => '0'), "id = '$id'");
        
        $this->tpl->assign('CONTENT', '<p>Сообщение было помечено как прочитанное</p>');
        
        $this->redirect('/admin/feedback/', false, 2);
        
        return true;
    }
    
    public function system()
    {
        $this->setMetaTags('Системные страницы сайта');
        
        $this->tpl->define_dynamic('system', "admin/system.tpl");
        $this->tpl->define_dynamic('system_list', "system");
        $this->tpl->define_dynamic('system_list_row', "system_list");
        
        $system = $this->db->fetchAll("SELECT * FROM `system` ORDER BY `id`");
        
        if ($system) {
            foreach ($system as $row) {
                $this->tpl->assign(
                    array(
                        'SYSTEM_NAME'   => $row['name'],
                        'SYSTEM_ID'     => $row['id']
                    )
                );
                
                $this->tpl->parse('SYSTEM_LIST_ROW', '.system_list_row');
            }
            
            $this->tpl->parse('CONTENT', 'system_list');
        } else {
            $this->tpl->assign('CONTENT', '<p>Системных страниц не найдено. Обратитесь пожалуйста к администратору</p>');
        }
        
        return true;
    }
    
    public function editsystem()
    {
        $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $system = $this->db->fetchRow("SELECT * FROM `system` WHERE `id` = '$id'");
        
        if (!$system) {
            // Левый ID
            return false;
        }
        
        $this->setMetaTags('Редактирование страницы: '.$system['name']);
        
        //$this->logger->addLogRow('Редактирование страницы: '.$system['name']);
        
        $this->tpl->define_dynamic('system', "admin/system.tpl");
        $this->tpl->define_dynamic('system_edit', "system");
        $this->tpl->define_dynamic('system_false', "system_edit");
        
        $header         = $system['header'];
        $title          = $system['title'];
        $keywords       = $system['keywords'];
        $description    = $system['description'];
        $body           = $system['body'];
        $ed1            = $system['ed1'];
        $ed2            = $system['ed2'];
        
        $error = false;
        
        if (!empty($_POST) && isset($_POST['edit_system'])) {
            $header         = $this->getVar('edit_header', '', $_POST);
            $title          = $this->getVar('edit_title', '', $_POST);
            $keywords       = $this->getVar('edit_keywords', '', $_POST);
            $description    = $this->getVar('edit_description', '', $_POST);
            $body           = $this->getVar('edit_body', '', $_POST);
            $ed1            = $this->getVar('ed1', '', $_POST);
            $ed2            = $this->getVar('ed2', '', $_POST);
            
            if (!$header || !$title) {
                $error = true;
            }
        }
        
        if (!empty($_POST) && isset($_POST['edit_system']) && !$error) {
            $this->logger->addLogRow('Редактирование страницы', serialize($system));
            
            $data = array(
                'header'        => stripslashes($header),
                'title'         => stripslashes($title),
                'keywords'      => stripslashes($keywords),
                'description'   => stripslashes($description),
                'body'          => stripslashes($body),
                'ed1'           => ($ed1),
                'ed2'           => ($ed2)
            );
            
            $this->db->update('system', $data, "id = '$id'");
            
            $this->tpl->assign('CONTENT', '<p>Страница успешно отредактирована</p>');
        
            $this->redirect('/admin/system/', false, 2);
            
            return true;
        }
        
        $this->tpl->assign(
            array(
                'SYSTEM_ID'             => $system['id'],
                'SYSTEM_NAME'           => $system['name'],
                'SYSTEM_HEADER'         => stripslashes($header),
                'SYSTEM_TITLE'          => stripslashes($title),
                'SYSTEM_KEYWORDS'       => stripslashes($keywords),
                'SYSTEM_DESCRIPTION'    => stripslashes($description),
                'SYSTEM_BODY'           => stripslashes($body),
                'ED1'           => $ed1,
                'ED2'           => $ed2
            )
        );
        
        if ($error) {
            $this->tpl->parse('SYSTEM_FALSE', '.system_false');
        } else {
            $this->tpl->parse('SYSTEM_FALSE', 'null');
        }
        
        $this->tpl->parse('CONTENT', 'system_edit');
        
        return true;
    }
    
    public function settings()
    {
        $this->setMetaTags('Настройки сайта');
        
        $this->tpl->define_dynamic('settings', "admin/settings.tpl");
        
        $settings = $this->db->fetchAll("SELECT * FROM `settings`");
        
        if (!$settings) {
            $this->tpl->assign('CONTENT', '<p>Настроек не найдено. Обратитесь пожалуйста к администратору</p>');
            
            return true;
        }
        
        if (!empty($_POST) && isset($_POST['edit_settings'])) {
            $this->logger->addLogRow('Настройки сайта', serialize($settings));
            $this->logger->addLogRow('Настройки сайта: новые', $_POST, 'sub', false);
            unset($_POST['edit_settings']);
            foreach ($_POST as $key => $value) {
                if (!$value && $key != 'use_auth') {
                    continue;
                }
                
                $this->db->update('settings', array('value' => stripslashes($value)), "`key` = '$key'");
            }
            
            $this->tpl->assign('CONTENT', '<p>Настройки успешно изменены</p>');
            
            $this->redirect('/admin/settings/', false, 2);
            
            return true;
        }
        
        foreach ($settings as $row) {
            if ($row['key'] == 'use_auth') {
                if ($row['value'] == '1') {
                    $this->tpl->assign(
                        array(
                            'USE_AUTH_TRUE' => 'checked',
                            'USE_AUTH_FALSE' => ''
                        )
                    );
                } else {
                    $this->tpl->assign(
                        array(
                            'USE_AUTH_TRUE' => '',
                            'USE_AUTH_FALSE' => 'checked'
                        )
                    );
                }
            } else {
                $this->tpl->assign(
                    array(
                        strtoupper($row['key']) => stripslashes($row['value'])
                    )
                );
            }
        }
        
        $this->tpl->parse('CONTENT', 'settings');
        
        return true;
    }
    
    public function lookups()
    {
        $this->setMetaTags('Редактируемые поля');
        
        $this->tpl->define_dynamic('lookups', "admin/lookups.tpl");
        
        $lookups = $this->db->fetchAll("SELECT * FROM `lookups`");
        
        if (!$lookups) {
            $this->tpl->assign('CONTENT', '<p>Редактируемых полей не найдено. Обратитесь пожалуйста к администратору</p>');
            
            return true;
        }
        
        if (!empty($_POST) && isset($_POST['edit_lookups'])) {
            $this->logger->addLogRow('Редактируемые поля', serialize($lookups));
            $this->logger->addLogRow('Редактируемые поля: новые', $_POST, 'sub', false);
            unset($_POST['edit_lookups']);
            foreach ($_POST as $key => $value) {
                if (!$value) {
                    continue;
                }
                
                if ($key != 'welcome_block') {
                	$value = strip_tags($value, '<br>');
                }
                
                $this->db->update('lookups', array('value' => stripslashes($value)), "`key` = '$key'");
            }
            
            $this->tpl->assign('CONTENT', '<p>Редактируемые поля успешно изменены</p>');
            
            $this->redirect('/admin/lookups/', false, 2);
            
            return true;
        }
        
        foreach ($lookups as $row) {
            $this->tpl->assign(
                array(
                    strtoupper($row['key']) => stripslashes($row['value'])
                )
            );
        }
        
        $this->tpl->parse('CONTENT', 'lookups');
        
        return true;
    }
    
    public function metatags()
	{
	    $this->setMetaTags('Мета-Теги');
        $this->setWay('Мета-Теги');
	   
	    $meta = $this->db->fetchAll("SELECT `id`, `name` FROM `meta_tags` ORDER BY `id`");
	   
	    $this->tpl->define_dynamic('meta', "admin/meta.tpl");
        $this->tpl->define_dynamic('meta_list', "meta");
        $this->tpl->define_dynamic('meta_list_row', "meta_list");
        
        foreach ($meta as $item) {
            $this->tpl->assign(
                array(
                	'META_ID' => $item['id'],
                    'META_NAME' => stripslashes($item['name']),
                    
                    
                )
            );
            
            $this->tpl->parse('META_LIST_ROW', '.meta_list_row');
        }
        
        $this->tpl->parse('CONTENT', 'meta_list');
        
        return true;
	}
	
	public function editmetatag()
	{
	    $id = end($this->url);
	    
	    if (!ctype_digit($id)) {
	        return $this->error404();
	    }
	    
	    $meta = $this->db->fetchRow("SELECT * FROM `meta_tags` WHERE `id` = $id");
	    
	    if (!$meta) {
	        return false;
	    }
	    
	    $this->setMetaTags('Мета-Теги : '.$meta['name']);
	    
	    $this->tpl->define_dynamic('meta', "admin/meta.tpl");
        $this->tpl->define_dynamic('meta_edit', "meta");
        $this->tpl->define_dynamic('meta_false', "meta_edit");
        
        $header         = $meta['header'];
        $title          = $meta['title'];
        $keywords       = $meta['keywords'];
        $description    = $meta['description'];
        $body           = $meta['body'];
        $ed1           = $meta['ed1'];
        $ed2           = $meta['ed2'];
        
        $error = false;
		
        if (!empty($_POST) && isset($_POST['edit_meta'])) {
            $header         = $this->getVar('edit_header', '', $_POST);
            $title          = $this->getVar('edit_title', '', $_POST);
            $keywords       = $this->getVar('edit_keywords', '', $_POST);
            $description    = $this->getVar('edit_description', '', $_POST);
            $body           = $this->getVar('edit_body', '', $_POST);
            $ed1           = $this->getVar('ed1', '', $_POST);
            $ed2           = $this->getVar('ed2', '', $_POST);
            
            if (!$header || !$title) {
                $error = true;
            }
        }
        
        if (!empty($_POST) && isset($_POST['edit_meta']) && !$error) {
            $this->logger->addLogRow('Мета-Теги : '.$meta['name'], serialize($meta));
            
            $data = array(
                'header'        => stripslashes($header),
                'title'         => stripslashes($title),
                'keywords'      => stripslashes($keywords),
                'description'   => stripslashes($description),
                'body'          => stripslashes($body),
                'ed1'          => stripslashes($ed1),
                'ed2'          => stripslashes($ed2)
            );
            
            $this->db->update('meta_tags', $data, "id = '$id'");
            
            $this->tpl->assign('CONTENT', '<p>Страница успешно отредактирована</p>');
        
            $this->redirect('/admin/metatags/', false, 2);
            
            return true;
        }
        
        $this->tpl->assign(
            array(
                'META_ID'             => $meta['id'],
                'META_NAME'           => $meta['name'],
                'META_HEADER'         => stripslashes($header),
                'META_TITLE'          => stripslashes($title),
                'META_KEYWORDS'       => stripslashes($keywords),
                'META_DESCRIPTION'    => stripslashes($description),
                'META_BODY'           => stripslashes($body),
                'ED1'           => $ed1,
                'ED2'           => $ed2
            )
        );
        
        if ($error) {
            $this->tpl->parse('META_FALSE', '.meta_false');
        } else {
            $this->tpl->parse('META_FALSE', 'null');
        }
        
        $this->tpl->parse('CONTENT', 'meta_edit');
        
        return true;
        
		if (!empty($_POST)) {
		    $header = $this->getVar('header', '');
		    $title = $this->getVar('title', '');
		    $keywords = $this->getVar('keywords', '');
		    $description = $this->getVar('description', '');
		    $body = $this->getVar('body', '');
		    $referer = $this->getVar('HTTP_REFERER', '');
		    
		    $data = array(
                'header' => $header,
                'title' => $title,
                'keywords' => $keywords,
                'description' => $description,
                'body' => $body
		    );
		    
		    $this->db->update('meta_tags', $data, "id = $id");
		    
		    $referer = (!empty($referer) && $referer != '{REFERER}') ? $referer : $this->basePath;
            $content = "Элемент успешно изменен<meta http-equiv='refresh' content='2;URL=$referer'>";
            
            $this->viewMessage($content);
            
            return true;
            
		} else {
		    $referer = $this->getVar('HTTP_REFERER', $this->basePath);
		    $this->tpl->assign(
                array(
                    'ADM_BODY' => $body,
                    'ADM_HEADER' => $header,
                    'ADM_TITLE' => $title,
                    'ADM_KEYWORDS' => $keywords,
                    'ADM_DESCRIPTION' => $description,
                    'REFERER' => $referer
                )
		    );
		    
		    $this->tpl->parse('CONTENT', '.start');
			$this->tpl->parse('CONTENT', '.mce');
			$this->tpl->parse('CONTENT', '.meta');
			$this->tpl->parse('CONTENT', '.body');
			$this->tpl->parse('CONTENT', '.end');
		}
		
		return true;
	}
    
    public function import()
    {
        $i=0;
        $this->setMetaTags('Импорт пользователей');
        
        $this->tpl->define_dynamic('import', "admin/import.tpl");
        $this->tpl->define_dynamic('import_form', "import");
        $this->tpl->define_dynamic('import_error', "import");
        $this->tpl->define_dynamic('import_error_row', "import_error");
        
        $this->tpl->parse('IMPORT_FORM', '.import_form');
        
        $error = '';
        $import_error = array();
        
        if (!empty($_POST) && isset($_POST['import'])) {
            $file = $this->getVar('import', null, $_FILES);
            
            if (null === $file) {
                $error = 'Выберите файл для загрузки';
            }
            
        	if (!$error && $file['error'] == '4') {
                $error = 'Выберите файл для загрузки';
            }
            
            if (!$error && $file['error'] != '0') {
                $error = 'Ошибка загрузки файла на сервер';
            }
            
            if (!$error) {
            	set_include_path(PATH . 'excel/');
                include_once PATH . 'excel/PHPExcel/IOFactory.php';
                //echo PATH . 'excel/PHPExcel/IOFactory.php<BR>'; die;
                $exel = PHPExcel_IOFactory::load($file['tmp_name']);
                
                $aSheet = $exel->getActiveSheet();
            
                $array = $aSheet->toArray();
                
                if (!empty($array)) {
                    array_shift($array);
                    
                    if (sizeof($array) < 1) {
                        $error = 'Файл не содержит записей';
                    }
                } else {
                    $error = 'Файл не содержит записей';
                }
                
                if (!$error) {
                    $this->logger->addLogRow('Импорт пользователей');
                    
                    $import_error = array();
                    $i = 1;
                    
                    foreach ($array as $row) {
                        $email = $this->getVar(0, null, $row);
                        $name = $this->getVar(1, null, $row);
                        $surname = $this->getVar(2, null, $row);
                        $number = $this->getVar(3, null, $row);
                        $phone = $this->getVar(4, null, $row);
                        $diamond = $this->getVar(5, null, $row);
                        $emerald = $this->getVar(6, null, $row);
                        $platinum = $this->getVar(7, null, $row);
                        $date = $this->getVar(8, null, $row);
                        $status = $this->getVar(9, null, $row);
                        $type = $this->getVar(10, null, $row);
                        //$top_user_no_platina = $this->getVar(11, null, $row);
                        //$top_user_no_emberland = $this->getVar(12, null, $row);
                        $top_user_no = $this->getVar(14, null, $row);
                        
                    	$userType = 'user';
                        switch ($type) {
                            case 1 : $userType = 'user'; break;
                            case 2 : $userType = 'platinum'; break;
                            case 3 : $userType = 'emerald'; break;
                            case 4 : $userType = 'diamond'; break;
                            default : $userType = 'user'; break;
                        }
                        
                        if (null === $email || null === $number) {
                            $import_error[] = array(
                                'line' => $i,
                                'error' => 'Не указаны E-Mail и/или Номер Amway пользователя',
                                'email' => '',
                                'number' => ''
                            );
                            
                            $i++;
                            
                            continue;
                        }
                        
                        $user = $this->db->fetchAll("SELECT * FROM `users` WHERE `email` = '$email' OR `number` = '$number'");
                        
                        if (!$user) {
                            $password = $this->generatePassword();
                            //echo $date.'<br />';
                            $date = (null === $date ? mktime() : explode('-', $date));
                            if (is_array($date)) {
                            	$date = mktime(date('H'), date('i'), date('s'), (isset($date[0])?$date[0]:0), (isset($date[1])?$date[1]:0), '20'.(isset($date[2])?$date[2]:0));
                            }
                            
                            $data = array(
                                'name'      => mb_convert_encoding($name, 'windows-1251', 'utf-8'),
                                'surname'   => mb_convert_encoding($surname, 'windows-1251', 'utf-8'),
                                'number'    => $number,
                                'email'     => $email,
                                'phone'     => $this->checkPhone($phone),
                                'diamond'   => mb_convert_encoding($diamond, 'windows-1251', 'utf-8'),
                                'emerald'   => mb_convert_encoding($emerald, 'windows-1251', 'utf-8'),
                                'platinum'  => mb_convert_encoding($platinum, 'windows-1251', 'utf-8'),
                                'date'      => $date,
                                'password'  => crypt($password, $this->_salt),
                            	'status'	=> "$status",
                            	'type'		=> $userType,
                                //'top_user_no_platina'	=> mb_convert_encoding($top_user_no_platina, 'windows-1251', 'utf-8'),
                                //'top_user_no_emberland'	=> mb_convert_encoding($top_user_no_emberland, 'windows-1251', 'utf-8'),
                                'top_user_no'	=> mb_convert_encoding($top_user_no, 'windows-1251', 'utf-8')
                            );
                            
                            $this->logger->addLogRow('Импорт пользователей: добавление', serialize($data), 'sub', false);
                            
                            $this->db->insert('users', $data);
                        } else {
                            if (sizeof($user) > 1) {
                                $import_error[] = array(
                                    'line' => $i,
                                    'error' => 'Найдено больше одной записи в базе данных пользователей',
                                    'email' => $email,
                                    'number' => $number
                                );
                            } elseif (sizeof($user) == 1) {
                                $user = $user[0];
                                
                                if ($user['email'] != $email || $user['number'] != $number) {
                                    $import_error[] = array(
                                        'line' => $i,
                                        'error' => 'Не совпадают E-Mail и Номер Amway у пользователя',
                                        'email' => $email,
                                        'number' => $number
                                    );
                                } else {
                                	if ($status == '3') {
                                	    $this->logger->addLogRow('Импорт пользователей: удаление', serialize($user), 'sub', false);
                                		$this->db->delete('users', "`id` = '".$user['id']."'");
                                	} else {
                                            
	                                    $data = array(
	                                        'name' => mb_convert_encoding($name, 'windows-1251', 'utf-8'),
	                                        'surname' => mb_convert_encoding($surname, 'windows-1251', 'utf-8'),
	                                        'phone' => $this->checkPhone($phone),
	                                        'diamond' => mb_convert_encoding($diamond, 'windows-1251', 'utf-8'),
	                                        'emerald' => mb_convert_encoding($emerald, 'windows-1251', 'utf-8'),
	                                        'platinum' => mb_convert_encoding($platinum, 'windows-1251', 'utf-8'),
	                                        'type' => $userType,
	                                        'status' => "$status",
                                                //'top_user_no_platina'	=> mb_convert_encoding($top_user_no_platina, 'windows-1251', 'utf-8'),
                                                //'top_user_no_emberland'	=> mb_convert_encoding($top_user_no_emberland, 'windows-1251', 'utf-8'),
                                                'top_user_no' => mb_convert_encoding($top_user_no, 'windows-1251', 'utf-8'),
	                                    );
	                                    //print_r($data);
	                                    $this->logger->addLogRow('Импорт пользователей: обновление', serialize($user), 'sub', false);
	                                    $this->db->update('users', $data, "`email` = '$email' AND `number` = '$number'");
                                	}
                                }
                            }
                        }
                    }
                }
            }
        }
        
        if (!empty($_POST) && isset($_POST['import'])) {
            if ($error) {
                $this->tpl->assign('IMPORT_FALSE', '<p>'.$error.'</p>');
            } else {
                $this->tpl->assign('IMPORT_FALSE', '');
                
            }
            
            if ($import_error) {
                $error_str = '';
                
                foreach ($import_error as $row) {
                    $this->tpl->assign(
                        array(
                            'FILE_LINE' => $row['line'],
                            'IMPORT_MESSAGE' => $row['error'],
                            'IMPORT_EMAIL' => (!empty($row['email']) ? $row['email'] : ''),
                            'IMPORT_NUMBER' => (!empty($row['number']) ? $row['number'] : '')
                        )
                    );
                    
                    $this->tpl->parse('IMPORT_ERROR_ROW', '.import_error_row');
                    $error_str .= $row['error'].'\tСтрока №'.$row['line'].(!empty($row['email']) ? '\t'.$row['email'] : '').(!empty($row['number']) ? '\t'.$row['number'] : '').'\r\n';
                }
                
                $fp = fopen('./import/'.mktime().'.log', 'w');
                fwrite($fp, $error_str);
                fclose($fp);
                
                $this->tpl->parse('IMPORT_ERROR', 'import_error');
            } else {
            	$this->tpl->parse('IMPORT_ERROR', 'null');
            }
            
            if (!$error && !$import_error) {
            	$this->tpl->assign('IMPORT_FALSE', '<p>Импорт прошел успешно</p>');
            }
        } else {
        	$this->tpl->assign('IMPORT_FALSE', '');
        	$this->tpl->parse('IMPORT_ERROR', 'null');
        }
        
                /**/
                            $data = array(
                                        'up' => '',
                                        'upup' => '',
                                        'upupup' => ''
                                    );
        
                            $this->db->update ('users', $data);
                            $user_add_no = $this->db->fetchAll("SELECT * FROM `users`");// WHERE ((`upup` = '' OR `up` = '' OR `upup` = '0' OR `up` = '0' OR `upupup`='' OR upupup='0') AND `top_user_no`<>'')");
                            foreach ($user_add_no as $user_add){
                                //echo $user_add['type'].'<br>';
$i++;                                
//Если брилиант или изумруд, то в изумруд и платину пишем самого себя
if ($user_add['type']=='diamond'){
    $data = array(
        'up' => (isset($user_add['number'])?$user_add['number']:0),
        'upup' => (isset($user_add['number'])?$user_add['number']:0),
        'upupup' => (isset($user_add['number'])?$user_add['number']:0)
    );
    $this->db->update ('users', $data, "`id` = '".$user_add['id']."'");
}
if ($user_add['type']=='emerald'){
    $data = array(
        'up' => (isset($user_add['number'])?$user_add['number']:0),
        'upup' => (isset($user_add['number'])?$user_add['number']:0),
        'upupup' => (isset($user_add['top_user_no'])?$user_add['top_user_no']:0),
    );
    $this->db->update ('users', $data, "`id` = '".$user_add['id']."'");
}
if ($user_add['type']=='platinum'){
    $up = $user_add['number'];
    $user_upup_no = $this->db->fetchRow("
        SELECT * 
        FROM `users` 
        WHERE (`number` = '".$user_add['top_user_no']."')
        ");
//    var_dump ($user_upup_no); echo '<br><br><br>';//['type'].'<br>';
    if ($user_upup_no){
        if ($user_upup_no['type']=='diamond'){
            $upup = $user_add['top_user_no'];
            $upupup = $user_add['top_user_no'];
        }
        elseif($user_upup_no['type']=='emerald'){
            $upup = $user_add['top_user_no'];
            $user_upupup_no = $this->db->fetchRow("
                SELECT * 
                FROM `users` 
                WHERE (`number` = '".$user_add['top_user_no']."')
            ");
            $upupup = $user_upupup_no['top_user_no'];
            //$upupup = $user_upup_no['top_user_no'];
        }
        else{
            $upup = 0;
            $upupup = 0;
        }
    }
    else{
        $up=0;
        $upup=0;
    }
    $data = array(
        'up' => $up,
        'upup' => $upup,
        'upupup' => $upupup
    );
    $this->db->update ('users', $data, "`id` = '".$user_add['id']."'");
}
                               
}
        $this->tpl->parse('CONTENT', 'import');
        return true;
    }
	
    public function export()
    {
    	$this->setMetaTags('Экспорт пользователей');
        
        $this->tpl->define_dynamic('export', "admin/export.tpl");
        
        $error = '';
        
        $startDate	= mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endDate	= mktime(23, 59, 59, date('m'), date('d'), date('Y'));
        $banned		= 1;
        $confirmed	= 1;
        $new		= 1;
        //$deleted	= 1; // А надо ли ??
        
        if (!empty($_POST) && isset($_POST['export'])) {
            $this->logger->addLogRow('Экспорт пользователей');
            
        	$startDate = $this->getVar('date-start', null, $_POST);
        	$endDate = $this->getVar('date-end', null, $_POST);
        	
        	if (null === $startDate || null === $endDate) {
        		return false;
        	}
        	
        	$startDate = explode('.', $startDate);
        	$endDate = explode('.', $endDate);
        	
        	$startDate = mktime(0, 0, 0, $startDate[1], $startDate[0], $startDate[2]);
        	$endDate = mktime(23, 59, 59, $endDate[1], $endDate[0], $endDate[2]);
        	
        	if ($startDate > $endDate || $startDate > mktime()) {
        		$error .= '<p class="error">Период задан неверно</p>';
        	}
        	
        	$banned		= $this->getVar('banned', '0', $_POST);
        	$confirmed	= $this->getVar('confirmed', '0', $_POST);
        	$new		= $this->getVar('new', '0', $_POST);
        	//$deleted	= $this->getVar('deleted', 0, $_POST);
        	
        	if ($banned == '0' && $confirmed == '0' && $new == '0') {
        		$error .= '<p class="error">Не выбран ни один статус пользователя</p>';
        	}
        }
        
        if (!empty($_POST) && isset($_POST['export']) && !$error) {
        	$select = $this->db->select();
        	$select->from('users', array('*'));
        	$select->where('privilege <> "administrator"');
        	$select->where('date > ?', "$startDate");
        	$select->where('date < ?', "$endDate");
        	
        	$where = '';
        	if ($banned) {
        		$where .= '`status` = "0"';
        	}
        	
        	if ($confirmed) {
        		$where .= $where ? ' OR `status` = "1"' : '`status` = "1"';
        	}
        	
        	if ($new) {
        		$where .= $where ? ' OR `status` = "2"' : '`status` = "2"';
        	}
        	
        	$select->where($where);
        	
        	/*if ($deleted) {
        		if ($or) {
        			$select->orWhere('status = "3"');
        		} else {
        			$select->where('status = "3"');
        		}
        	}*/
        	
        	$select->order('date DESC');
        	//echo $select->__toString();
        	$users = $this->db->fetchAll($select);
        	
        	if ($users) {
        		set_include_path(PATH . 'excel/');
                require_once PATH . 'excel/PHPExcel.php';
                
                $excel = new PHPExcel();
                
                $excel->getActiveSheet()->setCellValue('A1', mb_convert_encoding('E-Mail','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('B1', mb_convert_encoding('Имя','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('C1', mb_convert_encoding('Фамилия','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('D1', mb_convert_encoding('Номер Amway','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('E1', mb_convert_encoding('Телефон','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('F1', mb_convert_encoding('Бриллиант','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('G1', mb_convert_encoding('Изумруд','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('H1', mb_convert_encoding('Платина','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('I1', mb_convert_encoding('Дата','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('J1', mb_convert_encoding('Статус','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('K1', mb_convert_encoding('Уровень','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('L1', mb_convert_encoding('№ НПА (не менять)','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('M1', mb_convert_encoding('№ НПА (не менять)','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('N1', mb_convert_encoding('№ НПА (не менять)','utf-8','windows-1251'));
                $excel->getActiveSheet()->setCellValue('O1', mb_convert_encoding('№ НПА','utf-8','windows-1251'));
                
                $i = 2;
                
                foreach ($users as $row) {
                	switch($row['type']) {
                		case 'diamond' : $type = '4'; break;
                		case 'emerald' : $type = '3'; break;
                		case 'platinum' : $type = '2'; break;
                		default : $type = '1'; break;
                	}
                	
                	$excel->getActiveSheet()->setCellValue('A'.$i, $row['email']);
	                $excel->getActiveSheet()->setCellValue('B'.$i, mb_convert_encoding($row['name'],'utf-8','windows-1251'));
	                $excel->getActiveSheet()->setCellValue('C'.$i, mb_convert_encoding($row['surname'],'utf-8','windows-1251'));
	                $excel->getActiveSheet()->setCellValue('D'.$i, $row['number']);
	                $excel->getActiveSheet()->setCellValue('E'.$i, $this->checkPhone($row['phone']));
	                $excel->getActiveSheet()->setCellValue('F'.$i, mb_convert_encoding($row['diamond'],'utf-8','windows-1251'));
	                $excel->getActiveSheet()->setCellValue('G'.$i, mb_convert_encoding($row['emerald'],'utf-8','windows-1251'));
	                $excel->getActiveSheet()->setCellValue('H'.$i, mb_convert_encoding($row['platinum'],'utf-8','windows-1251'));
	                $excel->getActiveSheet()->setCellValue('I'.$i, $this->convertDate($row['date']));
	                $excel->getActiveSheet()->setCellValue('J'.$i, $row['status']);
	                $excel->getActiveSheet()->setCellValue('K'.$i, $type);
                        $excel->getActiveSheet()->setCellValue('L'.$i, $row['up']);
                        $excel->getActiveSheet()->setCellValue('M'.$i, $row['upup']);
                        $excel->getActiveSheet()->setCellValue('N'.$i, $row['upupup']);
                        $excel->getActiveSheet()->setCellValue('O'.$i, $row['top_user_no']);
	                
	                $i++;
                }
                
                header('Content-Type: application/vnd.ms-excel');
			    header('Content-Disposition: attachment;filename="./export.xls"');
			    header('Cache-Control: max-age=0');
			    
			    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
			    $objWriter->save('php://output');
			    @unlink('./export.xls');
			    exit;
        	} else {
        		$error .= '<p class="error">Не найден ни один пользователь по заданным критериям.</p>';
        	}
        }
        
        if ($error) {
        	$this->tpl->assign('EXPORT_FAIL', $error);
        } else {
        	$this->tpl->assign('EXPORT_FAIL', '');
        }
        
        $this->tpl->assign(
        	array(
        		'DATE_START' => $this->convertDate($startDate),
        		'DATE_END' => $this->convertDate($endDate),
        		'CHECK_BANNED' => $banned ? ' checked' : '',
        		'CHECK_CONFIRMED' => $confirmed ? ' checked' : '',
        		'CHECK_NEW' => $new ? ' checked' : ''
        	)
        );
        
        $this->tpl->parse('CONTENT', 'export');
        
        return true;
    }
        
    public function exportscanner()
    {
        $this->setMetaTags('Экспорт билетов для сканера');
        
        $this->logger->addLogRow('Экспорт билетов для сканера');
        
        $event = $this->getVar('event', 0, $_POST);
        $sector = $this->getVar('sector', 0, $_POST);
        
        if (!ctype_digit($event) || $event < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        if (!ctype_digit($sector) || $sector < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $select = $this->db->select();
        $select->from('tickets', array('id', 'count', 'payment', 'ticket_date' => 'date', 'lastNumber'));
        $select->join('events', 'events.id = tickets.event_id', array('event_id' => 'id', 'event_name' => 'name', 'event_date' => 'date', 'pic'));
        $select->join('event_types', 'event_types.id = type_id', array('event_type' => 'name'));
        $select->join('hall', 'hall.id = events.hall_id', array('adres'));
        $select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
        $select->join('sectors', 'sectors.id = tickets.sector_id', array('sector_id' => 'id', 'sector_name' => 'name'));
        $select->join('users', 'users.id = tickets.user_id', array('user_id' => 'id', 'user_name' => 'name', 'surname', 'number', 'amway' => 'number'));
        $select->where('tickets.event_id = ?', array("$event"));
        $select->where('tickets.sector_id = ?', array("$sector"));
        $select->order('tickets.date');
        
        $ticket = $this->db->fetchAll($select);
        //print_r($ticket);
        $locale = new Zend_Locale('ru_UA');
        Zend_Date::setOptions(array('format_type' => 'php'));
        
        //$item = '';
        
        if (!$ticket) {
            $this->tpl->assign('CONTENT', '<div class="rzd_list">Заказанных билетов для данного сектора не найдено.</div>');
            return true;
        }
        
        //$i = 1;
        $d = new Zend_Date($ticket[0]['event_date'], false, $locale);
        $tcNumber = $this->addZero($ticket[0]['event_id']);
	    $eventDate = $d->toString('d.m.Y');
	    $sector = 'Sector-'.$ticket[0]['sector_id'];
        
        $item = "ТС-$tcNumber
";
	    $item .= "$eventDate
";
	    $item .= "$sector
";
        
        foreach ($ticket as $row) {
            //$d = new Zend_Date($row['ticket_date'], false, $locale);
            $scannerCode = $this->db->fetchAll("SELECT * FROM `tickets_code` WHERE `ticket_id` = '".$row['id']."'");
            if (sizeof($scannerCode) > 1) {
                foreach ($scannerCode as $sc) {
                //for ($i=0; $i<$row['count'];$i++) {
                    //$lastNumber = (int) ($row['lastNumber'] + ($i+1));
                    //$code = (int) $d->toString('dmy').$lastNumber;
                    
                    $barcode = new Zend_Barcode_Object_Ean13();
                    $barcode->setText($sc['code']);
                    $scannerCode = substr($barcode->getTextToDisplay(), 1);
                    $number = $row['number'];
                    $amway = $row['amway'];
                    $fullName = $row['user_name'].' '.$row['surname'];
                    
                    $item .= "$scannerCode;$number;$amway;$fullName;чел.;1;0
";
                }
            } elseif (sizeof($scannerCode) == 1) {
                //$code = $d->toString('dmy').($row['lastNumber']+1);
                
                $barcode = new Zend_Barcode_Object_Ean13();
                $barcode->setText($scannerCode[0]['code']);
                $scannerCode = substr($barcode->getTextToDisplay(), 1);
                $number = $row['number'];
                $amway = $row['amway'];
                $fullName = $row['user_name'].' '.$row['surname'];
                
                $item .= "$scannerCode;$number;$amway;$fullName;чел.;1;0
";
            }
            
            //$i++;
        }
        
        $destinationFolder = './ticketexport/'.$row['event_id']."_".$row['sector_id'];
        if (!is_dir($destinationFolder)) {
            mkdir($destinationFolder);
            @chmod($destinationFolder, 0777);
        }
        
        $filename = $destinationFolder."/inventory.csv";
        
        $fp = fopen($filename, 'w+');
	    
	    //fputs($fp, $file);
	    fputs($fp, $item);
	    
	    fclose($fp);
	    @chmod($filename, 0777);
        
	    //$int = 1;
	    //echo (string) $int;
	    
	    
	    $filename = $destinationFolder."/goods.csv";
	    
	    
	    $fp = fopen($filename, 'w+');
	    
	    //fputs($fp, $file);
	    //fputs($fp, $item);
	    
	    fclose($fp);
	    @chmod($filename, 0777);
	    
	    $this->tpl->define_dynamic('_list', 'admin/exportscanner.tpl');
        $this->tpl->define_dynamic('list', '_list');
        $this->tpl->define_dynamic('list_row', 'list');
        
        $this->tpl->assign(
            array(
                'FILE_URL' => substr($destinationFolder, 1).'/goods.csv',
                'FILE_NAME' => 'goods.csv'
            )
        );
        
        $this->tpl->parse('LIST_ROW', '.list_row');
        
        $this->tpl->assign(
            array(
                'FILE_URL' => substr($destinationFolder, 1).'/inventory.csv',
                'FILE_NAME' => 'inventory.csv'
            )
        );
        
        $this->tpl->parse('LIST_ROW', '.list_row');
        
        $this->tpl->parse('CONTENT', '.list');
	    
        //ini_set('max_execution_time', '360');
        //header("Content-type: application/vnd.ms-excel");
        //header("Content-disposition: attachment; filename=\"http://upline24.com.ua/ticketexport/".$row['event_id']."_".$row['sector_id'].".csv\"");
        //echo $pdfString;
        //exit();
        /*$d = new Zend_Date($ticket[0]['event_date'], false, $locale);
        $code = (int) $d->toString('dmy').$ticket[0]['lastNumber'];
        
        $barcode = new Zend_Barcode_Object_Ean13();
        $barcode->setText($code);
        $scannerCode = $barcode->getTextToDisplay();*/
        //var_dump($scannerCode);
        //var_dump($barcode->getTextToDisplay());
        
        return true;
    }
    
    public function eventimport()
    {
        $this->setMetaTags('Импорт оплаченных заказов');
        
        //echo $_SERVER['REMOTE_ADDR'];
        //if ($_SERVER['REMOTE_ADDR'] != '94.179.62.151') {
            //$this->tpl->assign('CONTENT', 'В разработке');
            //return true;
        //}
        
        $this->tpl->define_dynamic('import', "admin/eventimport.tpl");
        $this->tpl->define_dynamic('import_form', "import");
        $this->tpl->define_dynamic('import_error', "import");
        $this->tpl->define_dynamic('import_error_row', "import_error");
        
        $this->tpl->parse('IMPORT_FORM', '.import_form');
        
        $error = '';
        $import_error = array();
        
        if (!empty($_POST) && isset($_POST['import'])) {
            $file = $this->getVar('import', null, $_FILES);
            
            if (null === $file) {
                $error = 'Выберите файл для загрузки';
            }
            
        	if (!$error && $file['error'] == '4') {
                $error = 'Выберите файл для загрузки';
            }
            
            if (!$error && $file['error'] != '0') {
                $error = 'Ошибка загрузки файла на сервер';
            }
            
            if (!$error) {
            	set_include_path(PATH . 'excel/');
                include_once PATH . 'excel/PHPExcel/IOFactory.php';
                $exel = PHPExcel_IOFactory::load($file['tmp_name']);
                
                $aSheet = $exel->getActiveSheet();
            
                $array = $aSheet->toArray();
                //print_r($array);
                
                if (empty($array)) {
                    $error = 'Файл не содержит записей';
                }
                
                if (!$error) {
                    $import_error = array();
                    $i = 1;
                    
                    $this->logger->addLogRow('Импорт оплаченных заказов', serialize($array));
                    
                    foreach ($array as $row) {
                        $number = $this->getVar(0, null, $row);
                        
                        //$number = $this->checkNumber($number);
                        
                        if (null === $number) {
                            $import_error[] = array(
                                'line' => $i,
                                'error' => 'Номер заказа не может быть пустым',
                                'number' => ''
                            );
                            
                            //$i++;
                            
                            continue;
                        }
                        
                        $ticket = $this->db->fetchAll("SELECT * FROM `tickets` WHERE `code` = '$number'");
                        
                        if (!$ticket) {
                            $import_error[] = array(
                                'line' => $i,
                                'error' => 'Заказ не найден',
                                'number' => $number
                            );
                            
                            //$i++;
                            
                            continue;
                        } else {
                            if (sizeof($ticket) > 1) {
                                $import_error[] = array(
                                    'line' => $i,
                                    'error' => 'Найдено больше одной записи в базе данных заказов',
                                    'number' => $number
                                );
                                
                                //$i++;
                            
                                continue;
                            } elseif (sizeof($ticket) == 1) {
                                $ticket = $ticket[0];
                                
                                //if ($ticket['payment']==0) {
                                if ($ticket['payment']==0) {
                                    $data = array(
                                        'payment' => '1'
                                    );
                                    
                                    $this->logger->addLogRow('Импорт оплаченных заказов: обновление', serialize($number), 'sub', false);
                                    
                                    //объеденяем в один заказ
                                    $uNumber = $this->db->fetchOne ("SELECT `number` FROM `tickets` WHERE `code`='$number'");
                                    $uId = $this->db->fetchOne ("SELECT `id` FROM `tickets` WHERE `code`='$number'");
                                    $uIds = $this->db->fetchOne ("SELECT `ids` FROM `tickets` WHERE `code`='$number'");
                                    $uId = ($uIds==0?$uId:$uIds);
                                    //$this->db->update('tickets', $data, "code = '$number'");
                                    $this->db->update('tickets', $data, "number = '$uNumber' OR ids='$uId'");
                                    $this->sendMailTopUserTickets($uNumber,$uId);
                                    $import_error[] = array(
                                        'line' => $i,
                                        'error' => 'Оплата подтверждена',
                                        'number' => $number
                                    );
                                } else {
                                    $import_error[] = array(
                                        'line' => $i,
                                        'error' => 'Заказ уже оплачен',
                                        'number' => $number
                                    );
                                    
                                    //$i++;
                                }
                            }
                        }
                        
                        $i++;
                    }
                }
            }
        }
        
        if (!empty($_POST) && isset($_POST['import'])) {
            if ($error) {
                $this->tpl->assign('IMPORT_FALSE', '<p>'.$error.'</p>');
            } else {
                $this->tpl->assign('IMPORT_FALSE', '');
                
            }
            
            if ($import_error) {
                foreach ($import_error as $row) {
                    $this->tpl->assign(
                        array(
                            'FILE_LINE' => $row['line'],
                            'IMPORT_MESSAGE' => $row['error'],
                            //'IMPORT_EMAIL' => (!empty($row['email']) ? $row['email'] : ''),
                            'IMPORT_NUMBER' => (!empty($row['number']) ? $row['number'] : '')
                        )
                    );
                    
                    $this->tpl->parse('IMPORT_ERROR_ROW', '.import_error_row');
                    //$error_str .= $row['error'].'\tСтрока №'.$row['line'].(!empty($row['email']) ? '\t'.$row['email'] : '').(!empty($row['number']) ? '\t'.$row['number'] : '').'\r\n';
                }
                
                //$fp = fopen('./import/'.mktime().'.log', 'w');
                //fwrite($fp, $error_str);
                //fclose($fp);
                
                $this->tpl->parse('IMPORT_ERROR', 'import_error');
            } else {
            	$this->tpl->parse('IMPORT_ERROR', 'null');
            }
            
            if (!$error && !$import_error) {
            	$this->tpl->assign('IMPORT_FALSE', '<p>Импорт прошел успешно</p>');
            }
        } else {
        	$this->tpl->assign('IMPORT_FALSE', '');
        	$this->tpl->parse('IMPORT_ERROR', 'null');
        }
        
        $this->tpl->parse('CONTENT', 'import');
        
        return true;
    }

    public function discimport()
    {
        
        $this->setMetaTags('Импорт оплаченных дисков');
        
        //echo $_SERVER['REMOTE_ADDR'];
        //if ($_SERVER['REMOTE_ADDR'] != '94.179.62.151') {
            //$this->tpl->assign('CONTENT', 'В разработке');
            //return true;
        //}
        
        $this->tpl->define_dynamic('import', "admin/discimport.tpl");
        $this->tpl->define_dynamic('import_form', "import");
        $this->tpl->define_dynamic('import_error', "import");
        $this->tpl->define_dynamic('import_error_row', "import_error");
        
        $this->tpl->parse('IMPORT_FORM', '.import_form');
        
        $error = '';
        $import_error = array();
        
        if (!empty($_POST) && isset($_POST['import'])) {
            $file = $this->getVar('import', null, $_FILES);
            
            if (null === $file) {
                $error = 'Выберите файл для загрузки';
            }
            
        	if (!$error && $file['error'] == '4') {
                $error = 'Выберите файл для загрузки';
            }
            
            if (!$error && $file['error'] != '0') {
                $error = 'Ошибка загрузки файла на сервер';
            }
            
            if (!$error) {
                 
            	set_include_path(PATH . 'excel/');
                include_once PATH . 'excel/PHPExcel/IOFactory.php';
                
                $exel = PHPExcel_IOFactory::load($file['tmp_name']);
               //echo PATH; die;
                $aSheet = $exel->getActiveSheet();
            
                $array = $aSheet->toArray();
                //print_r($array);
                
                if (empty($array)) {
                    $error = 'Файл не содержит записей';
                }
                
                if (!$error) {
                    $import_error = array();
                    $i = 1;
                    
                    $this->logger->addLogRow('Импорт оплаченных дисков', serialize($array));
                    $c=0;
                    foreach ($array as $row) {
                        $c++;
                        $number = (int)($row[0]);
                        $status = (int)($row[1]);
                        
                        if ($c>=1){
                        //$number = $this->checkNumber($number);
                        
                        if (null === $number) {
                            $import_error[] = array(
                                'line' => $i,
                                'error' => 'Номер заказа не может быть пустым',
                                'number' => ''
                            );
                            
                            //$i++;
                            
                            continue;
                        }
                        
                        
                        $ticket = $this->db->fetchAll("SELECT * FROM `order_disc` WHERE `id` = '$number'");
                        
                        if (!$ticket) {
                            $import_error[] = array(
                                'line' => $i,
                                'error' => 'Заказ не найден',
                                'number' => $number
                            );
                            
                            //$i++;
                            
                            continue;
                        } else {
                            if (sizeof($ticket) > 1) {
                                $import_error[] = array(
                                    'line' => $i,
                                    'error' => 'Найдено больше одной записи в базе данных заказов',
                                    'number' => $number
                                );
                                
                                //$i++;
                            
                                continue;
                            } elseif (sizeof($ticket) == 1) {
                                $ticket = $ticket[0];
                                
                                if ($status!=5){
                                //if ($ticket['payment'] == '0') {
                                    $data = array(
                                        'status' => ($status==''?1:$status)//(($status=='' or $status==0) ? '1':($status==1?'0':$status))
                                        //'status' => (($status>0 and $status<3)?$status:'0')
                                    );
                                    //echo $status.'<br>';
                                    $this->logger->addLogRow('Импорт оплаченных дисков: обновление', serialize($number), 'sub', false);
                                    
                                    $this->db->update('order_disc', $data, "id = '$number'");
                                    if ($status==2){
                                        //echo $number;
                                        $this->sendMailTopUserDisc($number);
                                    }
                                    $import_error[] = array(
                                        'line' => $i,
                                        //'error' => ($status==1?'Оплата подтверждена':($status==2?'Доставлен':'Ожидает оплаты')),
                                        //'error' => (($status=='' or $status==0)?'Оплата подтверждена':($status==2?'Доставлен':'Ожидает оплаты')),
                                        'error' => (($status=='' or $status==1)?'Оплата подтверждена':($status==2?'Оплачен':($status==3?'На доставке':($status==4?'Доставлен':'Удален')))),
                                        'number' => $number
                                    );
                                    //Если пользователь оплатил заказ, то 
                                    //отправляем вышестоящим пользователям уведомление
                                    
                                        
                                }
                                elseif ($status==5){
                                    
                                    $this->db->delete('order_disc', "id = '$number'");    
                                    $this->db->delete('order_disc_items', "order_id = '$number'");  
                                    $import_error[] = array(
                                        'line' => $i,
                                        'error' => 'Удален',
                                        'number' => $number
                                    );
                                }

                            }
                        }
                        
                        $i++;
                    }
                }
                }
            }
        }
        
        if (!empty($_POST) && isset($_POST['import'])) {
            if ($error) {
                $this->tpl->assign('IMPORT_FALSE', '<p>'.$error.'</p>');
            } else {
                $this->tpl->assign('IMPORT_FALSE', '');
                
            }
            
            if ($import_error) {
                foreach ($import_error as $row) {
                    $this->tpl->assign(
                        array(
                            'FILE_LINE' => $row['line'],
                            'IMPORT_MESSAGE' => $row['error'],
                            //'IMPORT_EMAIL' => (!empty($row['email']) ? $row['email'] : ''),
                            'IMPORT_NUMBER' => (!empty($row['number']) ? $row['number'] : '')
                        )
                    );
                    
                    $this->tpl->parse('IMPORT_ERROR_ROW', '.import_error_row');
                    //$error_str .= $row['error'].'\tСтрока №'.$row['line'].(!empty($row['email']) ? '\t'.$row['email'] : '').(!empty($row['number']) ? '\t'.$row['number'] : '').'\r\n';
                }
                
                //$fp = fopen('./import/'.mktime().'.log', 'w');
                //fwrite($fp, $error_str);
                //fclose($fp);
                
                $this->tpl->parse('IMPORT_ERROR', 'import_error');
            } else {
            	$this->tpl->parse('IMPORT_ERROR', 'null');
            }
            
            if (!$error && !$import_error) {
            	$this->tpl->assign('IMPORT_FALSE', '<p>Импорт прошел успешно</p>');
            }
        } else {
        	$this->tpl->assign('IMPORT_FALSE', '');
        	$this->tpl->parse('IMPORT_ERROR', 'null');
        }
        
        $this->tpl->parse('CONTENT', 'import');
        //$this->sendMailTopUserDisc(316);
        return true;
    }

    
    
    private function checkNumber($number)
    {
        if (null === $number) {
            return null;
        }
        
        $number = (string) $number;
        
        if (strlen($number) >= 13) {
            return $number;
        }
        
        /*if (strlen($number) > 13) {
            return null;
        }*/
        
        $count = strlen($number);
        
        for ($i=0; $i<$count; $i++) {
            $number = '0'.$number;
        }
        
        return $number;
    }
    
    private function addZero($val)
    {
        $val = (string) $val;
        
        $tmp = '';
        $count = strlen($val);
        
        for ($i=0; $i<(7-$count); $i++) {
            $tmp .= '0';
        }
        
        return $tmp.$val;
    }
    
    
    public function eventexport()
    {
        $bNumber=0;
        $event = end($this->url);
        
        if (!ctype_digit($event) || $event < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $select = $this->db->select();
        $select->from('tickets', array('id', 'count', 'payment', 'number', 'ticket_date' => 'date', 'lastNumber', 'code'));
        $select->join('events', 'events.id = tickets.event_id', array('event_id' => 'id', 'event_name' => 'name', 'event_date' => 'date', 'pic'));
        $select->join('event_types', 'event_types.id = type_id', array('event_type' => 'name'));
        $select->join('hall', 'hall.id = events.hall_id', array('adres'));
        $select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
        $select->join('sectors', 'sectors.id = tickets.sector_id', array('sector_id' => 'id', 'sector_name' => 'name'));
        $select->join('users', 'users.id = tickets.user_id', array('user_id' => 'id', 'user_name' => 'name', 'surname', 'amway' => 'number', 'phone', 'diamond', 'emerald'));
        $select->where('tickets.event_id = ?', array("$event"));
        //$select->where('tickets.sector_id = ?', array("$sector"));
        
        $select->order('sectors.id ASC');
        $select->order('tickets.date DESC');
        //echo $select; die;
        $ticket = $this->db->fetchAll($select);
        //echo $select; die;
        $locale = new Zend_Locale('ru_UA');
        Zend_Date::setOptions(array('format_type' => 'php'));
        
        if (!$ticket) {
            $this->tpl->assign('CONTENT', '<div class="rzd_list">Заказанных билетов для данного сектора не найдено.</div>');
            return true;
        }
        
        //set_include_path(PATH . 'excel/');
        set_include_path(implode(PATH_SEPARATOR, array(
            PATH . 'excel/',
            PATH . 'Zend/',
            get_include_path(),
        )));
        require_once PATH . 'excel/PHPExcel.php';
        
        $excel = new PHPExcel();
        
        $excel->getActiveSheet()->setCellValue('A1', mb_convert_encoding('Сектор','utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('B1', mb_convert_encoding('Ф.И.О.','utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('C1', mb_convert_encoding('Номер Amway','utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('D1', mb_convert_encoding('Номер заказа','utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('E1', mb_convert_encoding('Дата заказа','utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('F1', mb_convert_encoding('Diamond','utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('G1', mb_convert_encoding('Emerald','utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('H1', mb_convert_encoding('Телефон','utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('I1', mb_convert_encoding('Штрих код','utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('J1', mb_convert_encoding('Оплачено','utf-8','windows-1251'));
        //$excel->getActiveSheet()->setCellValue('G1', mb_convert_encoding('Изумруд','utf-8','windows-1251'));
        //$excel->getActiveSheet()->setCellValue('H1', mb_convert_encoding('Платина','utf-8','windows-1251'));
        //$excel->getActiveSheet()->setCellValue('I1', mb_convert_encoding('Дата','utf-8','windows-1251'));
        //$excel->getActiveSheet()->setCellValue('J1', mb_convert_encoding('Статус','utf-8','windows-1251'));
        //$excel->getActiveSheet()->setCellValue('K1', mb_convert_encoding('Уровень','utf-8','windows-1251'));
        
        $z = 2;
        
        foreach ($ticket as $row) {
            //$d = new Zend_Date($row['ticket_date'], false, $locale);
            //$ticketDate = $d->toString('dmy');
            
            $scannerCode = $this->db->fetchAll("SELECT * FROM `tickets_code` WHERE `ticket_id` = '".$row['id']."'");
            
            if (sizeof($scannerCode) > 1) { 
                
                foreach ($scannerCode as $sc) {
                //for ($i=0; $i<$row['count'];$i++) {
                    //$lastNumber = (int) ($row['lastNumber'] + ($i+1));
                    //$code = (int) $d->toString('dmy').$lastNumber;
                    
                    $barcode = new Zend_Barcode_Object_Ean13();
                    $barcode->setText($sc['code']);
                    $code = substr($barcode->getTextToDisplay(), 1);
                    
                    //$number = $row['number'];
                    //$amway = $row['amway'];
                    $fullName = $row['user_name'].' '.$row['surname'];
                    if ($row['number']!=$bNumber){
                        $bNumber=$row['number'];
                        $noZak = $row['code'];
                        $excelRow = array(
                                'sector' => $row['sector_name'],
                                'fio' => $fullName,
                                'amway' => $row['amway'],
                                'number' => $row['code'],
                                'date' => $row['ticket_date'],
                                'diamond' => $row['diamond'],
                                'emerald' => $row['emerald'],
                                'phone' => $row['phone'],
                                'code' => $code,
                                'payment' => $row['payment'],
                                //'info' => $row['number']
                        );

                        $excel = $this->addExcelExportRow($excel, $excelRow, $z);

                        //$z++;
                    } else {
                        $excelRow = array(
                                'sector' => $row['sector_name'],
                                'fio' => $fullName,
                                'amway' => $row['amway'],
                                'number' => $noZak,
                                'date' => $row['ticket_date'],
                                'diamond' => $row['diamond'],
                                'emerald' => $row['emerald'],
                                'phone' => $row['phone'],
                                'code' => $code,
                                'payment' => $row['payment'],
                                //'info' => $row['number']
                        );
                        //$z++;
                    }
                    $excel = $this->addExcelExportRow($excel, $excelRow, $z);
                    $z++;
                }
            } elseif (sizeof($scannerCode) == 1) {
                //$code = $d->toString('dmy').($row['lastNumber']+1);
                
                $barcode = new Zend_Barcode_Object_Ean13();
                $barcode->setText($scannerCode[0]['code']);
                $code = substr($barcode->getTextToDisplay(), 1);
                //$number = $row['number'];
                //$amway = $row['amway'];
                $fullName = $row['user_name'].' '.$row['surname'];
                //echo $row['number'].'<br>'; die;
                if ($row['number']!=$bNumber){
                    $bNumber=$row['number'];
                    $noZak = $row['code'];
                    $excelRow = array(
                        'sector' => $row['sector_name'],
                        'fio' => $fullName,
                        'amway' => $row['amway'],
                        'number' => $row['code'],
                        'date' => $row['ticket_date'],
                        'diamond' => $row['diamond'],
                        'emerald' => $row['emerald'],
                        'phone' => $row['phone'],
                        'code' => $code,
                        'payment' => $row['payment'],
                        //'info' => $row['number']
                    );

                    //$excel = $this->addExcelExportRow($excel, $excelRow, $z);

                    //$z++;
                } else {
                    $excelRow = array(
                        'sector' => $row['sector_name'],
                        'fio' => $fullName,
                        'amway' => $row['amway'],
                        'number' => $noZak,
                        'date' => $row['ticket_date'],
                        'diamond' => $row['diamond'],
                        'emerald' => $row['emerald'],
                        'phone' => $row['phone'],
                        'code' => $code,
                        'payment' => $row['payment'],
                        //'info' => $row['number']
                    );
                }
                $excel = $this->addExcelExportRow($excel, $excelRow, $z);

                $z++;
            }
            
            //$i++;
        }
        
        header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="eventexport.xls"');
	    header('Cache-Control: max-age=0');
	    
	    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
	    $objWriter->save('php://output');
	    @unlink('eventexport.xls');
	    exit;
        
        print_r($ticket);
        
        return true;
    }
    
    private function addExcelExportRow($excel, $row, $i)
    {
        $excel->getActiveSheet()->setCellValue('A'.$i, mb_convert_encoding($row['sector'],'utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('B'.$i, mb_convert_encoding($row['fio'],'utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('C'.$i, mb_convert_encoding($row['amway'],'utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('D'.$i, $row['number']);
        $excel->getActiveSheet()->setCellValue('E'.$i, $this->convertDate($row['date']));
        $excel->getActiveSheet()->setCellValue('F'.$i, mb_convert_encoding($row['diamond'],'utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('G'.$i, mb_convert_encoding($row['emerald'],'utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('H'.$i, $this->checkPhone($row['phone']));
        $excel->getActiveSheet()->setCellValue('I'.$i, (string) mb_convert_encoding('ш '.$row['code'],'utf-8','windows-1251'));
        $excel->getActiveSheet()->setCellValue('J'.$i, mb_convert_encoding($row['payment'],'utf-8','windows-1251'));
	//$excel->getActiveSheet()->setCellValue('J'.$i, mb_convert_encoding($row['number'],'utf-8','windows-1251'));                
        return $excel;
    }
    
    
    private function uploadContentImage($from, $to, $maxwidth = 140, $maxheight = 105, $quality = 100)
    {
        ini_set('max_execution_time', '120');
        
        // защита от Null-байт уязвимости PHP
		$from = preg_replace('/\0/uis', '', $from);
		$to = preg_replace('/\0/uis', '', $to);
		
		// информация об изображении
		$imageinfo = @getimagesize($from);
		// если получить информацию не удалось - ошибка
		if (!$imageinfo) {
		    $this->addErr('>Ошибка получения информации об изображении');
		    return false;
		}
		// получаем параметры изображения
		$width = $imageinfo[0];		// ширина
		$height = $imageinfo[1];	// высота
		$format = $imageinfo[2];	// ID формата (число)
		$mime = $imageinfo['mime'];	// mime-тип

		// определяем формат и создаём изображения
		switch ($format) {
			case 2: $img = imagecreatefromjpeg($from); break;	// jpg
			case 3: $img = imagecreatefrompng($from); break;	// png
			case 1: $img = imagecreatefromgif($from); break;	// gif
			default: $this->addErr('Неверный или недопустимый формат загружаемого файла'); return false; break;
		}
		// если создать изображение не удалось - ошибка
		if (!$img) {
		    $this->addErr('Ошибка создания изображения');
		    return false;
		}

		// меняем размеры изображения
		$newwidth = $width;
		$newheight = $height;
		// требуется квадратная картинка
		if ($maxwidth == $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// пропорции картинки одинаковы
				if ($width == $height) {
					$newwidth = $maxwidth;
					$newheight = $maxheight;
				}
				// ширина больше
				elseif ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется горизонтальная картинка
		elseif ($maxwidth > $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше
				if ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
					
					if ($newheight > $maxheight) {
					    $newheight = $maxheight;
                        $newwidth = intval(((float)$newheight / (float)$height) * $width);
					}
				}
				// высота больше или равна ширине
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
			    //echo '1';
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется вертикальная картинка
		elseif ($maxwidth < $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше или равна высоте
				if ($width >= $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}

		// если изменений над картинкой производить не надо - просто копируем её
		if ($newwidth == $width && $newheight == $height && $quality == 100) {
		    if (copy($from, $to)) return true;
			else {
			    $this->addErr('Ошибка копирования файла');
			    //$this->_err .= '<br />Ошибка копирования файла!';
			    return false;
			}
		}

		// создаём новое изображение
		
		$new = imagecreatetruecolor($maxwidth, $maxheight);
		$black = imagecolorallocate($new, 0, 0, 0);
		$white = imagecolorallocate($new, 255, 255, 255);
		// копируем старое в новое с учётом новых размеров
		imagefilledrectangle($new, 0, 0, $maxwidth - 1, $maxheight - 1, $white);
		
		$center_w = round(($maxwidth-$newwidth)/2);
		$center_w = ($center_w < 0) ? 0 : $center_w;
		$center_h = round(($maxheight-$newheight)/2);
		$center_h = ($center_h < 0) ? 0 : $center_h;
		imagecopyresampled($new, $img, $center_w, $center_h, 0, 0, $newwidth, $newheight, $width, $height);
		// создаём файл с новым изображением
		switch ($format) {
			case 2: // jpg
				if ($quality < 0) $quality = 0;
				if ($quality > 100) $quality = 100;
				imagejpeg($new, $to, $quality);
			break;
			case 3: // png
				$quality = intval($quality * 9 / 100);
				if ($quality < 0) $quality = 0;
				if ($quality > 9) $quality = 9;
				imagepng($new, $to, $quality);
			break;
			case 1: // gif
				imagegif($new, $to);
			break;
		}
		
		@chmod($to, 0644);
		
		return true;
    }
    
    private function uploadHallPic($from, $to, $name)
    {
        ini_set('max_execution_time', '120');
        
        $maxwidth = 249;
        $maxheight = 142;
        $quality = 100;
        
        // защита от Null-байт уязвимости PHP
		$from = preg_replace('/\0/uis', '', $from);
		
		// информация об изображении
		$imageinfo = @getimagesize($from);
		// если получить информацию не удалось - ошибка
		if (!$imageinfo) {
		    $this->addErr('>Ошибка получения информации об изображении');
		    return false;
		}
		// получаем параметры изображения
		$width = $imageinfo[0];		// ширина
		$height = $imageinfo[1];	// высота
		$format = $imageinfo[2];	// ID формата (число)
		$mime = $imageinfo['mime'];	// mime-тип

		// определяем формат и создаём изображения
		switch ($format) {
			case 2: $img = imagecreatefromjpeg($from); break;	// jpg
			case 3: $img = imagecreatefrompng($from); break;	// png
			case 1: $img = imagecreatefromgif($from); break;	// gif
			default: $this->addErr('Неверный или недопустимый формат загружаемого файла'); return false; break;
		}
		// если создать изображение не удалось - ошибка
		if (!$img) {
		    $this->addErr('Ошибка создания изображения');
		    return false;
		}
		
		// меняем размеры изображения
		$newwidth = $width;
		$newheight = $height;
		// требуется квадратная картинка
		if ($maxwidth == $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// пропорции картинки одинаковы
				if ($width == $height) {
					$newwidth = $maxwidth;
					$newheight = $maxheight;
				}
				// ширина больше
				elseif ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется горизонтальная картинка
		elseif ($maxwidth > $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше
				if ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
					
					if ($newheight > $maxheight) {
					    $newheight = $maxheight;
                        $newwidth = intval(((float)$newheight / (float)$height) * $width);
					}
				}
				// высота больше или равна ширине
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
			    //echo '1';
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется вертикальная картинка
		elseif ($maxwidth < $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше или равна высоте
				if ($width >= $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}

		// если изменений над картинкой производить не надо - просто копируем её
		if ($newwidth == $width && $newheight == $height && $quality == 100) {
		    if (copy($from, $to.$name)) return true;
			else {
			    $this->addErr('Ошибка копирования файла');
			    //$this->_err .= '<br />Ошибка копирования файла!';
			    return false;
			}
		}

		// создаём новое изображение
		
		$new = imagecreatetruecolor($maxwidth, $maxheight);
		$black = imagecolorallocate($new, 0, 0, 0);
		$white = imagecolorallocate($new, 255, 255, 255);
		// копируем старое в новое с учётом новых размеров
		imagefilledrectangle($new, 0, 0, $maxwidth - 1, $maxheight - 1, $white);
		
		$center_w = round(($maxwidth-$newwidth)/2);
		$center_w = ($center_w < 0) ? 0 : $center_w;
		$center_h = round(($maxheight-$newheight)/2);
		$center_h = ($center_h < 0) ? 0 : $center_h;
		imagecopyresampled($new, $img, $center_w, $center_h, 0, 0, $newwidth, $newheight, $width, $height);
		// создаём файл с новым изображением
		switch ($format) {
			case 2: // jpg
				if ($quality < 0) $quality = 0;
				if ($quality > 100) $quality = 100;
				imagejpeg($new, $to.$name, $quality);
			break;
			case 3: // png
				$quality = intval($quality * 9 / 100);
				if ($quality < 0) $quality = 0;
				if ($quality > 9) $quality = 9;
				imagepng($new, $to.$name, $quality);
			break;
			case 1: // gif
				imagegif($new, $to.$name);
			break;
		}
		
		@chmod($to.$name, 0644);
		@copy($from, $to.'big/'.$name);
		@chmod($to.'big/'.$name, 0644);
		
		return true;
    }
    
    private function uploadEventPic($from, $to, $name)
    {
    	ini_set('max_execution_time', '360');
        
        $maxwidth = 118;
        $maxheight = 100;
        $quality = 100;
        
        // защита от Null-байт уязвимости PHP
		$from = preg_replace('/\0/uis', '', $from);
		
		// информация об изображении
		$imageinfo = @getimagesize($from);
		// если получить информацию не удалось - ошибка
		if (!$imageinfo) {
		    $this->addErr('>Ошибка получения информации об изображении');
		    return false;
		}
		// получаем параметры изображения
		$width = $imageinfo[0];		// ширина
		$height = $imageinfo[1];	// высота
		$format = $imageinfo[2];	// ID формата (число)
		$mime = $imageinfo['mime'];	// mime-тип

		// определяем формат и создаём изображения
		switch ($format) {
			case 2: $img = imagecreatefromjpeg($from); break;	// jpg
			case 3: $img = imagecreatefrompng($from); break;	// png
			case 1: $img = imagecreatefromgif($from); break;	// gif
			default: $this->addErr('Неверный или недопустимый формат загружаемого файла'); return false; break;
		}
		// если создать изображение не удалось - ошибка
		if (!$img) {
		    $this->addErr('Ошибка создания изображения');
		    return false;
		}
		
		// меняем размеры изображения
		$newwidth = $width;
		$newheight = $height;
		// требуется квадратная картинка
		if ($maxwidth == $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// пропорции картинки одинаковы
				if ($width == $height) {
					$newwidth = $maxwidth;
					$newheight = $maxheight;
				}
				// ширина больше
				elseif ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется горизонтальная картинка
		elseif ($maxwidth > $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше
				if ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
					
					if ($newheight > $maxheight) {
					    $newheight = $maxheight;
                        $newwidth = intval(((float)$newheight / (float)$height) * $width);
					}
				}
				// высота больше или равна ширине
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
			    //echo '1';
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется вертикальная картинка
		elseif ($maxwidth < $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше или равна высоте
				if ($width >= $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}

		// если изменений над картинкой производить не надо - просто копируем её
		if ($newwidth == $width && $newheight == $height && $quality == 100) {
		    if (copy($from, $to.$name)) return true;
			else {
			    $this->addErr('Ошибка копирования файла');
			    //$this->_err .= '<br />Ошибка копирования файла!';
			    return false;
			}
		}

		// создаём новое изображение
		
		$new = imagecreatetruecolor($maxwidth, $maxheight);
		$black = imagecolorallocate($new, 0, 0, 0);
		$white = imagecolorallocate($new, 255, 255, 255);
		// копируем старое в новое с учётом новых размеров
		imagefilledrectangle($new, 0, 0, $maxwidth - 1, $maxheight - 1, $white);
		
		$center_w = round(($maxwidth-$newwidth)/2);
		$center_w = ($center_w < 0) ? 0 : $center_w;
		$center_h = round(($maxheight-$newheight)/2);
		$center_h = ($center_h < 0) ? 0 : $center_h;
		imagecopyresampled($new, $img, $center_w, $center_h, 0, 0, $newwidth, $newheight, $width, $height);
		// создаём файл с новым изображением
		switch ($format) {
			case 2: // jpg
				if ($quality < 0) $quality = 0;
				if ($quality > 100) $quality = 100;
				imagejpeg($new, $to.$name, $quality);
			break;
			case 3: // png
				$quality = intval($quality * 9 / 100);
				if ($quality < 0) $quality = 0;
				if ($quality > 9) $quality = 9;
				imagepng($new, $to.$name, $quality);
			break;
			case 1: // gif
				imagegif($new, $to.$name);
			break;
		}
		
		@chmod($to.$name, 0644);
		
		
		
        // Большая картинка, используется в билетах PDF
        $maxwidth = 391;
        $maxheight = 454;
        $quality = 100;
        
        // защита от Null-байт уязвимости PHP
		//$from = preg_replace('/\0/uis', '', $from);
		
		// информация об изображении
		//$imageinfo = @getimagesize($from);
		// если получить информацию не удалось - ошибка
		/*if (!$imageinfo) {
		    $this->addErr('>Ошибка получения информации об изображении');
		    return false;
		}*/
		// получаем параметры изображения
		$width = $imageinfo[0];		// ширина
		$height = $imageinfo[1];	// высота
		$format = $imageinfo[2];	// ID формата (число)
		$mime = $imageinfo['mime'];	// mime-тип

		// определяем формат и создаём изображения
		switch ($format) {
			case 2: $img = imagecreatefromjpeg($from); break;	// jpg
			case 3: $img = imagecreatefrompng($from); break;	// png
			case 1: $img = imagecreatefromgif($from); break;	// gif
			default: $this->addErr('Неверный или недопустимый формат загружаемого файла'); return false; break;
		}
		// если создать изображение не удалось - ошибка
		if (!$img) {
		    $this->addErr('Ошибка создания изображения');
		    return false;
		}
		
		// меняем размеры изображения
		$newwidth = $width;
		$newheight = $height;
		// требуется квадратная картинка
		if ($maxwidth == $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// пропорции картинки одинаковы
				if ($width == $height) {
					$newwidth = $maxwidth;
					$newheight = $maxheight;
				}
				// ширина больше
				elseif ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется горизонтальная картинка
		elseif ($maxwidth > $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше
				if ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
					
					if ($newheight > $maxheight) {
					    $newheight = $maxheight;
                        $newwidth = intval(((float)$newheight / (float)$height) * $width);
					}
				}
				// высота больше или равна ширине
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
			    //echo '1';
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется вертикальная картинка
		elseif ($maxwidth < $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше или равна высоте
				if ($width >= $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}

		// если изменений над картинкой производить не надо - просто копируем её
		if ($newwidth == $width && $newheight == $height && $quality == 100) {
		    if (copy($from, $to.'big/'.$name)) return true;
			else {
			    $this->addErr('Ошибка копирования файла');
			    //$this->_err .= '<br />Ошибка копирования файла!';
			    return false;
			}
		}

		// создаём новое изображение
		
		$new = imagecreatetruecolor($maxwidth, $maxheight);
		$black = imagecolorallocate($new, 0, 0, 0);
		$white = imagecolorallocate($new, 255, 255, 255);
		// копируем старое в новое с учётом новых размеров
		imagefilledrectangle($new, 0, 0, $maxwidth - 1, $maxheight - 1, $white);
		
		$center_w = round(($maxwidth-$newwidth)/2);
		$center_w = ($center_w < 0) ? 0 : $center_w;
		$center_h = round(($maxheight-$newheight)/2);
		$center_h = ($center_h < 0) ? 0 : $center_h;
		imagecopyresampled($new, $img, $center_w, $center_h, 0, 0, $newwidth, $newheight, $width, $height);
		// создаём файл с новым изображением
		switch ($format) {
			case 2: // jpg
				if ($quality < 0) $quality = 0;
				if ($quality > 100) $quality = 100;
				imagejpeg($new, $to.'big/'.$name, $quality);
			break;
			case 3: // png
				$quality = intval($quality * 9 / 100);
				if ($quality < 0) $quality = 0;
				if ($quality > 9) $quality = 9;
				imagepng($new, $to.'big/'.$name, $quality);
			break;
			case 1: // gif
				imagegif($new, $to.'big/'.$name);
			break;
		}
		
		@chmod($to.'big/'.$name, 0644);
		//@copy($from, $to.'big/'.$name);
		//@chmod($to.'big/'.$name, 0644);
		
		return true;
    }
    
    private function uploadDiscPic($from, $to, $name)
    {
    	ini_set('max_execution_time', '360');
        
        $maxwidth = 97;
        $maxheight = 97;
        $quality = 100;
        
        // защита от Null-байт уязвимости PHP
		$from = preg_replace('/\0/uis', '', $from);
		
		// информация об изображении
		$imageinfo = @getimagesize($from);
		// если получить информацию не удалось - ошибка
		if (!$imageinfo) {
		    $this->addErr('>Ошибка получения информации об изображении');
		    return false;
		}
		// получаем параметры изображения
		$width = $imageinfo[0];		// ширина
		$height = $imageinfo[1];	// высота
		$format = $imageinfo[2];	// ID формата (число)
		$mime = $imageinfo['mime'];	// mime-тип

		// определяем формат и создаём изображения
		switch ($format) {
			case 2: $img = imagecreatefromjpeg($from); break;	// jpg
			case 3: $img = imagecreatefrompng($from); break;	// png
			case 1: $img = imagecreatefromgif($from); break;	// gif
			default: $this->addErr('Неверный или недопустимый формат загружаемого файла'); return false; break;
		}
		// если создать изображение не удалось - ошибка
		if (!$img) {
		    $this->addErr('Ошибка создания изображения');
		    return false;
		}
		
		// меняем размеры изображения
		$newwidth = $width;
		$newheight = $height;
		// требуется квадратная картинка
		if ($maxwidth == $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// пропорции картинки одинаковы
				if ($width == $height) {
					$newwidth = $maxwidth;
					$newheight = $maxheight;
				}
				// ширина больше
				elseif ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется горизонтальная картинка
		elseif ($maxwidth > $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше
				if ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
					
					if ($newheight > $maxheight) {
					    $newheight = $maxheight;
                        $newwidth = intval(((float)$newheight / (float)$height) * $width);
					}
				}
				// высота больше или равна ширине
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
			    //echo '1';
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется вертикальная картинка
		elseif ($maxwidth < $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше или равна высоте
				if ($width >= $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}

		// если изменений над картинкой производить не надо - просто копируем её
		if ($newwidth == $width && $newheight == $height && $quality == 100) {
		    if (copy($from, $to.$name)) return true;
			else {
			    $this->addErr('Ошибка копирования файла');
			    //$this->_err .= '<br />Ошибка копирования файла!';
			    return false;
			}
		}

		// создаём новое изображение
		
		$new = imagecreatetruecolor($maxwidth, $maxheight);
		$black = imagecolorallocate($new, 0, 0, 0);
		$white = imagecolorallocate($new, 255, 255, 255);
		// копируем старое в новое с учётом новых размеров
		imagefilledrectangle($new, 0, 0, $maxwidth - 1, $maxheight - 1, $white);
		
		$center_w = round(($maxwidth-$newwidth)/2);
		$center_w = ($center_w < 0) ? 0 : $center_w;
		$center_h = round(($maxheight-$newheight)/2);
		$center_h = ($center_h < 0) ? 0 : $center_h;
		imagecopyresampled($new, $img, $center_w, $center_h, 0, 0, $newwidth, $newheight, $width, $height);
		// создаём файл с новым изображением
		switch ($format) {
			case 2: // jpg
				if ($quality < 0) $quality = 0;
				if ($quality > 100) $quality = 100;
				imagejpeg($new, $to.$name, $quality);
			break;
			case 3: // png
				$quality = intval($quality * 9 / 100);
				if ($quality < 0) $quality = 0;
				if ($quality > 9) $quality = 9;
				imagepng($new, $to.$name, $quality);
			break;
			case 1: // gif
				imagegif($new, $to.$name);
			break;
		}
		
		@chmod($to.$name, 0644);
		
		
		
        // Большая картинка, используется в билетах PDF
        $maxwidth = 236;
        $maxheight = 236;
        $quality = 100;
        
        // защита от Null-байт уязвимости PHP
		//$from = preg_replace('/\0/uis', '', $from);
		
		// информация об изображении
		//$imageinfo = @getimagesize($from);
		// если получить информацию не удалось - ошибка
		/*if (!$imageinfo) {
		    $this->addErr('>Ошибка получения информации об изображении');
		    return false;
		}*/
		// получаем параметры изображения
		$width = $imageinfo[0];		// ширина
		$height = $imageinfo[1];	// высота
		$format = $imageinfo[2];	// ID формата (число)
		$mime = $imageinfo['mime'];	// mime-тип

		// определяем формат и создаём изображения
		switch ($format) {
			case 2: $img = imagecreatefromjpeg($from); break;	// jpg
			case 3: $img = imagecreatefrompng($from); break;	// png
			case 1: $img = imagecreatefromgif($from); break;	// gif
			default: $this->addErr('Неверный или недопустимый формат загружаемого файла'); return false; break;
		}
		// если создать изображение не удалось - ошибка
		if (!$img) {
		    $this->addErr('Ошибка создания изображения');
		    return false;
		}
		
		// меняем размеры изображения
		$newwidth = $width;
		$newheight = $height;
		// требуется квадратная картинка
		if ($maxwidth == $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// пропорции картинки одинаковы
				if ($width == $height) {
					$newwidth = $maxwidth;
					$newheight = $maxheight;
				}
				// ширина больше
				elseif ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется горизонтальная картинка
		elseif ($maxwidth > $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше
				if ($width > $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
					
					if ($newheight > $maxheight) {
					    $newheight = $maxheight;
                        $newwidth = intval(((float)$newheight / (float)$height) * $width);
					}
				}
				// высота больше или равна ширине
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
			    //echo '1';
				$newwidth = $width;
				$newheight = $height;
			}
		}
		// требуется вертикальная картинка
		elseif ($maxwidth < $maxheight) {
			// размеры картинки больше по X и по Y
			if ($width > $maxwidth && $height > $maxheight) {
				// ширина больше или равна высоте
				if ($width >= $height) {
					$newwidth = $maxwidth;
					$newheight = intval(((float)$newwidth / (float)$width) * $height);
				}
				// высота больше
				else {
					$newheight = $maxheight;
					$newwidth = intval(((float)$newheight / (float)$height) * $width);
				}
			}
			// размеры картинки больше только по X
			elseif ($width > $maxwidth) {
				$newwidth = $maxwidth;
				$newheight = intval(((float)$newwidth / (float)$width) * $height);
			}
			// размеры картинки больше только по Y
			elseif ($height > $maxheight) {
				$newheight = $maxheight;
				$newwidth = intval(((float)$newheight / (float)$height) * $width);
			}
			// в остальных случаях ничего менять не надо
			else {
				$newwidth = $width;
				$newheight = $height;
			}
		}

		// если изменений над картинкой производить не надо - просто копируем её
		if ($newwidth == $width && $newheight == $height && $quality == 100) {
		    if (copy($from, $to.'big/'.$name)) return true;
			else {
			    $this->addErr('Ошибка копирования файла');
			    //$this->_err .= '<br />Ошибка копирования файла!';
			    return false;
			}
		}

		// создаём новое изображение
		
		$new = imagecreatetruecolor($maxwidth, $maxheight);
		$black = imagecolorallocate($new, 0, 0, 0);
		$white = imagecolorallocate($new, 255, 255, 255);
		// копируем старое в новое с учётом новых размеров
		imagefilledrectangle($new, 0, 0, $maxwidth - 1, $maxheight - 1, $white);
		
		$center_w = round(($maxwidth-$newwidth)/2);
		$center_w = ($center_w < 0) ? 0 : $center_w;
		$center_h = round(($maxheight-$newheight)/2);
		$center_h = ($center_h < 0) ? 0 : $center_h;
		imagecopyresampled($new, $img, $center_w, $center_h, 0, 0, $newwidth, $newheight, $width, $height);
		// создаём файл с новым изображением
		switch ($format) {
			case 2: // jpg
				if ($quality < 0) $quality = 0;
				if ($quality > 100) $quality = 100;
				imagejpeg($new, $to.'big/'.$name, $quality);
			break;
			case 3: // png
				$quality = intval($quality * 9 / 100);
				if ($quality < 0) $quality = 0;
				if ($quality > 9) $quality = 9;
				imagepng($new, $to.'big/'.$name, $quality);
			break;
			case 1: // gif
				imagegif($new, $to.'big/'.$name);
			break;
		}
		
		@chmod($to.'big/'.$name, 0644);
		//@copy($from, $to.'big/'.$name);
		//@chmod($to.'big/'.$name, 0644);
		
		return true;
    }
    
    /*
     * Билеты. Добавление ряда в секторах.
     */
    
	public function addrow()
	{
            $this->tpl->assign(
                array(
                    'BACK_PAGE_ROW' => ''
                )
            );        
            
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $hall = $this->db->fetchRow("SELECT * FROM `sectors` WHERE `id` = '$id'");
        
        if (!$hall) {
            return false;
        }
        
        $this->setMetaTags('Добавление рядов');
        
        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('rows', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
		
		if (!empty($_POST)) {
		    $array = $_POST['series'];
		    //var_dump($array);
                    //die;
		    foreach ($array as $key => $row) {
                        
                        
                        
		        if (!empty($row['name'])) {
                            
                            $issetRow = $this->db->fetchRow("SELECT * FROM `series` WHERE `id_sector` = '$id' AND `row_name`='".$row['name']."'");
                            if ($issetRow) {
                                $err .= 'Ряд '.$row['name'].' уже существует';
                                
                            }
                            else{
		            $data = array(
                        'id_sector'=>$id,
                        'id_hall' => $hall['hall_id'],
                        'row_name' => $row['name'],
                        'first_location' => (int) $row['first_location'],
                        'count_location' => (int) $row['count_location']
		            );
		            
		            $this->logger->addLogRow('Добавление рядов', serialize($data));
		            
		            $this->db->insert('series', $data);
                            //$idRow = $this->db->lastInsertId();
                            //die;
                            }
		        }
		    }
		    if (empty($err))
                        $content = "Элементы успешно добавлены<meta http-equiv='refresh' content='2;URL=/admin/editsector/$id'>";
                    else
                        $content = $err.' <br> <a href="">Попробовать еще.</a>';
            $this->viewMessage($content);
            
            return true;
		}
		
		$this->tpl->parse('CONTENT', '.start');
		
		$name = '';
		$count = 0;
		
		for ($i=0; $i<5; $i++) {
		    $this->tpl->assign('INCREMENT', $i);
		    $this->tpl->assign(
                array(
                    'ROW_NAME' => $name,
                    'ROW_COUNTF' => $count,
                    'ROW_COUNT' => $count
                )
            );
            $this->tpl->parse('CONTENT', '.rows');
		}
        $this->tpl->parse('CONTENT', '.end');
        
        return true;
	}  
        
        /*
         * Удаялет ряд сектора по id ряда
         */
        public function deleterow()
	{
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $sector = $this->db->fetchRow("SELECT * FROM `series` WHERE `id` = '$id'");
        
        if (!$sector) {
            return false;
        }
        
        $this->setMetaTags('Удаление ряда');
        
        $this->logger->addLogRow('Удаление ряда', serialize($sector));
        
        $this->db->delete("series", "id = '$id'");
        
        $content = "Элемент успешно удален<meta http-equiv='refresh' content='2;URL=/admin/editsector/".$sector['id_sector']."'>";
            
        $this->viewMessage($content);
        
        return true;
	}
/*
 * Редактирование данных о ряде по его id
 * 
 */
    public function editrow()
	{
        
       
	    $id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $row = $this->db->fetchRow("SELECT * FROM `series` WHERE `id` = '$id'");
        
        if (!$row) {
            return false;
        }
        
        $this->setMetaTags('Редактирование ряда');
        
        $name = $this->getVar('row_name', '', $row);
        $first_location = $this->getVar('first_location', '', $row);
        $count_location = $this->getVar('count_location', '', $row);
        
        //$count = $this->getVar('count', 0, $row);
        
        if (!empty($_POST)) {
            
            $this->logger->addLogRow('Редактирование ряда', serialize($row));
            
            $name = $this->getVar('name', '', $_POST['series'][0]);
            $first_location = $this->getVar('first_location', '', $_POST['series'][0]);
            $count_location = $this->getVar('count_location', '', $_POST['series'][0]);
            
            //$count = $this->getVar('count', '', $_POST['series'][0]);
            
            //Проверим есть, ли уже ряд в этом секторе с таким названием 
            $issetRow=0;
            $issetRow = $this->db->fetchRow("SELECT * FROM `series` WHERE `row_name`='$name' AND `id`<>'$id' AND `id_sector`='".$row['id_sector']."'");
            //echo "SELECT * FROM `series` WHERE `row_name`='$name' AND `id`<>'$id' AND `id_sector`='".$row['id_sector']."'";
            //echo $issetRow['id'];
            if ($issetRow) {
                $err = 'Ряд '.$name.' уже есть в секторе ('.$row['id_sector'].') <br/> <a href="">Попробовать еще.</a>';
                $content = $err;
            } else {
                $data = array(
                    'row_name' => $name,
                    'first_location'=>$first_location,
                    'count_location' => $count_location
                );
                
                $this->db->update('series', $data, "`id` = '$id'");
            
                $content = "Элемент успешно изменен<meta http-equiv='refresh' content='2;URL=/admin/editsector/".$row['id_sector']."'>";
            }    
            $this->viewMessage($content);
            
            return true;
        }
        
        
        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('rows', 'edit');
		$this->tpl->define_dynamic('end', 'edit');
        
        $this->tpl->assign(
            array(
                'INCREMENT' => '0',
                'ROW_NAME' => $name,
                'ROW_COUNTF' => $first_location,
                'ROW_COUNT' => $count_location,
                'SECTOR_ID'=>$id,
                'BACK_PAGE_ROW'=>'<a href="/admin/editsector/'.$row['id_sector'].'">Вернуться к "Редактирование сектора"</a> | 
                                <a href="/admin/viewhall/'.$row['id_hall'].'">Вернуться к "Редактирование зала"</a>'
            )
        );
        
        $this->tpl->parse('CONTENT', '.start');
		$this->tpl->parse('CONTENT', '.rows');
        $this->tpl->parse('CONTENT', '.end');
        
        
        return true;
	}
        public function delpicpage (){
            $id = end($this->url);
            $selPic = $this->db->fetchOne ("SELECT `pic` FROM `page` WHERE `id`='".$id."'");
            echo $selPic;
            if (file_exists(PATH.'images/pages/'.$selPic)) {
                    	@unlink(PATH.'images/pages/'.$selPic);
            }
            $data = array(
                'pic'           => ''
            );
            $this->db->update("page", $data, "id = '$id'");
            header ('location:/admin/editpage/'.$id);
            return true;
        }
        public function deleteOrderDisc(){
            $id = end($this->url);
            $this->db->delete('order_disc', "id = '$id'");    
            $this->db->delete('order_disc_items', "order_id = '$id'"); 
            header ("location: /admin/orders/");
            return true;

        }
        /*
         * Если пользователь оплатил билеты, 
         * то вышестоящем руководителям приходит уведомление
         */
    protected function sendMailTopUserDisc($id){
        $orderInfoCount = $this->db->fetchOne("SELECT SUM(`count`) FROM `order_disc_items` WHERE `order_id` = '".$id."'");
        $orderInfoCost = $this->db->fetchOne("SELECT SUM(`summ`) FROM `order_disc_items` WHERE `order_id` = '".$id."'");
        $orderId = $this->db->fetchOne("SELECT `user_id` FROM `order_disc` WHERE `id` = '".$id."'");
        $orderVal = $this->db->fetchOne("SELECT `country` FROM `order_disc` WHERE `id` = '".$id."'");
        $top = $this->db->fetchRow("SELECT `top_user_no`, `id`, `email`, `name`, `surname` FROM `users` WHERE `id` = '".$orderId."'");
        $orderInfoCost = number_format($orderInfoCost, 0, '', ' ');
        $subject = 'Активность партнеров вашей структуры';
        $body = '
            <p>Партнер из вашей организации '.$top['surname'].' '.$top['name'].' осуществил покупку '.$orderInfoCount.' дисков на сумму '.$orderInfoCost.' '.($orderVal=='ru'?'руб':'грн').' на официальном сайте поддержки бизнеса команды Альянса Бриллиантов www.upline24.ru.</p>
            <p>Данное письмо было получено вами автоматически, так как вы зарегистрированы на сайте UpLine24 и являетесь спонсором пользователя '.$top['surname'].' '.$top['name'].', совершившего покупку дисков на сайте. Просмотреть историю покупок вы можете в разделе Личного кабинета «Партнеры».</p>
            <p>Если у вас возникли вопросы, свяжитесь с нами, отправив письмо на e-mail: upline24@gmail.com, или по номерам телефонов: +7 916 95 22 195 (Россия); + 38 063 798 00 79 (Украина). Прием звонков с 10:00 до 18:00 МСК.</p>
            <p>---</p>
            <p>Стройте бизнес уверенно, ощущая нашу поддержку!</p>
            <p><b>Diamond Alliance</b></p>';
        if ($top['top_user_no']!=0 AND $top['top_user_no']!=''){
            $topUserNo = $top['top_user_no'];
            if ($topUserNo>0){
                $nextTopUserNo=$topUserNo;
                while ($nextTopUserNo!=0){
                    $to = '';
                    $hello = '';
                    $NextTop = $this->db->fetchRow("SELECT `top_user_no`, `name`, `surname`, `id`, `email` FROM `users` WHERE `number` = '$nextTopUserNo'");
                    if ($NextTop['email']!='') {
                        $to = $NextTop['name'].' '.$NextTop['surname']." <".$NextTop['email'].">";
                        $hello = '<p>Здравствуйте, '.$NextTop['name'].'!</p>';
                    }
                    if ($NextTop['top_user_no']!=0 AND $NextTop['top_user_no']!='') {
                        $nextTopUserNo = $NextTop['top_user_no'];
                    } else {
                        $nextTopUserNo=0;
                    }
                    $headers= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-type: text/html; charset=Windows-1251\r\n";
                    $headers .= "From: Upline24 <Upline24@upline24.ru>\r\n";
                    $headers .= "Cc: Upline24@upline24.ru\r\n";
                    $headers .= "Bcc: Upline24@upline24.ru\r\n";
                    if ($to!='')
                        mail($to, $subject, $hello . $body, $headers);
                }
            }
        }
    }
        
        
    /*
     * Отправка письма вышестоящим, если пользователь оплатил билеты
     */
    protected function sendMailTopUserTickets($uN,$ids){
        $orderInfoCC = $this->db->fetchRow("SELECT `count`, `cost`, `user_id`, `country` FROM `tickets` WHERE `id` = '".$ids."'");
        $userInfo =  $this->db->fetchRow("SELECT `name`, `surname`, `top_user_no` FROM `users` WHERE `id` = '".$orderInfoCC['user_id']."'");        
        $summ = $orderInfoCC['count']*$orderInfoCC['cost'];
        $summ = number_format($summ, 0, '', ' ');
        $top = $this->db->fetchRow("SELECT `top_user_no`, `id`, `email`, `name`, `surname` FROM `users` WHERE `id` = '".$orderId."'");
        $subject = 'Активность партнеров вашей структуры';
        $body = '
            <p>Партнер из вашей организации '.$userInfo['surname'].' '.$userInfo['name'].' осуществил покупку '.$orderInfoCC['count'].' билетов на сумму '.$summ.' '.($orderInfoCC['country']=='ru'?'руб':'грн').' на официальном сайте поддержки бизнеса команды Альянса Бриллиантов www.upline24.ru.</p>
            <p>Данное письмо было получено вами автоматически, так как вы зарегистрированы на сайте UpLine24 и являетесь спонсором пользователя '.$userInfo['surname'].' '.$userInfo['name'].', совершившего покупку билетов на сайте. Просмотреть историю покупок вы можете в разделе Личного кабинета «Партнеры».</p>
            <p>Если у вас возникли вопросы, свяжитесь с нами, отправив письмо на e-mail: upline24@gmail.com, или по номерам телефонов: +7 916 95 22 195 (Россия); + 38 063 798 00 79 (Украина). Прием звонков с 10:00 до 18:00 МСК.</p>
            <p>---</p>
            <p>Стройте бизнес уверенно, ощущая нашу поддержку!</p>
            <p><b>Diamond Alliance</b></p>';
        if ($userInfo['top_user_no']!=0 AND $userInfo['top_user_no']!=''){
            $topUserNo = $userInfo['top_user_no'];
            if ($topUserNo>0){
                $nextTopUserNo=$topUserNo;
                while ($nextTopUserNo!=0){
                    $to = '';
                    $NextTop = $this->db->fetchRow("SELECT `top_user_no`, `name`, `surname`, `id`, `email` FROM `users` WHERE `number` = '$nextTopUserNo'");
                    if ($NextTop['email']!='') {
                        $to = $NextTop['name'].' '.$NextTop['surname']." <".$NextTop['email'].">";
                        $hello = '<p>Здравствуйте, '.$NextTop['name'].'!</p>';
                    }
                    if ($NextTop['top_user_no']!=0 AND $NextTop['top_user_no']!='') {
                        $nextTopUserNo = $NextTop['top_user_no'];
                    } else {
                        $nextTopUserNo=0;
                    }
                    $headers= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-type: text/html; charset=Windows-1251\r\n";
                    $headers .= "From: Upline24 <Upline24@upline24.ru>\r\n";
                    $headers .= "Cc: Upline24@upline24.ru\r\n";
                    $headers .= "Bcc: Upline24@upline24.ru\r\n";
                    if ($to!='')
                        mail($to, $subject, $hello . $body, $headers);
                }
            }
        }
    }
    
    public function editorder(){
        $id = end($this->url);
        if (!ctype_digit($id)) {
            return $this->error404();
        }
        $order = $this->db->fetchRow("SELECT * FROM `order_disc` WHERE `id` = '$id'");
        if (!$order) {
            return $this->error404();
        }
        $this->setMetaTags('Редактирование заказа');
        $this->setWay('Редактирование заказа');
        $this->tpl->define_dynamic('start', 'edit');
        $this->tpl->define_dynamic('order_disc_edit', 'edit');
        $this->tpl->define_dynamic('end', 'edit');
        $index = $order['zip'];
        $city = $order['city'];
        $adr = $order['adres'];
        $fio = $order['fullname'];
        $passport = $order['passport'];
        $phone = $order['phone'];
        $addInfo = $order['info'];
        if (!empty($_POST)) {
            $index = $this->getVar('index', '');
            $city = $this->getVar('city', '');
            $adr = $this->getVar('adr', '');
            $fio = $this->getVar('fio', '');
            $passport = $this->getVar('passport', '');
            $phone = $this->getVar('phone', '');
            $addInfo = $this->getVar('add_info', '');
            $referer = $this->getVar('HTTP_REFERER', $this->basePath);
            $data = array(
                'zip'          => $index,
                'city'          => $city,
                'adres'       => $adr,
                'phone'        => $phone,
                'fullname'         => $fio,
                'passport'      => $passport,
                'info'   => $addInfo,
            );

            $n = $this->db->update('order_disc', $data, "id = $id");
            $content = "Заказ успешно отредактирован<meta http-equiv='refresh' content='2;URL=/admin/orders/'>";   
            $this->viewMessage($content);
        }
        if (empty($_POST)) {
            $this->tpl->assign(
                array(
                    'ED_INDEX' => $index,
                    'ED_CITY' => $city,
                    'ED_ADR' => $adr,
                    'ED_FIO' => $fio,
                    'ED_PASSPORT' => $passport,
                    'ED_PHONE' => $phone,
                    'ED_INFO' => $addInfo,
                )
            );
            $this->tpl->parse('CONTENT', '.start');
            $this->tpl->parse('CONTENT', '.order_disc_edit');
            $this->tpl->parse('CONTENT', '.end');
        }
        return true;    
    }
}


