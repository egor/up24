    $(document).ready(function() {	
	//if close button is clicked
	$('.window_ed .close').click(function (e) {
            //Cancel the link behavior
            e.preventDefault();
            $('#mask, .window_ed').hide();
	});		
	//if mask is clicked
	$('#mask').click(function () {
            $(this).hide();
            $('.window_ed').hide();
	});			
    });
    function slot(no, method, form) {

        var id = '#dialog';
        var maskHeight = $(document).height();
        var maskWidth = $(window).width();
        show_form(no, method, form);

        //Set heigth and width to mask to fill up the whole screen
        $('#mask').css({'width':maskWidth,'height':maskHeight});
        //transition effect		
        $('#mask').fadeIn(1000);	
        $('#mask').fadeTo("slow",0.8);	
        //Get the window height and width
        var winH = $(window).height();
        var winW = $(window).width();
        //Set the popup window to center
        $(id).css('top',  winH/2-$(id).height()/2);
        $(id).css('left', winW/2-$(id).width()/2);
        //transition effect
        $(id).fadeIn(2000); 
    }
    function show_form(no, method, form) {
        var global_id = $('#global_user_id').val(); 
        if (method=='new' || method=='edit') {
            $.ajax({  
                url: "/edit_my_data.php?no="+no+"&method="+method+"&user_id="+global_id+"&form="+form,  
                //cache: false,  
                success: function(html){  
                    $("#mw_data_id").html(html);    
                }  
            });  
        } else {
            alert ('непредвиденная ошибка! #0001 :(');
        }
}
    function check_slot(slot) {
        var global_id = $('#global_user_id').val(); 
        document.getElementById('adr_md_1').className = 'no_distinguish';
        document.getElementById('adr_md_2').className = 'no_distinguish';
        document.getElementById('adr_md_3').className = 'no_distinguish';
        if (slot!=0) {
            document.getElementById('c_slots').value=0;
            document.getElementById('adr_md_'+slot).className = 'distinguish';
            //document.getElementById('c_slots').value=slot;
            document.getElementById('c_slots').value = slot;
            //alert (document.getElementById('c_slots').value);
            $.ajax({  
                url: "/edit_my_data.php?print_id="+global_id+"&slot_id="+slot,  
                //cache: false,  
                success: function(html){  
                    $("#info_city").html(html);    
                }  
            }); 
        }
    }
    function del_div_info(){
        //alert('1');
        $('#info_city').html('');
        //$('#info_city').html('123'); 
    }