<?php

class My_Pdf
{
	
	private $pdf = null;
	
	private $pages = array();
	
	private $currentPage = 0;
	
	private $pagesCoord = array();
	
	private $currentBlock = 1;
	
	public function __construct($tickets = null, $scannerCode)
	{
		$this->pdf = new Zend_Pdf();
		
		$this->loadPagesCoord();
		
		$this->pages[] = array();
		$this->newPage();
		$s=0;
		//foreach ($scannerCode as $code) {
                    foreach($tickets as $ticket){
                        
                        //$code=$scannerCode[$s];
                        //$scannerCode = $this->db->fetchAll("SELECT * FROM `tickets_code` WHERE `ticket_id` = '".$ticket['id']."'");
		//for ($i=0; $i<sizeof($scannerCode); $i++) {
			$this->drawTicketBlock();
			
			if ($ticket['payment'] != '1') {
				$this->drawUnpaidImage();
			}
			//$code['code']='123';
                        
                        //var_dump($scannerCode); die;
                        //die;
                        $code=$scannerCode[$s];
                        $s++;
			$this->drawStaticText();
			$this->drawEventLogo($ticket['pic']);
			$this->drawEventName($ticket['event_name']);
			$this->drawUserSurname($ticket['surname']);
			$this->drawUserName($ticket['user_name']);
			$this->drawUserNumber($ticket['number']);
			$this->drawEventDate($ticket['event_date']);
			$this->drawEventCity($ticket['city_name']);
			$this->drawEventAdres($ticket['adres']);
			$this->drawEventSector($ticket['sector_name']);
                        $this->drawEventRow($ticket['row']);
                        $this->drawEventLoc($ticket['location']);
			$this->drawEventType($ticket['event_type']);
			//$this->drawPaymentStatus($ticket['payment']);
			$this->drawBarCode($code);
			
			$this->currentBlock++;
			
			if ($this->currentBlock > 5) {
				$this->newPage();
				$this->currentBlock = 1;
			}
                        
                    }
		//}
		
		unset($this->pages[0]);
		//exit();
		$this->pdf->pages = $this->pages;
		
	}
	
	public function __toString()
	{
		return $this->pdf->render();
	}
	
	private function loadPagesCoord()
	{
		$data[1] = array('x' => 30, 'y' => 820);
		$data[2] = array('x' => 30, 'y' => 658);
		$data[3] = array('x' => 30, 'y' => 496);
		$data[4] = array('x' => 30, 'y' => 334);
		$data[5] = array('x' => 30, 'y' => 172);
		
		$this->pagesCoord = $data;
	}
	
	private function newPage()
	{
		$this->pages[] = $this->pdf->newPage(Zend_Pdf_Page::SIZE_A4);
		
		$this->currentPage++;
	}
	
	private function drawTicketBlock()
	{
		$x1 = $this->pagesCoord[$this->currentBlock]['x'];
		$y1 = $this->pagesCoord[$this->currentBlock]['y'];
		$x2 = $this->pagesCoord[$this->currentBlock]['x'] + 536.2;
		$y2 = $this->pagesCoord[$this->currentBlock]['y'] - 147.4;
		$fill_type = Zend_Pdf_Page::SHAPE_DRAW_STROKE;
		
		$this->pages[$this->currentPage]->drawRectangle($x1, $y1, $x2, $y2, $fill_type);
	}
	
	private function drawUnpaidImage()
	{
		$image = Zend_Pdf_Image::imageWithPath('./img/unpaid.jpg');
		
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 227.5;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 138.5;
		$x2 = $this->pagesCoord[$this->currentBlock]['x'] + 423.1;
		$y2 = $this->pagesCoord[$this->currentBlock]['y'] - 11.5;
		
        $this->pages[$this->currentPage]->drawImage($image, $x1, $y1, $x2, $y2);
	}
	
	private function drawStaticText()
	{
                //$this->pagesCoord[$this->currentBlock]['y']=$this->pagesCoord[$this->currentBlock]['y']-20;
		// Text #1
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 110.4;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 100.3;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/TAHOMA.TTF');
		$this->pages[$this->currentPage]->setFont($font, 8);
		
		$this->pages[$this->currentPage]->drawText('Номер Amway:', $x1, $y1, 'CP1251');
		
		// Text #2
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 267.6;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 65.8;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/TAHOMA.TTF');
		$this->pages[$this->currentPage]->setFont($font, 6);
		//$y1=$y1-20;
                //$x1=$x1-20;
                
		$this->pages[$this->currentPage]->drawText('Дата:', $x1, $y1+20, 'CP1251');
		
		
                // Text #3
		//$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 74.6;
                $y1 = $this->pagesCoord[$this->currentBlock]['y'] - 83.8;
		$this->pages[$this->currentPage]->drawText('Город:', $x1, $y1+20, 'CP1251');
		
		// Text #4
		//$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 83.8;
                $y1 = $this->pagesCoord[$this->currentBlock]['y'] - 92.3;
		$this->pages[$this->currentPage]->drawText('Адрес:', $x1, $y1+20, 'CP1251');
		
		// Text #5
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 130;
		$this->pages[$this->currentPage]->drawText('Сектор:', $x1, $y1+20, 'CP1251');
		// Text #5 1
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 140;
		$this->pages[$this->currentPage]->drawText('Ряд:', $x1, $y1+20, 'CP1251');
		// Text #5 2
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 150;
		$this->pages[$this->currentPage]->drawText('Место:', $x1, $y1+20, 'CP1251');

                
		// Text #6 110.6
		//$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 129.1;
                $y1 = $this->pagesCoord[$this->currentBlock]['y'] - 74.6;
		$this->pages[$this->currentPage]->drawText('Время начала:', $x1, $y1+20, 'CP1251');
		
		// Text #7 119.8
		//$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 138.3;
                $y1 = $this->pagesCoord[$this->currentBlock]['y'] - 110.7;
		$this->pages[$this->currentPage]->drawText('Вид мероприятия:', $x1, $y1+20, 'CP1251');
		
		// Text #8 128.6
		//$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 147.5;
		//$this->pages[$this->currentPage]->drawText('Статус оплаты:', $x1, $y1+20, 'CP1251');
	}
	
	private function drawEventLogo($pic)
	{
		$image = Zend_Pdf_Image::imageWithPath('./images/events/big/'.$pic);
		
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 8.9;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 132.8;
		$x2 = $this->pagesCoord[$this->currentBlock]['x'] + 102.7;
		$y2 = $this->pagesCoord[$this->currentBlock]['y'] - 23.8;
		
        $this->pages[$this->currentPage]->drawImage($image, $x1, $y1, $x2, $y2);
	}
	
	private function drawEventName($name)
	{
		$name = trim($name);
		
		$name = wordwrap($name, 47, '%%%');
		$name = explode('%%%', $name);
		
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 110.4;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 16.8;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/georgiaz.TTF');
		$this->pages[$this->currentPage]->setFont($font, 10);
		
		if (sizeof($name) == 1) {
			$this->pages[$this->currentPage]->drawText($name[0], $x1, $y1, 'CP1251');
		} elseif (sizeof($name) > 1) {
			for ($i=0; $i<sizeof($name); $i++) {
				if ($i > 2) {
					break;
				}
				
				if ($i > 0) {
					$y1 = $y1 - 12;
				}
				
				$this->pages[$this->currentPage]->drawText($name[$i], $x1, $y1, 'CP1251');
			}
		} else {
			return;
		}
	}
	
	private function drawUserSurname($surname)
	{
		$surname = trim($surname);
		
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 110.4;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 62.9;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/tahomabd.TTF');
		$this->pages[$this->currentPage]->setFont($font, 9.57);
		
		$this->pages[$this->currentPage]->drawText($surname, $x1, $y1, 'CP1251');
	}
	
	private function drawUserName($name)
	{
		$name = trim($name);
		
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 110.4;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 75.1;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/TAHOMA.TTF');
		$this->pages[$this->currentPage]->setFont($font, 8);
		
		$this->pages[$this->currentPage]->drawText($name, $x1, $y1, 'CP1251');
	}
	
	private function drawUserNumber($number)
	{
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 110.4;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 111.8;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/tahomabd.TTF');
		$this->pages[$this->currentPage]->setFont($font, 8);
		
		$this->pages[$this->currentPage]->drawText($number, $x1, $y1, 'CP1251');
	}
	//Дата на билете
	private function drawEventDate($date)
	{
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 332.9;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 65.8+20;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/tahomabd.TTF');
		$this->pages[$this->currentPage]->setFont($font, 8);
		
		$locale = new Zend_Locale('ru_RU');
        Zend_Date::setOptions(array('format_type' => 'php'));
        
        $d = new Zend_Date($date, false, $locale);
		
		$this->pages[$this->currentPage]->drawText($d->toString('d.m.Y'), $x1, $y1, 'CP1251');
		
		// Event TIME
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 74.6;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/tahomabd.TTF');
		$this->pages[$this->currentPage]->setFont($font, 6);
		
		$this->pages[$this->currentPage]->drawText($d->toString('H:i'), $x1, $y1+20, 'CP1251');
	}
	//Город
	private function drawEventCity($city)
	{
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 332.9;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 83.8+20;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/tahomabd.TTF');
		$this->pages[$this->currentPage]->setFont($font, 6);
		
		$this->pages[$this->currentPage]->drawText($city, $x1, $y1, 'CP1251');
	}
	
	private function drawEventAdres($adres)
	{
		$adres = wordwrap($adres, 25, '%%%');
		$adres = explode('%%%', $adres);
		
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 332.9;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 92.3+20;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/tahomabd.TTF');
		$this->pages[$this->currentPage]->setFont($font, 6);
		
		if (sizeof($adres) == 1) {
			$this->pages[$this->currentPage]->drawText($adres[0], $x1, $y1, 'CP1251');
		} elseif (sizeof($adres) > 1) {
			for ($i=0; $i<sizeof($adres); $i++) {
				if ($i > 1) {
					break;
				}
				
				if ($i > 0) {
					$y1 = $y1 - 8;
				}
				
				$this->pages[$this->currentPage]->drawText($adres[$i], $x1, $y1, 'CP1251');
			}
		} else {
			return;
		}
	}
	
	private function drawEventSector($sector)
	{
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 332.9;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 130+20;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/tahomabd.TTF');
		$this->pages[$this->currentPage]->setFont($font, 8);
		
		$this->pages[$this->currentPage]->drawText($sector, $x1, $y1, 'CP1251');
	}

        	private function drawEventRow($sector)
	{
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 332.9;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 140+20;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/tahomabd.TTF');
		$this->pages[$this->currentPage]->setFont($font, 8);
		
		$this->pages[$this->currentPage]->drawText($sector, $x1, $y1, 'CP1251');
	}
	private function drawEventLoc($sector)
	{
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 332.9;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 150+20;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/tahomabd.TTF');
		$this->pages[$this->currentPage]->setFont($font, 8);
		
		$this->pages[$this->currentPage]->drawText($sector, $x1, $y1, 'CP1251');
	}

        
	private function drawEventType($type)
	{
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 332.9;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 110.7+20;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/tahomabd.TTF');
		$this->pages[$this->currentPage]->setFont($font, 6);
		
		$this->pages[$this->currentPage]->drawText($type, $x1, $y1, 'CP1251');
	}
	
	private function drawPaymentStatus($status = 0)
	{
		$status = (int) $status;
		
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 332.9;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 128.6;
		
		$font = Zend_Pdf_Font::fontWithPath('fonts/tahomabd.TTF');
		if ($status == 1) {
			$this->pages[$this->currentPage]->setFont($font, 6);
		} else {
			$this->pages[$this->currentPage]->setFont($font, 8);
		}
		
		$this->pages[$this->currentPage]->drawText(($status == 1 ? 'оплачен' : 'НЕ ОПЛАЧЕН'), $x1, $y1, 'CP1251');
	}
	
	private function drawBarCode($code)
	{
		//$locale = new Zend_Locale('ru_UA');
        //Zend_Date::setOptions(array('format_type' => 'php'));
        
        //$d = new Zend_Date($date, false, $locale);
        
		//$code = (int) $d->toString('dmy').$number;
		//echo $code.'<br />';
		$renderer = new Zend_Barcode_Renderer_Image();
		$renderer->setImageType('jpg');
		$renderer->setHorizontalPosition('right');
		$renderer->setVerticalPosition('middle');
		
		$options = array('text' => $code, 'barHeight' => 318);
		
		$barcode = new Zend_Barcode_Object_Ean13();
		$barcode->setBarHeight(318);
		//$barcode->setBarThickWidth(100);
		$barcode->setBarThinWidth(3);
		$barcode->setText($code);
		$barcode->setOrientation(270);
		$barcode->setFont('fonts/tahomabd.TTF');
		$barcode->setFontSize(22);
		//$imageResource = $barcode->draw();
		//var_dump($imageResource);
		$renderer->setBarcode($barcode);
		
		@imagejpeg($renderer->draw(), './images/tickets/'.$code.'.jpg', 100);
		@chmod('./images/tickets/'.$code.'.jpg', 777);
		
		$image = Zend_Pdf_Image::imageWithPath('./images/tickets/'.$code.'.jpg');
		
		$x1 = $this->pagesCoord[$this->currentBlock]['x'] + 447.6;
		$y1 = $this->pagesCoord[$this->currentBlock]['y'] - 116.6;
		$x2 = $this->pagesCoord[$this->currentBlock]['x'] + 529.7;
		$y2 = $this->pagesCoord[$this->currentBlock]['y'] - 33.8;
		
        $this->pages[$this->currentPage]->drawImage($image, $x1, $y1, $x2, $y2);
        
        @unlink('./images/tickets/'.$code.'.jpg');
		//var_dump($renderer->draw());
		//exit();
		//$image = Zend_Pdf_Image::
		
		/*Zend_Barcode::render(
		    'ean13',
		    'image',
		    array(
		        'text' => '999999999999',
		        'font' => 3,
		        'imagetype' => 'jpeg'
		    )
		);*/
	}
	
}