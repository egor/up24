<?php

require_once PATH . 'library/Abstract.php';

require_once PATH . 'library/Interface.php';

class Partners extends Main_Abstract implements Main_Interface
{
	
	private $_diamond = array();
	
	private $_emerald = array();
	
	private $_platinum = array();
	
	private $_user = array();
	
	private $_event_user = array();
        
        private $_print = '';
	
        private $_count_down_user = 0;
        private $c = 0;
        private $e = 0;
        private $id_e = 0;
	public function factory()
    {
        if (!$this->_isAuth()) {
            return false;
        }
        
        $this->setMetaTags('Партнеры');
        
        return true;
    }
    
    public function main()
    {
        //$this->loadUser();
    	if (!$this->_isAdmin()) {
//            $this->tpl->assign('CONTENT', 'В стадии разработки');
//   		return true;
    	}



 
        $this->printDiamondPartners($this->auth->number);
        $this->_print = '
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/jquery.tree.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(\'ul.partners\').tree();
	});
</script>
 <div class="rzd_list2">'.$this->_print.'</div>';
                    $this -> _print = str_replace('zamenaD',$this->c,$this -> _print);
        $this->tpl->assign('CONTENT', $this->_print);
 
    		//$this->showAdminList();
    		return true;
    	// Release data
    	$type = $this->auth->type;
    	
    	// Development data
    	#$type = 'diamond';
    	#$type = 'emerald';
    	#$type = 'platinum';
    	#$type = 'user';
    	/*
    	if ($this->auth->type == 'user' && !$this->_isAdmin()) {
			return false;
		}
		
		switch ($type) {
			case 'diamond' : $this->showDiamondList(); break;
			case 'emerald' : $this->showEmeraldList(); break;
			case 'platinum' : $this->showPlatinumList(); break;
			default : return false;
		}
    	 */
        
    	return true;
    }
    
    
    // выводит список купленых билетов
    public function tickets()
    {
    	/*if (!$this->_isAdmin()) {
    		$this->tpl->assign('CONTENT', 'В стадии разработки');
    		return true;
    	}
    	*/
    	$id = $this->getVar(2, null, $this->url);
    	
    	if (null === $id) {
    		return false;
    	}
    	
    	if (!ctype_digit($id) || $id < 1) {
    		return false;
    	}
    	
    	$user = $this->db->fetchRow("SELECT * FROM `users` WHERE `id` = '$id'");
    	
    	if (!$user) {
    		return false;
    	}
    	
    	if ($user['privilege'] == 'administrator') {
    		$this->logger->addLogRow('Попытка просмотра покупок Администратора', serialize($this->auth));
    		return false;
    	}
    	
    	$currentUser = $this->auth->surname.' '.$this->auth->name;
    	
    	if (!$this->_isAdmin()) {
    		if ($currentUser != $user['diamond'] && $currentUser != $user['emerald'] && $currentUser != $user['platinum']) {
    			return false;
    		}
    	}
    	
    	$select = $this->db->select();
		$select->from('events', array('id', 'name', 'date', 'pic'));
		$select->join('hall', 'events.hall_id = hall.id', array('adres'));
		$select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
		$select->join('event_types', 'events.type_id = event_types.id', array('event_type' => 'name'));
		$select->join('sectors', 'hall.id = sectors.hall_id', array());
		$select->join('tickets', 'tickets.event_id = events.id AND tickets.sector_id = sectors.id', array('ticket_id' => 'id', 'count', 'payment', 'ticket_number' => 'code'));
		$select->where('tickets.user_id = ?', array("$id"));
		//$select->group('events.id');
		//echo $select->__toString();
		$purchases = $this->db->fetchAll($select);
    	
		$this->tpl->define_dynamic('purchases', 'purchases.tpl');
		$this->tpl->define_dynamic('list', 'purchases');
		$this->tpl->define_dynamic('list_row', 'list');
		$this->tpl->define_dynamic('list_row_status_on', 'list_row');
		$this->tpl->define_dynamic('list_row_status_off', 'list_row');
		$this->tpl->define_dynamic('list_empty', 'purchases');
		
		if ($purchases) {
			$this->tpl->parse('LIST_ROW_EMPTY', 'null');
			
			foreach ($purchases as $row) {
				$pic = '';
				if ($row['pic'] && file_exists('./images/events/'.$row['pic'])) {
					$pic = '<div class="img"><img src="/images/events/'.$row['pic'].'" width="118" height="100" alt="'.$row['name'].'" title="'.$row['name'].'" /></div>';
				}
				
				$this->tpl->assign(
					array(
						'EVENT_ID' => $row['id'],
						'TICKET_ID' => $row['ticket_id'],
						'EVENT_NAME' => $row['name'],
						'TICKET_NUMBER' => $row['ticket_number'],
						'EVENT_DATE' => $this->convertDate($row['date']),
                                                'EVENT_DATE2' => ($row['date']>=$row['date2']?'':' - '.$this->convertDate($row['date2'])),
                                                
						'EVENT_CITY' => $row['city_name'],
						'EVENT_ADRES' => $row['adres'],
						'EVENT_TIME' => $this->convertDate($row['date'], 'H:i'),
						'EVENT_TYPE' => $row['event_type'],
						'EVENT_TICKETS' => ($this->_isAdmin()?'<p>Куплено билетов: <strong>'.$row['count'].'</strong></p>':''),
						'EVENT_PIC' => $pic,
                                                'BUTTON_PRINT' => (!$this->_isAdmin()?'':'<p><input type="submit" value="Напечатать билеты" class="button_print" /></p>'),
                                                
                                                'ACTION_SECURITY' => (!$this->_isAdmin()?'':'/ticketprint')
					)
				);
				
				if ($this->_isAdmin()){
                                    if ($row['payment'] == '1') {
                                        $this->tpl->parse('LIST_ROW_STATUS_OFF', 'null');
                                        $this->tpl->parse('LIST_ROW_STATUS_ON', 'list_row_status_on');
                                    } else {
					$this->tpl->parse('LIST_ROW_STATUS_ON', 'null');
					$this->tpl->parse('LIST_ROW_STATUS_OFF', 'list_row_status_off');
                                    }
                                }
                                else{
                                    $this->tpl->parse('LIST_ROW_STATUS_ON', 'null');
                                    $this->tpl->parse('LIST_ROW_STATUS_OFF', 'null');
                                }
                                    
				$this->tpl->parse('LIST_ROW', '.list_row');
			}
		} else {
			$this->tpl->parse('LIST_ROW', 'null');
			$this->tpl->parse('LIST_ROW_EMPTY', '.list_row_empty');
		}
		
		$this->tpl->parse('CONTENT', '.list');
		
    	return true;
    }
    
    public function viewevent()
    {
        
        
        
        $id = $this->getVar(2, null, $this->url);
    	
    	if (null === $id) {
    		return false;
    	}
    	
        $this->id_e=$id;
        
        
         //$this->loadUser();
    	if (!$this->_isAdmin()) {
//            $this->tpl->assign('CONTENT', 'В стадии разработки');
//   		return true;
    	}



 
        $this->printDiamondPartners($this->auth->number);
        $this->_print = '
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/jquery.tree.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		//$(\'ul.partners\').tree();
                //document.getElementsByTagName("span").className="open";
	});
</script><br />Партнеры, купившие билет на данный семинар выделены оранжевым цветом.
 <div class="rzd_list2">'.$this->_print.'</div>';
                    $this -> _print = str_replace('zamenaD',$this->c,$this -> _print);
        $this->tpl->assign('CONTENT', $this->_print);
 
    		//$this->showAdminList();
    		return true;
    	// Release data
    	$type = $this->auth->type;
    	
    	// Development data
    	#$type = 'diamond';
    	#$type = 'emerald';
    	#$type = 'platinum';
    	#$type = 'user';
    	/*
    	if ($this->auth->type == 'user' && !$this->_isAdmin()) {
			return false;
		}
		
		switch ($type) {
			case 'diamond' : $this->showDiamondList(); break;
			case 'emerald' : $this->showEmeraldList(); break;
			case 'platinum' : $this->showPlatinumList(); break;
			default : return false;
		}
    	 */
        
    	return true;
        
        
        
        
        
        
        
        /*
    	if (!$this->_isAdmin()) {
    		$this->tpl->assign('CONTENT', 'В стадии разработки');
    		return true;
    	}
    	*/
    	$id = $this->getVar(2, null, $this->url);
    	
    	if (null === $id) {
    		return false;
    	}
    	
    	if (!ctype_digit($id) || $id < 1) {
    		return false;
    	}
    	//$this->main(); 
    	$this->loadUser();
    	$this->loadEvent($id);
    	
    	if ($this->_isAdmin()) {
    		$this->eventAdmin();
    		return true;
    	}
    	
    	$type = $this->auth->type;
    	
    	// Development data
    	#$type = 'diamond';
    	#$type = 'emerald';
    	#$type = 'platinum';
    	
    	switch ($type) {
    		case 'diamond' : $this->eventDiamond(); break;
    		case 'emerald' : $this->eventEmerald(); break;
    		case 'platinum' : $this->eventPlatinum(); break;
    		default : return false;
    	}
    	
    	return true;
    }
    
    private function eventAdmin()
    {
    	$partners = '
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/jquery.tree.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(\'ul.partners\').tree();
	});
</script>
    	';
    	
    	$partners .= '<div class="rzd_list2">';
    	//$partners .= '<h2>Total Users: '.(sizeof($this->_diamond) + sizeof($this->_emerald) + sizeof($this->_platinum) + sizeof($this->_user)).'</h2>';
    	$partners .= '<ul class="partners">';
    	
    	foreach ($this->_diamond as $diamond) {
            $li_1=0;
    		$diamondUsers = $this->getDiamondUsers($diamond['surname'].' '.$diamond['name']);
    		$diamondCount = sizeof($diamondUsers['emerald']) +
    						sizeof($diamondUsers['platinum']) +
    						sizeof($diamondUsers['user']);
    		
			$partners .= '<li>';
                        //$li_1=0;
			$partners .= '<img src="/img/icons/diamond.gif" width="11px" height="11px" /> '.$diamond['surname'].' '.$diamond['name'].' ('.$this->getUserCountTicket($diamond['id']).') (li_1_rep)';
                        
			//$partners .= '<img src="/img/icons/diamond.gif" width="11px" height="11px" /> '.(!empty($diamondUsers['emerald']) || !empty($diamondUsers['platinum']) || !empty($diamondUsers['user']) ? '<span class="open" style="cursor: pointer;">'.$diamond['surname'].' '.$diamond['name'].' ('.$diamondCount.')</span>' : $diamond['surname'].' '.$diamond['name'].' ('.$diamondCount.')');
    		
    		if (!empty($diamondUsers['emerald'])) {
    			$partners .= '<ul>';
    			
    			foreach ($diamondUsers['emerald'] as $emerald) {
                                $li_2=0;
    				$emeraldUsers = $this->getEmeraldUsers($emerald['surname'].' '.$emerald['name']);
    				$emeraldCount = sizeof($emeraldUsers['platinum']) +
    								sizeof($emeraldUsers['user']);
    				
    				$partners .= '<li>';
    				
    				$partners .= '<img src="/img/icons/emerald.gif" width="11px" height="11px" /> '.$emerald['surname'].' '.$emerald['name'].' ('.$this->getUserCountTicket($emerald['id']).') (li_2_rep)';
    				$li_1 = $li_1 + $this->getUserCountTicket($emerald['id']);
    				if (!empty($emeraldUsers['platinum'])) {
    					$partners .= '<ul>';
    					
    					foreach ($emeraldUsers['platinum'] as $platinum) {
                                                $li_3=0;
    						$platinumUsers = $this->getPlatinumUsers($platinum['surname'].' '.$platinum['name']);
    						$platinumCount = sizeof($platinumUsers['user']);
    						
    						$partners .= '<li>';
    						
    						$partners .= '<img src="/img/icons/platinum.gif" width="11px" height="11px" /> '.$platinum['surname'].' '.$platinum['name'].' ('.$this->getUserCountTicket($platinum['id']).') (li_3_rep)';
    						$li_1 = $li_1 + $this->getUserCountTicket($platinum['id']);
                                                $li_2 = $li_2 + $this->getUserCountTicket($platinum['id']);
    						if (!empty($platinumUsers['user'])) {
    							$partners .= '<ul>';
    							
    							foreach ($platinumUsers['user'] as $user) {
    								$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
    								
    								$partners .= $user['surname'].' '.$user['name'].' ('.$this->getUserCountTicket($user['id']).')';
    								$li_1 = $li_1 + $this->getUserCountTicket($user['id']);
                                                                $li_2 = $li_2 + $this->getUserCountTicket($user['id']);
                                                                $li_3 = $li_3 + $this->getUserCountTicket($user['id']);
    								$partners .= '</li>';
    							}
    							
    							$partners .= '</ul>';
    						}
    						
    						$partners .= '</li>';
                                                $partners = str_replace('li_3_rep', $li_3, $partners);
    					}
    					
    					$partners .= '</ul>';
    				}
    				
    				if (!empty($emeraldUsers['user'])) {
    					$partners .= '<ul>';
    					
    					foreach ($emeraldUsers['user'] as $user) {
    						$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
    						
    						$partners .= $user['surname'].' '.$user['name'].' ('.$this->getUserCountTicket($user['id']).')';
    						$li_1 = $li_1 + $this->getUserCountTicket($user['id']);
                                                $li_2 = $li_2 + $this->getUserCountTicket($user['id']);
    						$partners .= '</li>';
    					}
    					
    					$partners .= '</ul>';
    				}
    				
    				$partners .= '</li>';
                                $partners = str_replace('li_2_rep', $li_2, $partners);
    			}
    			
    			$partners .= '</ul>';
    		}
    		
    		if (!empty($diamondUsers['platinum'])) {
    			$partners .= '<ul>';
    			
    			foreach ($diamondUsers['platinum'] as $platinum) {
    				$platinumUsers = $this->getPlatinumUsers($platinum['surname'].' '.$platinum['name']);
    				$platinumCount = sizeof($platinumUsers['user']);
    				
    				$partners .= '<li>';
    				
    				$partners .= '<img src="/img/icons/platinum.gif" width="11px" height="11px" /> '.$platinum['surname'].' '.$platinum['name'].' ('.$this->getUserCountTicket($platinum['id']).')';
    				$li_1 = $li_1 + $this->getUserCountTicket($platinum['id']);
    				if (!empty($platinumUsers['user'])) {
    					$partners .= '<ul>';
    					
    					foreach ($platinumUsers['user'] as $user) {
    						$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
    						
    						$partners .= $user['surname'].' '.$user['name'].' ('.$this->getUserCountTicket($user['id']).')';
    						$li_1 = $li_1 + $this->getUserCountTicket($user['id']);
    						$partners .= '</li>';
    					}
    					
    					$partners .= '</ul>';
    				}
    				
    				$partners .= '</li>';
    			}
    			
    			$partners .= '</ul>';
    		}
    		
    		if (!empty($diamondUsers['user'])) {
    			$partners .= '<ul>';
    			
    			foreach ($diamondUsers['user'] as $user) {
    				$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
    				
    				$partners .= $user['surname'].' '.$user['name'].' ('.$this->getUserCountTicket($user['id']).')';
    				$li_1 = $li_1 + $this->getUserCountTicket($user['id']);
    				$partners .= '</li>';
    			}
    			
    			$partners .= '</ul>';
    		}
    		
    		$partners .= '</li>';
    	$partners = str_replace('li_1_rep', $li_1, $partners);
                
        }
    	
    	$partners .= '</ul>';
    	$partners .= '</div>';
        
    	//str_replace( $li_1;
    	$this->tpl->assign('CONTENT', $partners);
    }
    
    private function eventDiamond()
    {
    	// Release data
    	$userName = $this->auth->surname.' '.$this->auth->name;
    	
    	// Development data
    	#$userName = 'Сапрыкина Галина';
    	#$userName = 'Царук Лариса';
    	#$userName = 'Проскура Александра';
    	#$userName = 'Ляшенко Михаил';
    	#$userName = 'Солошенко Татьяна';
    	#$userName = 'Ким Олег';
    	#$userName = 'Ена Наталья';
    	#$userName = 'Демичева Светлана';
    	#$userName = 'Уразакаев Максим';
    	
    	$diamondUsers = $this->getDiamondUsers($userName);
    	
    	if (empty($diamondUsers['emerald']) && empty($diamondUsers['platinum']) && empty($diamondUsers['user'])) {
    		$this->tpl->assign('CONTENT', 'Партнеров не найдено.');
    		return true;
    	}
    	
    	$partners = '
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/jquery.tree.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(\'ul.partners\').tree();
	});
</script>
    	';
    	
    	$partners .= '<div class="rzd_list2">';
    	
    	$partners .= '<ul class="partners">';
    	
    	foreach ($diamondUsers['emerald'] as $emerald) {
			$li_1=0;
                        $emeraldUsers = $this->getEmeraldUsers($emerald['surname'].' '.$emerald['name']);
			$emeraldCount = sizeof($emeraldUsers['platinum']) +
							sizeof($emeraldUsers['user']);
			
			$partners .= '<li>';
			
			$partners .= '<img src="/img/icons/emerald.gif" width="11px" height="11px" /> '.$emerald['surname'].' '.$emerald['name'].' ('.$this->getUserCountTicket($emerald['id']).') (li_1_rep)';
			
			if (!empty($emeraldUsers['platinum'])) {
				$partners .= '<ul>';
				
				foreach ($emeraldUsers['platinum'] as $platinum) {
					$platinumUsers = $this->getPlatinumUsers($platinum['surname'].' '.$platinum['name']);
					$platinumCount = sizeof($platinumUsers['user']);
					
					$partners .= '<li>';
					
					$partners .= '<img src="/img/icons/platinum.gif" width="11px" height="11px" /> '.$platinum['surname'].' '.$platinum['name'].' ('.$this->getUserCountTicket($platinum['id']).')';
					$li_1 = $li_1 + $this->getUserCountTicket($platinum['id']);
					if (!empty($platinumUsers['user'])) {
						$partners .= '<ul>';
						
						foreach ($platinumUsers['user'] as $user) {
							$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
							
							$partners .= $user['surname'].' '.$user['name'].' ('.$this->getUserCountTicket($user['id']).')';
                                                        $li_1 = $li_1 + $this->getUserCountTicket($user['id']);
							
							$partners .= '</li>';
						}
						
						$partners .= '</ul>';
					}
					
					$partners .= '</li>';
				}
				
				$partners .= '</ul>';
			}
			
			if (!empty($emeraldUsers['user'])) {
				$partners .= '<ul>';
				
				foreach ($emeraldUsers['user'] as $user) {
					$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
					
					$partners .= $user['surname'].' '.$user['name'].' ('.$this->getUserCountTicket($user['id']).')';
					$li_1 = $li_1 + $this->getUserCountTicket($user['id']);
					$partners .= '</li>';
				}
				
				$partners .= '</ul>';
			}
			$partners = str_replace('li_1_rep', $li_1, $partners);
			$partners .= '</li>';
		}
		
		foreach ($diamondUsers['platinum'] as $platinum) {
			$platinumUsers = $this->getPlatinumUsers($platinum['surname'].' '.$platinum['name']);
			$platinumCount = sizeof($platinumUsers['user']);
			
			$partners .= '<li>';
			
			$partners .= '<img src="/img/icons/platinum.gif" width="11px" height="11px" /> '.$platinum['surname'].' '.$platinum['name'].' ('.$this->getUserCountTicket($platinum['id']).')';
			
			if (!empty($platinumUsers['user'])) {
				$partners .= '<ul>';
				
				foreach ($platinumUsers['user'] as $user) {
					$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
					
					$partners .= $user['surname'].' '.$user['name'].' ('.$this->getUserCountTicket($user['id']).')';
					
					$partners .= '</li>';
				}
				
				$partners .= '</ul>';
			}
			
			$partners .= '</li>';
		}
		
		foreach ($diamondUsers['user'] as $user) {
			$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
			
			$partners .= $user['surname'].' '.$user['name'].' ('.$this->getUserCountTicket($user['id']).')';
			
			$partners .= '</li>';
		}
    	
    	$partners .= '</ul>';
    	$partners .= '</div>';
    	
    	
    	$this->tpl->assign('CONTENT', $partners);
    }
    
    private function eventEmerald()
    {
    	// Release data
    	$userName = $this->auth->surname.' '.$this->auth->name;
    	
    	// Development data
    	#$userName = 'Соколова Татьяна';
    	#$userName = 'Курочка Наталья';
    	#$userName = 'Ена Ольга'; #CHECK THIS
    	#$userName = 'Переход Виктор'; #CHECK THIS
    	#$userName = 'Кардаш Олег';
    	#$userName = 'Плеханова Галина'; #CHECK THIS
    	#$userName = 'Волошко Наталья'; #CHECK THIS
    	#$userName = 'Коряк Петр';
    	#$userName = 'Астахов Денис'; #CHECK THIS
    	#$userName = 'Аболина Лариса'; #CHECK THIS
    	#$userName = 'Онда Галина'; #CHECK THIS
    	#$userName = 'Шелест Юрий'; #CHECK THIS
    	#$userName = 'Клименко Александр'; #CHECK THIS
    	#$userName = 'Левченко Сергей'; #CHECK THIS
    	#$userName = 'Бундюк Игорь'; #CHECK THIS
    	#$userName = 'Троицкий Николай';
    	#$userName = 'Улиганич Йосиф'; #CHECK THIS
    	#$userName = 'Бескорованная Людмила'; #CHECK THIS
    	#$userName = 'Калинченков Вячеслав'; #CHECK THIS
    	#$userName = 'Калинченков Вячеслав'; Калинченков Вячеслав Вячеслав !!!!!!!!!!!!!!!!!!
    	#$userName = 'Старцева Лиена';
    	#$userName = 'Болоцкий Николай'; #CHECK THIS
    	#$userName = 'Жансеитова Венера';
    	
    	$emeraldUsers = $this->getEmeraldUsers($userName);
    	
    	if (empty($emeraldUsers['platinum']) && empty($emeraldUsers['user'])) {
    		$this->tpl->assign('CONTENT', 'Партнеров не найдено.');
    		return true;
    	}
    	
    	$partners = '
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/jquery.tree.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(\'ul.partners\').tree();
	});
</script>
    	';
    	
    	$partners .= '<div class="rzd_list2">';
    	
    	$partners .= '<ul class="partners">';
    	
    	foreach ($emeraldUsers['platinum'] as $platinum) {
			$platinumUsers = $this->getPlatinumUsers($platinum['surname'].' '.$platinum['name']);
			$platinumCount = sizeof($platinumUsers['user']);
			
			$partners .= '<li>';
			
			$partners .= '<img src="/img/icons/platinum.gif" width="11px" height="11px" /> '.$platinum['surname'].' '.$platinum['name'].' ('.$this->getUserCountTicket($platinum['id']).')';
			
			if (!empty($platinumUsers['user'])) {
				$partners .= '<ul>';
				
				foreach ($platinumUsers['user'] as $user) {
					$partners .= '<li>';
					
					$partners .= $user['surname'].' '.$user['name'].' ('.$this->getUserCountTicket($user['id']).')';
					
					$partners .= '</li>';
				}
				
				$partners .= '</ul>';
			}
			
			$partners .= '</li>';
		}
		
		foreach ($emeraldUsers['user'] as $user) {
			$partners .= '<li>';
			
			$partners .= $user['surname'].' '.$user['name'].' ('.$this->getUserCountTicket($user['id']).')';
			
			$partners .= '</li>';
		}
    	
    	$partners .= '</ul>';
    	$partners .= '</div>';
    	
    	
    	$this->tpl->assign('CONTENT', $partners);
    }
    
    private function eventPlatinum()
    {
    	// Release data
    	$userName = $this->auth->surname.' '.$this->auth->name;
    	
    	// Development data
    	#$userName = 'Курочка Наталья';
    	
    	$platinumUsers = $this->getPlatinumUsers($userName);
    	
    	if (empty($platinumUsers['user'])) {
    		$this->tpl->assign('CONTENT', 'Партнеров не найдено.');
    		return true;
    	}
    	
    	$partners = '
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/jquery.tree.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(\'ul.partners\').tree();
	});
</script>
    	';
    	
    	$partners .= '<div class="rzd_list2">';
    	
    	$partners .= '<ul class="partners">';
    	
    	foreach ($platinumUsers['user'] as $user) {
			$partners .= '<li>';
			
			$partners .= $user['surname'].' '.$user['name'].' ('.$this->getUserCountTicket($user['id']).')';
			
			$partners .= '</li>';
		}
		
		$partners .= '</ul>';
    	$partners .= '</div>';
    	
    	
    	$this->tpl->assign('CONTENT', $partners);
    }
    
    
    // выводит список дисков
    public function disks()
    {
    	/*if (!$this->_isAdmin()) {
    		$this->tpl->assign('CONTENT', 'В стадии разработки');
    		return true;
    	}
    	*/
    	$id = $this->getVar(2, null, $this->url);
    	
    	if (null === $id) {
    		return false;
    	}
    	
    	if (!ctype_digit($id) || $id < 1) {
    		return false;
    	}
    	
    	$user = $this->db->fetchRow("SELECT * FROM `users` WHERE `id` = '$id'");
    	
    	if (!$user) {
    		return false;
    	}
    	
    	if ($user['privilege'] == 'administrator') {
    		$this->logger->addLogRow('Попытка просмотра покупок Администратора', serialize($this->auth));
    		return false;
    	}
    	
    	$currentUser = $this->auth->surname.' '.$this->auth->name;
    	
    	if (!$this->_isAdmin()) {
    		if ($currentUser != $user['diamond'] && $currentUser != $user['emerald'] && $currentUser != $user['platinum']) {
    			return false;
    		}
    	}
    	
    	$select = $this->db->select();
		$select->from('order_disc', array('id', 'date', 'status', 'summ' => 'SUM(order_disc_items.summ)'));
		$select->joinLeft('order_disc_items', 'order_disc.id = order_disc_items.order_id', array());
		//$select->join('city', 'city.id = hall.city_id', array('city_name' => 'name'));
		//$select->join('event_types', 'events.type_id = event_types.id', array('event_type' => 'name'));
		//$select->join('sectors', 'hall.id = sectors.hall_id', array());
		//$select->join('tickets', 'tickets.event_id = events.id AND tickets.sector_id = sectors.id', array('ticket_id' => 'id', 'count', 'payment'));
		$select->where('order_disc.user_id = ?', array("".$user['id'].""));
		$select->group('order_disc.id');
		//$select->group('events.id');
		//echo $select->__toString();
		$purchases = $this->db->fetchAll($select);
		//print_r($purchases);
		$this->tpl->define_dynamic('discs', 'partner_discs.tpl');
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
				        $status = 'оплачено';
				        $statusClass = 'or';
				        break;
			        case 2:
				        $status = 'доставлено';
				        $statusClass = 'gr';
				        break;
			        default:
			            $status = 'доставлено';
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
		
		$this->tpl->parse('CONTENT', '.discs_order_list');
    	
    	return true;
    }
    
    public function viewdisk()
    {
    	if (!$this->_isAdmin()) {
    		$this->tpl->assign('CONTENT', 'В стадии разработки');
    		return true;
    	}
    	
    	$id = end($this->url);
        
        if (!ctype_digit($id) || $id < 1) {
            // Что-то не понятное в адресной строке
            return false;
        }
        
        $order = $this->db->fetchRow("SELECT * FROM `order_disc` WHERE `id` = '$id'");
        
        if (!$order) {
            return false;
        }
        
        $client = $this->db->fetchRow("SELECT * FROM `users` WHERE `id` = '".$order['user_id']."'");
        
        if (!$client) {
        	// Oooooops!
        	return false;
        }
        
        $currentUser = $this->auth->surname.' '.$this->auth->name;
        
        if ($client['diamond'] != $currentUser && $client['emerald'] != $currentUser && $client['platinum'] != $currentUser) {
        	if (!$this->_isAdmin()) {
            	return false;
        	}
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
                'PRINT_ORDER_INFO' => $order['info'],
                'PRINT_ORDER_TOTAL_SUMM' => $totalSumm,
                'ADMIN_AMWAY_TEXT' => '',
                'ADMIN_AMWAY_NUMBER' => ''
            )
        );
        
        return true;
    }
    
    
    
    private function showAdminList()
    {
    	$partners = '
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/jquery.tree.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(\'ul.partners\').tree();
	});
</script>
    	';
    	
    	$partners .= '<div class="rzd_list2">';
    	//$partners .= '<h2>Total Users: '.(sizeof($this->_diamond) + sizeof($this->_emerald) + sizeof($this->_platinum) + sizeof($this->_user)).'</h2>';
    	$partners .= '<ul class="partners">';
    	$partners .='<li style="display:none;"><ul><li></li></ul></li>';
    	foreach ($this->_diamond as $diamond) {
                
                $count_1=0;
    		$diamondUsers = $this->getDiamondUsers($diamond['surname'].' '.$diamond['name']);
    		$diamondCount = sizeof($diamondUsers['emerald']) +
    						sizeof($diamondUsers['platinum']) +
    						sizeof($diamondUsers['user']);
    	
             
			$partners .= '<li>';
/*    		
Выводит партнера верхнего уровня, заменено т.к. неверно ститает кол-во партнеров нижнего уровня
 **/ 		//	$partners .= '<img src="/img/icons/diamond.gif" width="11px" height="11px" /> '.$diamond['surname'].' '.$diamond['name'].' ('.$diamondCount.')'.$this->getUserStatLinks($diamond);
			

                        $partners .= '<img src="/img/icons/diamond.gif" width="11px" height="11px" /> '.$diamond['surname'].' '.$diamond['name'].' (diamond_count_new_eg) '.$this->getUserStatLinks($diamond);


                        //$partners .= '<img src="/img/icons/diamond.gif" width="11px" height="11px" /> '.(!empty($diamondUsers['emerald']) || !empty($diamondUsers['platinum']) || !empty($diamondUsers['user']) ? '<span class="open" style="cursor: pointer;">'.$diamond['surname'].' '.$diamond['name'].' ('.$diamondCount.')</span>' : $diamond['surname'].' '.$diamond['name'].' ('.$diamondCount.')');
    		
    		if (!empty($diamondUsers['emerald'])) {
    			$partners .= '<ul>';
    			
    			foreach ($diamondUsers['emerald'] as $emerald) {
                                $count_1++;
    				$emeraldUsers = $this->getEmeraldUsers($emerald['surname'].' '.$emerald['name']);
    				$emeraldCount = sizeof($emeraldUsers['platinum']) +
    								sizeof($emeraldUsers['user']);
    				
    				$partners .= '<li>';
    				
    				$partners .= '<img src="/img/icons/emerald.gif" width="11px" height="11px" /> '.$emerald['surname'].' '.$emerald['name'].' ('.$emeraldCount.')'.$this->getUserStatLinks($emerald);
    				
    				if (!empty($emeraldUsers['platinum'])) {
    					$partners .= '<ul>';
    					
    					foreach ($emeraldUsers['platinum'] as $platinum) {
                                                $count_1++;
    						$platinumUsers = $this->getPlatinumUsers($platinum['surname'].' '.$platinum['name']);
    						$platinumCount = sizeof($platinumUsers['user']);
    						
    						$partners .= '<li>';
    						
    						$partners .= '<img src="/img/icons/platinum.gif" width="11px" height="11px" /> '.$platinum['surname'].' '.$platinum['name'].' ('.$platinumCount.')'.$this->getUserStatLinks($platinum);
    						
    						if (!empty($platinumUsers['user'])) {
    							$partners .= '<ul>';
    							
    							foreach ($platinumUsers['user'] as $user) {
                                                                $count_1++;
    								$partners .= '<li>';
    								
    								$partners .= $user['surname'].' '.$user['name'].$this->getUserStatLinks($user);
    								
    								$partners .= '</li>';
    							}
    							
    							$partners .= '</ul>';
    						}
    						
    						$partners .= '</li>';
    					}
    					
    					$partners .= '</ul>';
    				}
    				
    				if (!empty($emeraldUsers['user'])) {
    					$partners .= '<ul>';
    					
    					foreach ($emeraldUsers['user'] as $user) {
                                                $count_1++;
    						$partners .= '<li>';
    						
    						$partners .= $user['surname'].' '.$user['name'].$this->getUserStatLinks($user);
    						
    						$partners .= '</li>';
    					}
    					
    					$partners .= '</ul>';
    				}
    				
    				$partners .= '</li>';
    			}
    			
    			$partners .= '</ul>';
    		}
    		
    		if (!empty($diamondUsers['platinum'])) {
                   // echo '';
    			$partners .= '<ul>';
                    
    			
    			foreach ($diamondUsers['platinum'] as $platinum) {
                                $count_1++;
    				$platinumUsers = $this->getPlatinumUsers($platinum['surname'].' '.$platinum['name']);
    				$platinumCount = sizeof($platinumUsers['user']);
    				
    				$partners .= '<li>';
    				
    				$partners .= '<img src="/img/icons/platinum.gif" width="11px" height="11px" /> '.$platinum['surname'].' '.$platinum['name'].' ('.$platinumCount.')'.$this->getUserStatLinks($platinum);
    				
    				if (!empty($platinumUsers['user'])) {
    					$partners .= '<ul>';
                                    //echo '';
    					
    					foreach ($platinumUsers['user'] as $user) {
                                                $count_1++;
    						$partners .= '<li>';
    						
    						$partners .= $user['surname'].' '.$user['name'].$this->getUserStatLinks($user);
    						
    						$partners .= '</li>';
    					}
    					
    					$partners .= '</ul>';
    				}
    				
    				$partners .= '</li>';
                                //$partners .= '<li>'.$count_1.'</li>';
    			}
    			
    			$partners .= '</ul>';
    		}
    		
    		if (!empty($diamondUsers['user'])) {
                    
    			$partners .= '<ul>';
    			
    			foreach ($diamondUsers['user'] as $user) {
                                $count_1++;
    				$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
    				
    				$partners .= $user['surname'].' '.$user['name'].$this->getUserStatLinks($user);
    				
    				$partners .= '</li>';
    			}
    			
    			$partners .= '</ul>';
    		}
    		
    		$partners .= '</li>';
                $partners = str_replace ('diamond_count_new_eg', $count_1, $partners);
                //$count_1=0;
                //echo $count_1.'<br>';
    	}
    	
    	$partners .= '</ul>';
    	$partners .= '</div>';
    	
    	$this->tpl->assign('CONTENT', $partners);
    }
    
    
    
    private function showDiamondList()
    {
        
    	// Release data
    	$userName = $this->auth->surname.' '.$this->auth->name;
    	
    	// Development data
    	#$userName = 'Сапрыкина Галина';
    	#$userName = 'Царук Лариса';
    	#$userName = 'Проскура Александра';
    	#$userName = 'Ляшенко Михаил';
    	#$userName = 'Солошенко Татьяна';
    	#$userName = 'Ким Олег';
    	#$userName = 'Ена Наталья';
    	#$userName = 'Демичева Светлана';
    	#$userName = 'Уразакаев Максим';
    	
    	$diamondUsers = $this->getDiamondUsers($userName);
    	
    	if (empty($diamondUsers['emerald']) && empty($diamondUsers['platinum']) && empty($diamondUsers['user'])) {
    		$this->tpl->assign('CONTENT', 'Партнеров не найдено.');
    		return true;
    	}
    	
    	$partners = '
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/jquery.tree.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(\'ul.partners\').tree();
	});
</script>
    	';
    	
    	$partners .= '<div class="rzd_list2">';
    	
    	$partners .= '<ul class="partners">';
    	
    	foreach ($diamondUsers['emerald'] as $emerald) {
                        $li_1=0;
			$emeraldUsers = $this->getEmeraldUsers($emerald['surname'].' '.$emerald['name']);
			$emeraldCount = sizeof($emeraldUsers['platinum']) +
							sizeof($emeraldUsers['user']);
			
			$partners .= '<li>';
			
			$partners .= '<img src="/img/icons/emerald.gif" width="11px" height="11px" /> '.$emerald['surname'].' '.$emerald['name'].' ('.$emeraldCount.')'.$this->getUserStatLinks($emerald);
			
			if (!empty($emeraldUsers['platinum'])) {
				$partners .= '<ul>';
				
				foreach ($emeraldUsers['platinum'] as $platinum) {
					$platinumUsers = $this->getPlatinumUsers($platinum['surname'].' '.$platinum['name']);
					$platinumCount = sizeof($platinumUsers['user']);
					
					$partners .= '<li>';
					
					$partners .= '<img src="/img/icons/platinum.gif" width="11px" height="11px" /> '.$platinum['surname'].' '.$platinum['name'].' ('.$platinumCount.')'.$this->getUserStatLinks($platinum);
					$li_1 = $li_1 + $this->getUserStatLinks($platinum);
					if (!empty($platinumUsers['user'])) {
						$partners .= '<ul>';
						
						foreach ($platinumUsers['user'] as $user) {
							$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
							
							$partners .= $user['surname'].' '.$user['name'].$this->getUserStatLinks($user);
                                                        $li_1 = $li_1 + $this->getUserStatLinks($user);
							
							$partners .= '</li>';
						}
						
						$partners .= '</ul>';
					}
					
					$partners .= '</li>';
				}
				
				$partners .= '</ul>';
			}
			
			if (!empty($emeraldUsers['user'])) {
				$partners .= '<ul>';
				
				foreach ($emeraldUsers['user'] as $user) {
					$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
					
					$partners .= $user['surname'].' '.$user['name'].$this->getUserStatLinks($user);
                                        $li_1 = $li_1 + $this->getUserStatLinks($user);
					
					$partners .= '</li>';
				}
				
				$partners .= '</ul>';
			}
			
			$partners .= '</li>';
                        
		}
		
		foreach ($diamondUsers['platinum'] as $platinum) {
			$platinumUsers = $this->getPlatinumUsers($platinum['surname'].' '.$platinum['name']);
			$platinumCount = sizeof($platinumUsers['user']);
			
			$partners .= '<li>';
			
			$partners .= '<img src="/img/icons/platinum.gif" width="11px" height="11px" /> '.$platinum['surname'].' '.$platinum['name'].' ('.$platinumCount.')';
			
			if (!empty($platinumUsers['user'])) {
				$partners .= '<ul>';
				
				foreach ($platinumUsers['user'] as $user) {
					$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
					
					$partners .= $user['surname'].' '.$user['name'].$this->getUserStatLinks($user);
					
					$partners .= '</li>';
				}
				
				$partners .= '</ul>';
			}
			
			$partners .= '</li>';
		}
		
		foreach ($diamondUsers['user'] as $user) {
			$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
			
			$partners .= $user['surname'].' '.$user['name'].$this->getUserStatLinks($user);
			
			$partners .= '</li>';
		}
    	
    	$partners .= '</ul>';
    	$partners .= '</div>';
    	
    	
    	$this->tpl->assign('CONTENT', $partners);
    }
    
    private function showEmeraldList()
    {
        
    	// Release data
    	$userName = $this->auth->surname.' '.$this->auth->name;
    	
    	// Development data
    	#$userName = 'Курочка Наталья';
    	#$userName = 'Ена Ольга'; #CHECK THIS
    	#$userName = 'Переход Виктор'; #CHECK THIS
    	#$userName = 'Кардаш Олег';
    	#$userName = 'Плеханова Галина'; #CHECK THIS
    	#$userName = 'Волошко Наталья'; #CHECK THIS
    	#$userName = 'Коряк Петр';
    	#$userName = 'Астахов Денис'; #CHECK THIS
    	#$userName = 'Аболина Лариса'; #CHECK THIS
    	#$userName = 'Онда Галина'; #CHECK THIS
    	#$userName = 'Шелест Юрий'; #CHECK THIS
    	#$userName = 'Клименко Александр'; #CHECK THIS
    	#$userName = 'Левченко Сергей'; #CHECK THIS
    	#$userName = 'Бундюк Игорь'; #CHECK THIS
    	#$userName = 'Троицкий Николай';
    	#$userName = 'Улиганич Йосиф'; #CHECK THIS
    	#$userName = 'Бескорованная Людмила'; #CHECK THIS
    	#$userName = 'Калинченков Вячеслав'; #CHECK THIS
    	#$userName = 'Калинченков Вячеслав'; Калинченков Вячеслав Вячеслав !!!!!!!!!!!!!!!!!!
    	#$userName = 'Старцева Лиена';
    	#$userName = 'Болоцкий Николай'; #CHECK THIS
    	#$userName = 'Жансеитова Венера';
    	
    	$emeraldUsers = $this->getEmeraldUsers($userName);
    	
    	if (empty($emeraldUsers['platinum']) && empty($emeraldUsers['user'])) {
    		$this->tpl->assign('CONTENT', 'Партнеров не найдено.');
    		return true;
    	}
    	
    	$partners = '
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/jquery.tree.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(\'ul.partners\').tree();
	});
</script>
    	';
    	
    	$partners .= '<div class="rzd_list2">';
    	
    	$partners .= '<ul class="partners">';
    	
    	foreach ($emeraldUsers['platinum'] as $platinum) {
			$platinumUsers = $this->getPlatinumUsers($platinum['surname'].' '.$platinum['name']);
			$platinumCount = sizeof($platinumUsers['user']);
			
			$partners .= '<li>';
			
			$partners .= '<img src="/img/icons/platinum.gif" width="11px" height="11px" /> '.$platinum['surname'].' '.$platinum['name'].' ('.$platinumCount.')';
			
			if (!empty($platinumUsers['user'])) {
				$partners .= '<ul>';
				
				foreach ($platinumUsers['user'] as $user) {
					$partners .= '<li>';
					
					$partners .= $user['surname'].' '.$user['name'].' ('.$userCount.')'.$this->getUserStatLinks($user);
					
					$partners .= '</li>';
				}
				
				$partners .= '</ul>';
			}
			
			$partners .= '</li>';
		}
		
		foreach ($emeraldUsers['user'] as $user) {
			$partners .= '<li><img src="/img/icons/no-img.png" width="11px" height="11px" /> ';
			
			$partners .= $user['surname'];
			
			$partners .= '</li>';
		}
    	
    	$partners .= '</ul>';
    	$partners .= '</div>';
    	
    	
    	$this->tpl->assign('CONTENT', $partners);
    }
    
    private function showPlatinumList()
    {
    	// Release data
    	$userName = $this->auth->surname.' '.$this->auth->name;
    	
    	// Development data
    	#$userName = 'Курочка Наталья';
    	
    	$platinumUsers = $this->getPlatinumUsers($userName);
    	
    	if (empty($platinumUsers['user'])) {
    		$this->tpl->assign('CONTENT', 'Партнеров не найдено.');
    		return true;
    	}
    	
    	$partners = '
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/js/jquery.tree.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(\'ul.partners\').tree();
	});
</script>
    	';
    	
    	$partners .= '<div class="rzd_list2">';
    	
    	$partners .= '<ul class="partners">';
    	
    	foreach ($platinumUsers['user'] as $user) {
			$partners .= '<li>';
			
			$partners .= $user['surname'].' '.$user['name'];
			
			$partners .= '</li>';
		}
		
		$partners .= '</ul>';
    	$partners .= '</div>';
    	
    	
    	$this->tpl->assign('CONTENT', $partners);
    }
    
    
    private function getDiamondUsers($diamond)
    {
        
    	$return['emerald'] = array();
    	$return['platinum'] = array();
    	$return['user'] = array();
    	
    	foreach ($this->_emerald as $emerald) {
    		if ($diamond == $emerald['diamond']) {
    			$return['emerald'][] = $emerald;
    		}
    	}
    	
    	foreach ($this->_platinum as $platinum) {
    		if ($diamond == $platinum['emerald'] && $diamond == $platinum['diamond']) {
    			$return['platinum'][] = $platinum;
    		}
    	}
    	
    	foreach ($this->_user as $user) {
    		if ($diamond == $user['platinum'] && $diamond == $user['emerald'] && $diamond == $user['diamond']) {
    			$return['user'][] = $user;
    		}
    	}
    	
    	return $return;
    }
    
    private function getEmeraldUsers($emerald)
    {
    	$return['platinum'] = array();
    	$return['user'] = array();
    	
    	foreach ($this->_platinum as $platinum) {
    		if ($emerald == $platinum['emerald']) {
    			$return['platinum'][] = $platinum;
    		}
    	}
    	
    	foreach ($this->_user as $user) {
    		if ($emerald == $user['platinum'] && $emerald == $user['emerald']) {
    			$return['user'][] = $user;
    		}
    	}
    	
    	return $return;
    }
    
    private function getPlatinumUsers($platinum)
    {
    	$return['user'] = array();
    	
    	foreach ($this->_user as $user) {
    		if ($platinum == $user['platinum']) {
    			$return['user'][] = $user;
    		}
    	}
    	
    	return $return;
    }
    
    
    
    private function loadUser($diamond = true, $emerald = true, $platinum = true, $user = true)
    {
    	if ($diamond) {
    		$this->_diamond = $this->db->fetchAll(
    			"SELECT * FROM `users` WHERE `type` = 'diamond' AND `privilege` != 'administrator'"
			);
    	}
    	
    	if ($emerald) {
    		$this->_emerald = $this->db->fetchAll(
    			"SELECT * FROM `users` WHERE `type` = 'emerald' AND `privilege` != 'administrator'"
			);
    	}
    	
    	if ($platinum) {
    		$this->_platinum = $this->db->fetchAll(
    			"SELECT * FROM `users` WHERE `type` = 'platinum' AND `privilege` != 'administrator'"
			);
    	}
    	
    	if ($user) {
    		$this->_user = $this->db->fetchAll(
    			"SELECT * FROM `users` WHERE `type` = 'user' AND `privilege` != 'administrator'"
			);
    	}
    }
    
    private function loadEvent($id)
    {
    	$this->_event_user = $this->db->fetchAll("SELECT * FROM `tickets` WHERE `event_id` = '$id'");
    }
    
    private function getUserStatLinks($user)
    {
    	$tickets = '&nbsp;&nbsp;<a href="/partners/tickets/'.$user['id'].'" title="Купленные билеты"><img src="/img/icons/ticket.png" width="16px" height="16px" alt="Купленные билеты" title="Купленные билеты" /></a>';
    	$disks = '&nbsp;&nbsp;<a href="/partners/disks/'.$user['id'].'" title="Купленные диски"><img src="/img/icons/cd.png" width="16px" height="16px" alt="Купленные диски" title="Купленные диски"></a>';
    	
    	return $tickets.$disks;
    }
    
    private function getUserCountTicket($id)
    {
    	$count = 0;
    	
    	if (empty($this->_event_user)) {
    		return $count;
    	}
    	
    	foreach ($this->_event_user as $user) {
    		if ($user['user_id'] == $id) {
    			$count += $user['count'];
    		}
    	}
    	
    	return $count;
    }
    
    // подсчет купленых билетов
    private function getInnerUserCountTicket ($id_user)
    {
        $count = 0;
        $id = $this->getVar(2, null, $this->url);
        $ticket = $this->db->fetchAll("SELECT SUM(`count`) FROM `tickets` WHERE (`user_id` = '$id_user' AND `event_id`='$id')");
        //echo "SELECT * FROM `tickets` WHERE (`user_id` = '$id_user' AND `ievent_idd`='$id')<br>";
        //return $id_user;//$ticket['count'];
        //var_dump ($ticket); echo '<br>';
        if ($ticket)
        {
            return $ticket[0]['SUM(`count`)'];
        }
        else
            return '(vs)'.$id_user.' - '.$id;
        
    }
    


    private function printGoodPartners($no='admin', $type='admin', $sep=0 ,$ul=1){
        
    }
    
    private function printDiamondPartners($no){
        
        if ($this->_isAdmin()) $w="`type` = 'diamond'";
        else $w="`number` = '$no'";

        $diamond = $this->db->fetchAll("SELECT * FROM `users` WHERE ($w) ORDER BY `surname`");
        
        $this -> _print .= '<ul class="partners">';
        
        foreach ($diamond as $user) {
            //echo $this->c.'<br>';
            //str_replace($search, $replace, $subject)
            $this -> _print = str_replace('zamenaD',$this->c,$this -> _print);
            $this->c=0;

            
            //$this -> countUser($user['number']);

            $this -> _print .='<li><img src="/img/icons/diamond.gif" width="11px" height="11px" />'.$user['surname'].' '.$user['name'].' (zamenaD)'.$this->getUserStatLinks($user);

            $this -> _print .= '<ul>';

            $this -> printEmeraldPartners($user['number']);

            $this -> printPlatinumPartners($user['number']);
            
            $this -> printUsersPartners($user['number']);
            
            $this -> _print .='</li>';
        
            $this -> _print .= '</ul>';
            
        }
        
        $this -> _print .= '</ul>';
    }

    
    
    
/*
 * 
 * 
 * 
 */    
    private function printEmeraldPartners($no){

        $emerald = $this->db->fetchAll("SELECT * FROM `users` WHERE (`type` = 'emerald' and `top_user_no`='$no') ORDER BY `surname`");
        if ($emerald){
                                    
            //$this -> _print .= '<ul>';
            foreach ($emerald as $user) {
                $this -> _print = str_replace('zamenaE',$this->e,$this -> _print);
                $this->e=0;
                $this->c++;
                
                
                $color="";
                if ($this->id_e!=0){
                    $eventUsers = $this->db->fetchRow("SELECT * FROM `tickets` WHERE (`event_id` = '$this->id_e' and `user_id`='".$user['id']."')");
                    if (isset($eventUsers['id'])){
                        $color="style='color:#FF8000;'";
                    }
                }
                
                $this -> _print .='<li '.$color.'><img src="/img/icons/emerald.gif" width="11px" height="11px" />'.$user['surname'].' '.$user['name'].' (zamenaE)'.$this->getUserStatLinks($user);
                $this -> printPlatinumPartners($user['number'],1);
                $this -> printUsersPartners($user['number'],1);
                $this -> _print .= '</li>';
            }
            //$this -> _print .= '</ul>';
        }
    }

    private function printPlatinumPartners($no, $ul=0){
        
        $platinum = $this->db->fetchAll("SELECT * FROM `users` WHERE (`type` = 'platinum' and `top_user_no`='$no') ORDER BY `surname`");
        if ($platinum){
            if ($ul==1)
                $this -> _print .= '<ul>';
            foreach ($platinum as $user) {
                if ($ul==1)
                    $this->e++;
                $color="";
                if ($this->id_e!=0){
                    $eventUsers = $this->db->fetchRow("SELECT * FROM `tickets` WHERE (`event_id` = '$this->id_e' and `user_id`='".$user['id']."')");
                    if (isset($eventUsers['id'])){
                        $color="style='color:#FF8000;'";
                    }
                }
                $this->c++;
                $this -> _print .= '<li '.$color.'><img src="/img/icons/platinum.gif" width="11px" height="11px" />'.$user['surname'].' '.$user['name'].' ('.$this -> countUserP($user['number']).')'.$this->getUserStatLinks($user);
                $this -> printUsersPartners($user['number'],1);
                $this -> _print .= '</li>';
            }
            if ($ul==1)
                $this -> _print .= '</ul>';
        }
    }
    private function printUsersPartners($no, $ul=0){
        
        $users = $this->db->fetchAll("SELECT * FROM `users` WHERE (`type` = 'user' and `top_user_no`='$no') ORDER BY `surname`");
        if ($users){
            if ($ul==1)
                $this -> _print .= '<ul>';
            foreach ($users as $user) {
                
                if ($ul==1)
                    $this->e++;
                $color="";
                if ($this->id_e!=0){
                    $eventUsers = $this->db->fetchRow("SELECT * FROM `tickets` WHERE (`event_id` = '$this->id_e' and `user_id`='".$user['id']."')");
                    if (isset($eventUsers['id'])){
                        $color="style='color:#FF8000;'";
                    }
                }
                    
                $this->c++;
                $this -> _print .='<li '.$color.'><img src="/img/icons/no-img.png" width="11px" height="11px" />'.$user['surname'].' '.$user['name'].$this->getUserStatLinks($user).'</li>';
            }
            if ($ul==1)
                $this -> _print .= '</ul>';
        }
    }

    
    
    
    
    
  
    
    
    
    
    
    

    private function countUser($no){
        //$f = $this->db->fetchOne("SELECT count(*) FROM `users` WHERE ((`up` = '$no' or `upup` = '$no' or `upupup` = '$no') and `type`='platinum' AND `number`<>'$no')");
        $f = $this->db->fetchOne("SELECT count(*) FROM `users` WHERE (`upup` = '$no' or `upupup` = '$no') and `number`<>'$no'");
        return $f;
    }
    private function countUserP($no){
        //$f = $this->db->fetchOne("SELECT count(*) FROM `users` WHERE ((`up` = '$no' or `upup` = '$no' or `upupup` = '$no') and `type`='platinum' AND `number`<>'$no')");
        $f = $this->db->fetchOne("SELECT count(*) FROM `users` WHERE `top_user_no` = '$no'");
        return $f;
    }
    

}