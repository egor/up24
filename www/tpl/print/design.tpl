<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Index</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<link rel="stylesheet" type="text/css" href="/css/style.css" /> 
<!--[if lte IE 7]><link rel="stylesheet" href="/css/ie.css" type="text/css" /><![endif]-->
<link rel="shortcut icon" href="http://{SITE0}/favicon.ico" type='image/x-icon' />
</head>
<body>
              
<div class="wraper_print">
    
 <table class="info">
     
  <tr>
   <td class="noborder" colspan="2"><img src="/img{FOLDER}/diamond2.gif" width="358" height="93" alt="" /></td>
   <td class="right noborder"><span>����� ������: &nbsp; <span>{PRINT_ORDER_NUMBER}</span></span><br /><br /><span>���� ������:</span> &nbsp; <strong>{PRINT_ORDER_DATE}</strong></td>
  </tr>
  <tr>
      <td colspan="2"  style="width:882px; overflow: hidden;">
          <table class="inf">
              <tr><td class="noborder">������:</td><td class="noborder">{PRINT_ORDER_ZIP}</td></tr>
              <tr><td class="noborder">�����:</td><td class="noborder">{PRINT_ORDER_CITY}</td></tr>
              <tr><td class="noborder">�����:</td><td class="noborder">{PRINT_ORDER_ADRES}</td></tr>
              <tr><td class="noborder">��� ����������:</td><td class="noborder">{PRINT_ORDER_FIO}</td></tr>
              <tr><td class="noborder" >���������� ������:</td><td class="noborder" >{PRINT_ORDER_PASSPORT}</td></tr>
              <tr><td class="noborder">�������:</td><td class="noborder">{PRINT_ORDER_PHONE}</td></tr>
              <tr><td class="noborder">E-mail:</td><td class="noborder">{PRINT_ORDER_EMAIL}</td></tr>
              <tr><td class="noborder">{ADMIN_AMWAY_TEXT}</td><td class="noborder">{ADMIN_AMWAY_NUMBER}</td></tr>
              
          </table>
      </td>
      <td>
          �������������� ����������:<br /><span>{PRINT_ORDER_INFO}</span>
      </td>
   <!--<td>������:<br />�����:<br />�����:<br />��� ����������:<br />���������� ������:<br />�������:<br />E-mail:{ADMIN_AMWAY_TEXT}</td>
   <td>{PRINT_ORDER_ZIP}<br />{PRINT_ORDER_CITY}<br />{PRINT_ORDER_ADRES}<br />{PRINT_ORDER_FIO}<br /><p style="width:100px;">{PRINT_ORDER_PASSPORT}</p><br />{PRINT_ORDER_PHONE}<br />{PRINT_ORDER_EMAIL}{ADMIN_AMWAY_NUMBER}</td>
   <td class="right">�������������� ����������:<br /><span>{PRINT_ORDER_INFO}</span></td>-->
  </tr>
 </table>
 <table class="prod_list"> 
  <tr>
   <th class="left">�������� �����</th>
   <th>���</th>
   <th>����</th>
   <th>����������</th>
   <th class="right">����� </th>
  </tr>
  <!-- BDP: item_row -->
  <tr>
   <td class="left">{PRINT_ORDER_DISC_NAME}</td>
   <td>{PRINT_ORDER_DISC_ARTICUL}</td>
   <td>{PRINT_ORDER_DISC_COST} {MONEY3}</td>
   <td>{PRINT_ORDER_DISC_COUNT} ��.</td>
   <td class="right"><strong>{PRINT_ORDER_DISC_SUMM} {MONEY3}</strong></td>
  </tr>
  <!-- EDP: item_row -->
  <tr>
   <td colspan="2" class="noborder">&nbsp;</td>
   <td class="noborder"><strong>�����:</strong><span></td>
   <td class="noborder">{DISC3}</span></td>
   <td class="right noborder"><strong><span>{PRINT_ORDER_TOTAL_SUMM}</span> {MONEY3}</strong></td>
  </tr>
 </table>
 <table class="fter">
  <tr>
   <td><img src="/img{FOLDER}/diamond.gif" width="362" height="20" alt="" /></td>
   <td>{PRINT_BOTTOM_ADRES}</td>
   <td class="right">{PRINT_BOTTOM_PHONES}</td>
  </tr>
 </table>
</div>
</body>
</html> 