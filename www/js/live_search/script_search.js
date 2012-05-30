$(document).ready(function(){


function liFormat (row, i, num) {
	var result = row[0] +  row[1];
        //var result = row[0];
	return result;
}
function selectItem(li) {
//	if( li == null ) var sValue = 'А ничего не выбрано!';
//	if( !!li.extra ) var sValue = li.extra[2];
//	else var sValue = li.selectValue;
//var sValue = li.extra[0];
var sValue = li.selectValue;
//var sValue = li.extra[0];
//sValue = sValue.split('<span>');
//$('#subject').val(sValue[0]);
	//alert("Выбрана запись с ID: "+sValue[0]);
        
document.getElementById('search_form').submit();
}

// --- Автозаполнение2 ---
$("#subject").autocomplete("/modules/site/main_search/search.php", {
	delay:10,
	minChars:2,
	matchSubset:1,
	autoFill:false,
	matchContains:1,
	cacheLength:10,
	selectFirst:false,
	formatItem:liFormat,
	maxItemsToShow:15,
	onItemSelect:selectItem
}); 
// --- Автозаполнение2 ---
});

