function suretodelete(id) {
    var currentRow = jQuery("div#tk-row-"+id);
    currentRow.children().hide();
    currentRow.css("border","none");
    currentRow.children('div#js-suredelete').css({"background-color": "#FEEFB3","color":"#9F6000","border": "1px solid #9F6000"});
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
    currentRow.css("border","1px solid #999999");
}

function yesdeletethisrecord(id, cname, mname) {
    var currentRow = jQuery("div#tk-row-"+id);
    var tempdiv = currentRow.children('div#js-suredelete');
    var link = "index.php?option=com_jssupportticket&c="+cname+"&task="+mname;
    jQuery.post(link, {rowid: id}, function (data) {
        if (data) {
            array = JSON.parse(data);
            if(array[0] == 1){
                jQuery(tempdiv).css({"background-color": "#DFF2BF","color":"#519013","border": "1px solid #519013"});
                jQuery(tempdiv).html(array[1]);
                jQuery(tempdiv).prepend('<img id="warning-icon" src="components/com_jssupportticket/include/images/successful.png" />');
            }else if(array[0] == 0){
                jQuery(tempdiv).css({"background-color": "#FFBABA","color":"#C61515","border": "1px solid #C61515"});
                var original = jQuery(tempdiv).html();
                jQuery(tempdiv).html(array[1]);
                jQuery(tempdiv).prepend('<img id="warning-icon" src="components/com_jssupportticket/include/images/not_allow.png" />');
                window.setTimeout(function () {
                    currentRow.children().show(); 
                    currentRow.css("border","1px solid #999999");
                    jQuery(tempdiv).hide();
                    jQuery(tempdiv).html(original);
                }, 3000);
            }

        }
    });
}

function getDataForDepandantField(parentf, childf, type,jsession) {
    if (type == 1) {
        var val = jQuery("select#" + parentf).val();
    } else if (type == 2) {
        var val = jQuery("input[name=\'" + parentf + "\']:checked").val();
    }
    var link = "index.php?option=com_jssupportticket&c=userfields&task=datafordepandantfield&"+jsession+"=1";
    jQuery.post(ajaxurl, {fvalue: val, child: childf}, function (data) {
        if (data) {
            console.log(data);
            var d = jQuery.parseJSON(data);
            jQuery("select#" + childf).replaceWith(d);
        }
    });
}

function deleteCutomUploadedFile(field1) {
    jQuery("input#"+field1).val(1);
    jQuery("span."+field1).hide();
    
}
