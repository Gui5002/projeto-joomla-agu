var j = jQuery.noConflict();
j(document).ready(function($){
	
	function hidealldivremoveclass(){
		$("div.idTabs span a").each(function(){
			var href = $(this).attr('href');
			$(href).css({'display':'none'});
			$(this).removeClass('selected');
		});
	}
	$("div.idTabs span a").each(function(){
		var href = $(this).attr('href');
		$(href).css({'display':'none'});
		var selected = $(this).attr('class');
		if(selected == 'js-ticket-tab-link selected'){
			$($(this).attr('href')).css({'display':'block'});
		}
		$(this).click(function(e){
			e.preventDefault();
			hidealldivremoveclass();
			$(this).addClass('selected');
			$($(this).attr('href')).css({'display':'block'});
		});
	});
	$("div#idTabs ul").css({'margin':'0px','padding':'5px 0px 6px 0px'});

});
/*
Tabs Plugin
Created By Shoaib Rehmat Ali
Lisence GNU/GPL
*/
