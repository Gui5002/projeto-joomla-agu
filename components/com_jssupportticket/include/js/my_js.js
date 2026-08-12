function suretodelete(id) {
    var currentRow = jQuery("div#tk-row-"+id);
    currentRow.children().hide();
    currentRow.children('div#js-suredelete').show();
    
    // window.setTimeout(function () {
    //     currentRow.children().show(); 
    //     currentRow.children('div#js-suredelete').css("display","none");
    //     currentRow.css("border","1px solid #999999");
    // }, 1000);

}

function canceldelete(id) {
    var currentRow = jQuery("div#tk-row-"+id);
    currentRow.children().show(); 
    currentRow.children('div#js-suredelete').hide();
}

function yesdeletethisrecord(id, cname, mname, jsession) {
    var currentRow = jQuery("div#tk-row-"+id);
    var tempdiv = currentRow.children('div#js-suredelete');
    var link = "index.php?option=com_jssupportticket&c="+cname+"&task="+mname+"&"+jsession+"=1";
    var hostname = jQuery("input#joomlinkforjs").val();
    jQuery.post(link, {rowid: id}, function (data) {
        if (data) {
            array = JSON.parse(data);
            if(array[0] == 1){
                jQuery(tempdiv).html(array[1]);
                jQuery(tempdiv).prepend('<img id="warning-icon" src="'+hostname+'components/com_jssupportticket/include/images/successful.png" />');
            }else if(array[0] == 0){
                var original = jQuery(tempdiv).html();
                jQuery(tempdiv).html(array[1]);
                jQuery(tempdiv).prepend('<img id="warning-icon" src="'+hostname+'components/com_jssupportticket/include/images/not_allow.png" />');
                window.setTimeout(function () {
                    currentRow.children().show(); 
                    jQuery(tempdiv).hide();
                    jQuery(tempdiv).html(original);
                }, 3000);
            }
        }
    });
}
