// _____________________________________________________________________________
// ����������� ����� � ������ ������� 1_separator000_separator000._decimal
function sdf_FTS(_number,_decimal,_separator)
// ���������� ����������� ��� Float To String
// sd_ - ������� � ��� ������ :) 
// _number - ����� �����, ����� ��� ������� �� �����
// _decimal - ����� ������ ����� �������
// _separator - ����������� ��������
{
// ����������, ���������� ������ ����� �����, �� ��������� ������������ 2 �����
var decimal=(typeof(_decimal)!='undefined')?_decimal:2;

// ����������, ����� ����� ��������� [�� �� �����������] ����� ���������
var separator=(typeof(_separator)!='undefined')?_separator:'';

// ��������������� �������� �������� � �������� �����, �� ���� ������, ���� �����
// �������� �������� ����� �� ����������
var r=parseFloat(_number)

// ��� ��� � javascript ��� ������� ��� �������� ������� ����� ����� �����
// �� ��������� ������������ fix
var exp10=Math.pow(10,decimal);// �������� � ����������� ���������
r=Math.round(r*exp10)/exp10;// ��������� �� ������������ ����� ������ ����� �������

// ����������� � ��������, �������������� �������, ��� ��� � ������ ������ ������ �����
// ���� ������������� �� ���������, �� ���� ����� ����� ������ 
// ������������ 1.00, � �� 1
rr=Number(r).toFixed(decimal).toString().split('.');

// ��������� ������� � ������� ������, ���� ��� ����������
// �� ����, 1000 ���������� 1 000
b=rr[0].replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g,"\$1"+separator);
//r=b+'.'+rr[1];
r=b;
return r;// ���������� ���������
}

gebi = function(id){
	return document.getElementById(id);
}

reloadImage = function()
{
    gebi('captcha').src = '/captcha/kcaptcha_init.php?' + Math.random();
}


// �������� � ���������
function add_favorite(a, title, url) {    
    try {
        // Internet Explorer
        eval("window.external.AddFa-vorite(url, title)".replace(/-/g,'')); 
    }
    catch (e) {
        try {
            // Mozilla
            window.sidebar.addPanel(title, url, "");
        }
        catch (e) {
            // Opera
            if (typeof(opera)=="object") {
                a.rel="sidebar";
                a.title=title;
                a.url=url;
                return true;
            }
            else {
                // Unknown
                alert('������� Ctrl-D ����� �������� �������� � ��������');
            }
        }
    }
    
    return false;
}

getItemSumm = function (id, cost, update, currency) {
    count = gebi('count_' + id).value;
    summ = count*cost;
    
    gebi('summ_' + id).innerHTML = sdf_FTS(summ,'2',' ') + ' '+currency;
    
    if (update == 'no') {
        items.push(new Array(parseInt(id), parseInt(count), parseFloat(cost)));
    }
    
    if (update == 'yes') {
        for (var i=0; i<items.length; i++) {
            if (items[i][0] == parseInt(id)) {
                items[i][1] = parseInt(count);
                items[i][2] = parseFloat(cost);
            }
        }
        
        getTotalCount();
        getTotalSumm();
        getTotalWeight();
    }
}


testCount = function(t){
    var count = 0;
    
    for (var i=0; i<items.length; i++) {
        count += parseInt(items[i][1]);
    }
    
    
    
    //alert (count);
    if (document.getElementById('c_slots').value==0){
        alert ('�������� ����� ��������.');
    }else if (count>=10){
        document.getElementById('buy_type').value=t;
        document.disc_form.submit();
        //buy_type
        
    }
    else
        alert ('����������� ����� ������ - 10 ��.');
}

getTotalCount = function () {    
    var count = 0;
    
    for (var i=0; i<items.length; i++) {
        count += parseInt(items[i][1]);
    }
    if( isNaN(count) ){
      count=0; 
    }
    gebi('total_count').innerHTML = count;
}

getTotalSumm = function () {
    var summ = 0;
    
    for (var i=0; i<items.length; i++) {
        summ += parseInt(items[i][1])*parseFloat(items[i][2]);
    }
    if( isNaN(summ) ){
      summ=0; 
    }
    gebi('total_summ').innerHTML = sdf_FTS(summ,'2',' ');
}
getTotalWeight = function () {    
    var count = 0;
   
    for (var i=0; i<items.length; i++) {
        count += parseInt(items[i][1]);
    }
   
    gebi('total_weight').innerHTML = number_format((count*60)/1000, 2, ',', '');//number_format((count*60)/1000, ',','2',' ');//(count*60)/1000;//sdf_FTS((count*60)/1000,'2',' ');
}

function number_format( number, decimals, dec_point, thousands_sep ) {	// Format a number with grouped thousands
	// 
	// +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +	 bugfix by: Michael White (http://crestidg.com)

	var i, j, kw, kd, km;

	// input sanitation & defaults
	if( isNaN(decimals = Math.abs(decimals)) ){
		decimals = 2;
	}
	if( dec_point == undefined ){
		dec_point = ",";
	}
	if( thousands_sep == undefined ){
		thousands_sep = ".";
	}

	i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

	if( (j = i.length) > 3 ){
		j = j % 3;
	} else{
		j = 0;
	}

	km = (j ? i.substr(0, j) + thousands_sep : "");
	kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
	//kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
	kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


	return km + kw + kd;
        //return 0;
}
