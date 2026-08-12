    function selectdeseletsection(sectionid,sectionclass) {
            var obj =jQuery('#'+sectionid);
            if (obj.is(":checked")) {        
                jQuery('.'+sectionclass).each(function() { //loop through each checkbox
                  if(!this.disabled){
                    this.checked = true;  //select all checkboxes with class "rolepermission"              
                  }
                });
            }else{
                jQuery('.'+sectionclass).each(function() { //loop through each checkbox
                    this.checked = false; //deselect all checkboxes with class "rolepermission"                      
                });        
            }
    }    
    /*
    jQuery( document ).delegate( "#staff_EDIT_ROLE", "change", function( e ) {
           if (jQuery(this).is(':checked')){
                jQuery('#staff_VIEW_ROLE').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
            }   
    });
    jQuery( document ).delegate( "#staff_VIEW_ROLE", "change", function( e ) {
       if (jQuery('#staff_EDIT_ROLE').is(':checked')){
            jQuery('#staff_VIEW_ROLE').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
        }else{
           if (jQuery('#staff_VIEW_ROLE').is(':checked')){
                jQuery('#staff_VIEW_ROLE').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }else{
                jQuery('#staff_VIEW_ROLE').prop('checked',false);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }
        }   
    });
    jQuery( document ).delegate( "#staff_EDIT_USER", "change", function( e ) {
       if (jQuery(this).is(':checked')){
            jQuery('#staff_VIEW_USER').prop('checked',true);  //select checkboxe with ID "staff_VIEW_USER"              
        }   
    });
    jQuery( document ).delegate( "#staff_VIEW_USER", "change", function( e ) {
       if (jQuery('#staff_EDIT_USER').is(':checked')){
            jQuery('#staff_VIEW_USER').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
        }else{
           if (jQuery('#staff_VIEW_USER').is(':checked')){
                jQuery('#staff_VIEW_USER').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }else{
                jQuery('#staff_VIEW_USER').prop('checked',false);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }
        }   
    });
    jQuery( document ).delegate( "#staff_EDIT_DEPARTMENT", "change", function( e ) {
       if (jQuery(this).is(':checked')){
            jQuery('#staff_VIEW_DEPARTMENT').prop('checked',true);  //select checkboxe with ID "staff_VIEW_USER"              
        }   
    });
    jQuery( document ).delegate( "#staff_VIEW_DEPARTMENT", "change", function( e ) {
       if (jQuery('#staff_EDIT_DEPARTMENT').is(':checked')){
            jQuery('#staff_VIEW_DEPARTMENT').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
        }else{
           if (jQuery('#staff_VIEW_DEPARTMENT').is(':checked')){
                jQuery('#staff_VIEW_DEPARTMENT').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }else{
                jQuery('#staff_VIEW_DEPARTMENT').prop('checked',false);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }
        }   
    });

    jQuery( document ).delegate( "#ticke_EDIT_TICKET", "change", function( e ) {
       if (jQuery(this).is(':checked')){
            jQuery('#ticke_VIEW_TICKET').prop('checked',true);  //select checkboxe with ID "staff_VIEW_USER"              
        }   
    });

    jQuery( document ).delegate( "#ticke_VIEW_TICKET", "change", function( e ) {
       if (jQuery('#ticke_EDIT_TICKET').is(':checked')){
            jQuery('#ticke_VIEW_TICKET').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
        }else{
           if (jQuery('#ticke_VIEW_TICKET').is(':checked')){
                jQuery('#ticke_VIEW_TICKET').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }else{
                jQuery('#ticke_VIEW_TICKET').prop('checked',false);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }
        }   
    });

    jQuery( document ).delegate( "#ticke_SHOW_ONLY_ASSIGN_TICKETS", "change", function( e ) {
       if (jQuery(this).is(':checked')){
            jQuery('#ticke_SHOW_ALL_TICKETS').prop('checked',false);  //select checkboxe with ID "staff_VIEW_USER"              
        }else{
            jQuery('#ticke_SHOW_ALL_TICKETS').prop('checked',true);  //select checkboxe with ID "staff_VIEW_USER"              
            
        }   
    });

    jQuery( document ).delegate( "#ticke_SHOW_ALL_TICKETS", "change", function( e ) {
       if (jQuery(this).is(':checked')){
            jQuery('#ticke_SHOW_ONLY_ASSIGN_TICKETS').prop('checked',false);  //select checkboxe with ID "staff_VIEW_USER"              
        }else{
            jQuery('#ticke_SHOW_ONLY_ASSIGN_TICKETS').prop('checked',true);  //select checkboxe with ID "staff_VIEW_USER"              
            
        }   
    });

    jQuery( document ).delegate( "#kb_EDIT_KB_CATEGORY", "change", function( e ) {
           if (jQuery(this).is(':checked')){
                jQuery('#kb_VIEW_KB_CATEGORY').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
            }   
    });
    jQuery( document ).delegate( "#kb_VIEW_KB_CATEGORY", "change", function( e ) {
       if (jQuery('#kb_EDIT_KB_CATEGORY').is(':checked')){
            jQuery('#kb_VIEW_KB_CATEGORY').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
        }else{
           if (jQuery('#kb_VIEW_KB_CATEGORY').is(':checked')){
                jQuery('#kb_VIEW_KB_CATEGORY').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }else{
                jQuery('#kb_VIEW_KB_CATEGORY').prop('checked',false);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }
        }   
    });

    jQuery( document ).delegate( "#kb_EDIT_KB_ARTICLE", "change", function( e ) {
           if (jQuery(this).is(':checked')){
                jQuery('#kb_VIEW_KB_ARTICLE').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
            }   
    });
    jQuery( document ).delegate( "#kb_VIEW_KB_ARTICLE", "change", function( e ) {
       if (jQuery('#kb_EDIT_KB_ARTICLE').is(':checked')){
            jQuery('#kb_VIEW_KB_ARTICLE').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
        }else{
           if (jQuery('#kb_VIEW_KB_ARTICLE').is(':checked')){
                jQuery('#kb_VIEW_KB_ARTICLE').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }else{
                jQuery('#kb_VIEW_KB_ARTICLE').prop('checked',false);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }
        }   
    });
    jQuery( document ).delegate( "#faqs_EDIT_FAQ", "change", function( e ) {
           if (jQuery(this).is(':checked')){
                jQuery('#faqs_VIEW_FAQ').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
            }   
    });
    jQuery( document ).delegate( "#faqs_VIEW_FAQ", "change", function( e ) {
       if (jQuery('#faqs_EDIT_FAQ').is(':checked')){
            jQuery('#faqs_VIEW_FAQ').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
        }else{
           if (jQuery('#faqs_VIEW_FAQ').is(':checked')){
                jQuery('#faqs_VIEW_FAQ').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }else{
                jQuery('#faqs_VIEW_FAQ').prop('checked',false);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }
        }   
    });
    jQuery( document ).delegate( "#downloads_EDIT_DOWNLOAD", "change", function( e ) {
           if (jQuery(this).is(':checked')){
                jQuery('#downloads_VIEW_DOWNLOAD').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
            }   
    });
    jQuery( document ).delegate( "#downloads_VIEW_DOWNLOAD", "change", function( e ) {
       if (jQuery('#downloads_EDIT_DOWNLOAD').is(':checked')){
            jQuery('#downloads_VIEW_DOWNLOAD').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
        }else{
           if (jQuery('#downloads_VIEW_DOWNLOAD').is(':checked')){
                jQuery('#downloads_VIEW_DOWNLOAD').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }else{
                jQuery('#downloads_VIEW_DOWNLOAD').prop('checked',false);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }
        }   
    });
    jQuery( document ).delegate( "#announcement_EDIT_ANNOUNCEMENT", "change", function( e ) {
           if (jQuery(this).is(':checked')){
                jQuery('#announcement_VIEW_ANNOUNCEMENT').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
            }   
    });
    jQuery( document ).delegate( "#announcement_VIEW_ANNOUNCEMENT", "change", function( e ) {
       if (jQuery('#announcement_EDIT_ANNOUNCEMENT').is(':checked')){
            jQuery('#announcement_VIEW_ANNOUNCEMENT').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
        }else{
           if (jQuery('#announcement_VIEW_ANNOUNCEMENT').is(':checked')){
                jQuery('#announcement_VIEW_ANNOUNCEMENT').prop('checked',true);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }else{
                jQuery('#announcement_VIEW_ANNOUNCEMENT').prop('checked',false);  //select checkboxe with ID "staff_VIEW_ROLE"              
           }
        }   
    });
    */






