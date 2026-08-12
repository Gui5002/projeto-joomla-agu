<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:    www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
   + Project:    JS Tickets
  ^
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;

    $color1 = "#4f6df5"; /* header bk color */
    $color2 = "#2b2b2b";/* header link hover , header bottom bk, content heading bottom line, content box hover bk+ border color , */
    $color3 = "#f5f2f5"; /* content box background color */
    $color4 = "#636363";/* every text color in content */
    $color5 = "#d1d1d1";/* border color and div  border line  */
    $color6 = "#e7e7e7"; /* Every button and tab background color*/
    $color7 = "#ffffff"; /* header top and bottom text color,sort text color,headind seprater and text color */
    $color8 = "#2DA1CB";/* subject and name color for ticket listing */
    $color9 = "#000000";
    $color10 = "";
      $style = "
            div#jsst-header { background-color: $color1;}
            div.js-ticket-top-search-wrp {border: 1px solid $color5;background-color:$color3;}
            div#jsst-header span.jsst-header-tab a.js-cp-menu-link {background: rgba(0,0,0,0.4); color: $color7;}
            div#jsst-header span.jsst-header-tab a.js-cp-menu-link:hover {background: rgba(0,0,0,0.6); color: $color7;}
            div.js-ticket-fields-wrp div.js-ticket-form-field input.js-ticket-field-input {background-color: $color7;border: 1px solid $color5;color: $color4; margin:0px;}
            div.js-ticket-search-form-btn-wrp button.js-search-button {background: $color1;color: $color7;border: 1px solid $color5;}
            div.js-ticket-search-form-btn-wrp button.js-search-button:hover {border-color: $color2;}
            div.js-ticket-search-form-btn-wrp button.js-reset-button {background: $color2;color: $color7;border: 1px solid $color5;}
            div.js-ticket-search-form-btn-wrp button.js-reset-button:hover {border-color: $color1;}
            div.js-ticket-downloads-content div.js-ticket-download-box {border: 1px solid $color5;color: $color4;}
            div.js-ticket-downloads-wrp div.js-ticket-downloads-heading-wrp {background-color: $color2; border: 1px solid $color5; color: $color7;}
            div.js-ticket-downloads-content div.js-ticket-download-box div.js-ticket-download-left a.js-ticket-download-title span.js-ticket-download-name:hover {color: $color2;}
            div.jsst-main-up-wrapper a {color: #428bca; text-decoration: none;}
            div.js-ticket-messages-data-wrapper span.js-ticket-messages-block_text {color: $color4;}
            span.js-ticket-user-login-btn-wrp a.js-ticket-login-btn {background-color: $color1;color: $color7;border: 1px solid $color5;}
            span.js-ticket-user-login-btn-wrp a.js-ticket-login-btn:hover {border-color: $color2;}
            div#jl_pagination div#jl_pagination_pageslink ul li.active a {background-color: $color1; color: $color7;border: 1px solid $color1 }
            div#jl_pagination div#jl_pagination_pageslink ul li.active a:hover {border-color:$color2;}
            div.js-ticket-feedback-heading {border: 1px solid $color5;background-color: $color2;color: $color7;}
            div.jsst-feedback-det-wrp div.jsst-feedback-det-list {border: 1px solid $color5;}
            div.jsst-feedback-det-wrp div.jsst-feedback-det-list div.jsst-feedback-det-list-top {border-bottom: 1px solid $color5;}
            div.jsst-feedback-det-list-data-btm div.jsst-feedback-det-list-data-top-val {color: $color4;}
            div.jsst-feedback-det-list-data-btm div.jsst-feedback-det-list-datea-btm-rec div.jsst-feedback-det-list-data-btm-val {color: $color4;}
            div.jsst-feedback-det-list-data-btm div.jsst-feedback-det-list-datea-btm-rec div.jsst-feedback-det-list-data-btm-val.name {color: $color1;text-decoration: underline;font-size: 16px;margin-left: 0px;}
            div.jsst-feedback-det-list-data-btm div.jsst-feedback-det-list-datea-btm-rec div.jsst-feedback-det-list-data-btm-val.staff {color: $color1;}
            div.jsst-feedback-det-list-data-btm div.jsst-feedback-det-list-data-top-val a.jsst-feedback-det-list-data-top-val-txt {color: $color4;}
            div.jsst-feedback-det-list-data-btm div.jsst-feedback-det-list-data-btm-title {color: $color2;}
            div.jsst-feedback-det-wrp div.jsst-feedback-det-list div.jsst-feedback-det-list-btm {display: inline-block;width: 100%;float: left;padding: 15px;background: $color3;}
            div.jsst-feedback-det-wrp div.jsst-feedback-det-list div.jsst-feedback-det-list-btm div.jsst-feedback-det-list-btm-title {color: $color4;}
            div.jsst-feedback-det-wrp div.jsst-feedback-det-list div.jsst-feedback-det-list-btm div.jsst-feedback-det-list-btm-val {color: $color2;}
            div#jsst-wrapper-top {float: left;width: 100%;padding: 0px;position: relative;background: #f7f7f7;margin-bottom: 15px;border: 1px solid #ebecec;border-top: none;}
            div#jsst-breadcrunbs ul li {display: inline-block;margin: 0;color: $color1;text-transform: capitalize;}
            div#jsst-breadcrunbs ul li a {text-decoration: none;color: $color1;}
            div#jsst-breadcrunbs ul li:last-child {color: $color2;}
            /* announcement detail */

            div.js-ticket-search-heading-main-wrp {color: $color2;background-color: $color3;}
            div.js-ticket-knowledgebase-details {color: $color4;}
            div#jsst-header span.jsst-header-tab a.js-cp-menu-link {background: rgba(0,0,0,0.4);color: $color7;}
            div#jsst-header span.jsst-header-tab a.js-cp-menu-link:hover {background: rgba(0,0,0,0.6);color: $color7;}
            
            
            /* user downloads */
            
            div.js-ticket-messages-data-wrapper span.js-ticket-messages-main-text {color: $color4;}
            div.js-ticket-downloads-content div.js-ticket-download-box div.js-ticket-download-left a.js-ticket-download-title span.js-ticket-download-name {color: $color4;}

            /* Download */
            div.js-ticket-download-content-wrp div.js-ticket-search-heading-wrp {color: $color2;background-color: $color3;}

            div#js-ticket-main-popup {background: $color7;}
            span#js-ticket-popup-title {background-color: $color1;color: $color7;}
            div.js-ticket-download-description {color: $color4;}
            div.js-ticket-download-btn button.js-ticket-download-btn-style {color: $color1;border: 1px solid $color1;background: $color7;}
            div.js-ticket-download-btn a.js-ticket-download-btn-style {color: $color1;border: 1px solid $color1;}
            
            /*knowledgebase */
            div.js-ticket-categories-heading-wrp {background-color: $color2;border: 1px solid $color5;color: $color7;}
            div.tk_attachment_value_wrapperform {border: 1px solid $color5;background: $color7;color: $color4;}
            span.tk_attachment_value_text {border: 1px solid $color5;background-color: $color7;}
            span.tk_attachments_configform{color: $color4;}
            span.tk_attachments_addform {background-color: $color2;color: $color7;border: 1px solid $color5;text-transform:capitalize;}
            span.tk_attachments_addform:hover {border-color: $color1;}
            div.js_ticketattachment {background-color: $color7;border: 1px solid $color5;}
            div.jsst-main-up-wrapper a.js-ticket-delete-attachment {color: $color7;text-decoration: none;}
            a.js-ticket-delete-attachment {background-color: #ed3237;color: $color7;}
            /*div.js-ticket-heading-wrp{padding: 10px;color: $color7;background-color: $color4;}*/
            div.js-ticket-heading-wrp div.js-ticket-heading-right a.js-ticket-add-download-btn{background: $color2;color: $color7;border: 1px solid $color5;}
            
            /* knowledgebase detail*/
            {background-color: $color3;border: 1px solid $color5;color: $color2;}
            div.js-ticket-downloads-content div.js-ticket-download-box div.js-ticket-knowledgebase-download-left a.js-ticket-download-title span.js-ticket-download-name {color: $color4;}
            div.js-ticket-downloads-content div.js-ticket-download-box div.js-ticket-knowledgebase-download-left a.js-ticket-download-title span.js-ticket-download-name:hover {color: $color2;}
            div.js-ticket-categories-content div.js-ticket-category-box {background-color: $color7;border: 1px solid $color5;}
            div.js-ticket-categories-content div.js-ticket-category-box a.js-ticket-category-title span.js-ticket-category-download-logo {border-bottom: 1px solid $color5;}
            div.js-ticket-categories-content div.js-ticket-category-box a.js-ticket-category-title span.js-ticket-category-name {color: $color2;}
            div.js-ticket-categories-content div.js-ticket-category-box a.js-ticket-category-title span.js-ticket-category-name:hover {color: $color1;}
            
            /*ticket status*/
            div.system-message-container{background: url(../images/notsaved.png) 12px 15px no-repeat #ffd2d3;border: 1px solid #871414;box-sizing: border-box;width: 100%;}
            div.js-ticket-field-title {color: $color2;text-transform:capitalize;}
            div.js-ticket-form-btn-wrp {border-top: 2px solid $color2;}
            div.js-ticket-form-btn-wrp input.js-ticket-save-button {background-color: $color1;color: $color7;border: 1px solid $color5;}
            div.js-ticket-field-wrp input.js-ticket-form-input-field {background-color: $color7;border: 1px solid $color5;color: $color4;}
            
            /*add ticket form*/
            div.js-ticket-search-top div.js-ticket-search-right div.js-ticket-search-btn-wrp input.js-ticket-search-btn:hover {border-color: $color2;}
            div.js-ticket-search-top div.js-ticket-search-right div.js-ticket-search-btn-wrp input.js-ticket-reset-btn:hover {border-color: $color1;}
            div.js-ticket-search-top div.js-ticket-search-left div.js-ticket-search-fields-wrp input.js-ticket-search-input-fields {border: 1px solid $color5;background-color: $color7;color: $color4;}
            div#js-tk-formwrapper div.js-append-premadecheck {border:1px solid $color5;color: $color4;}
            div#js-tk-formwrapper div.js-form-value select#premadeid {width: 100%;min-height: 50px;border-radius: 0;box-shadow: unset !important;padding: 10px;border:1px solid $color5;color: $color4;}
            div#js-tk-formwrapper div.js-form-value select#premadeid {background-image: url(../../include/images/selecticon.png) !important;background-repeat: no-repeat;background-position: calc(100% - 12px);-webkit-appearance: none;-moz-appearance: none;appearance: none;background-size: 13px;}
            div.js-form-value div.field-calendar div.input-append input#ticket_duedate {background-color: $color7;border: 1px solid $color5;color: $color4;}
            div#js-tk-formwrapper div.js-ticket-from-field input.js-ticket-form-field-input {background-color: $color7;border: 1px solid $color5;color: $color4;}
            div.js-ticket-search-heading-main-wrp {background-color: $color2;color: $color7;}
            div.js-ticket-search-heading-main-wrp div.js-ticket-heading-right a.js-ticket-add-download-btn {background: $color7;color: $color2;}
            div.js-ticket-search-heading-main-wrp div.js-ticket-heading-right a.js-ticket-add-download-btn:hover {color: $color1;}
            div.js-ticket-search-heading-main-wrp.second-style {background-color: $color3;color: $color2;}
            div.js-ticket-from-field-wrp div.js-ticket-from-field-title {color: $color2;text-transform:capitalize;}
            div.js-ticket-from-field-wrp div.js-ticket-from-field input.js-ticket-form-field-input {background-color: $color7;border: 1px solid $color5;color: $color4;}
            div.js-form-submit-btn-wrp {border-top: 2px solid $color2;}
            div.js-form-submit-btn-wrp a.js-ticket-cancel-button {background: $color2;color: $color7;border: 1px solid $color5;}
            div.js-form-submit-btn-wrp a.js-ticket-cancel-button:hover {border-color: $color1;}
            div.js-form-submit-btn-wrp input.js-save-button {background-color: $color1;color: $color7;border: 1px solid $color5;}
            div.js-form-submit-btn-wrp input.js-save-button:hover {border-color: $color2;}
            
            /* add ticket*/
            div#js-tk-formwrapper div.js-form-title{color: $color2;}
            div#js-tk-formwrapper div.js-form-value input.js-form-input-field {border: 1px solid $color5;color: $color4;}
            div#js-tk-formwrapper div.js-form-value select.js-form-select-field {border: 1px solid $color5;color: $color4;background-color: $color7;}
            div.js-attachment-files {border: 1px solid $color5;}
            span.js-attachment-file-box {border: 1px solid $color5;background-color: $color7;}
            span.js-attachment-option {color: $color4;}
            span#js-attachment-add {background-color: $color2;color: $color7;border: 1px solid $color5;text-transform:capitalize;}
            span#js-attachment-add:hover {border-color: $color1;}
            div.js-ticket-reply-form-button-wrp input.js-ticket-save-button:hover {border-color: $color2;}
            
            /* my ticket*/
            div.js-ticket-top-cirlce-count-wrp {border: 1px solid $color5;}
            div.js-myticket-link a.js-myticket-link {border: 1px solid $color5;}
            div.js-ticket-search-wrp {border: 1px solid $color5;}
            div.js-myticket-link a.js-myticket-link span.js-ticket-circle-count-text.js-ticket-green {color: #14A76C;}
            div.js-myticket-link a.js-myticket-link span.js-ticket-circle-count-text.js-ticket-pink {color: #D79922;}
            div.js-myticket-link a.js-myticket-link span.js-ticket-circle-count-text.js-ticket-red {color: #e82d3e;}
            div.js-ticket-search-wrp div.js-ticket-form-wrp form.js-tk-combinesearch div.js-filter-wrapper {background: $color3;}
            div.js-ticket-search-wrp div.js-ticket-form-wrp form.js-tk-combinesearch div.js-filter-wrapper div.js-filter-form-fields-wrp input.js-ticket-input-field {background-color: $color7;border: 1px solid $color5;color: $color4;}
            div.js-ticket-search-wrp div.js-ticket-form-wrp form.js-tk-combinesearch div.js-filter-field-wrp div.input-append button {background-color: $color3;border: 1px solid $color5;border-left: 0px;}
            span#js-filter-wrapper-toggle-btn .js-search-filter-btn {color: $color4;border: 1px solid $color5;background: $color7;padding:13px 0;}
            div.js-ticket-search-wrp div.js-ticket-form-wrp form.js-tk-combinesearch span.js-filter-button-wrp button.js-ticket-search-btn {background-color: $color1;color: $color7;border: 1px solid $color5;}
            div.js-ticket-search-wrp div.js-ticket-form-wrp form.js-tk-combinesearch span.js-filter-button-wrp button.js-ticket-search-btn:hover {border-color: $color2;}
            div.js-ticket-search-wrp div.js-ticket-form-wrp form.js-tk-combinesearch span.js-filter-button-wrp button.js-ticket-reset-btn {background-color: $color2;color: $color7;border: 1px solid $color5;}
            #filter_datestart_btn{background-color: $color7 !important;border-color: $color1 !important;color: $color1 !important;}
            #filter_datestart_btn:hover{background-color: $color7 !important;border-color: $color2 !important;color: $color2 !important;}
            #filter_dateend_btn{background-color: $color7 !important;border-color: $color1 !important;color: $color1 !important;}
            #filter_dateend_btn:hover{background-color: $color7 !important;border-color: $color2 !important;color: $color2 !important;}
            div.js-ticket-wrapper {border: 1px solid $color5;box-shadow: 0 8px 6px -6px #dedddd;}
            div.js-ticket-body-data-elipses a {color: $color2;text-decoration: none;}
            div.js-ticket-wrapper div.js-ticket-data span.js-ticket-value {color: $color4;}
            div.js-ticket-wrapper div.js-ticket-data .name span.js-ticket-value {color: $color4;}
            div.js-ticket-wrapper div.js-ticket-data span.js-ticket-status {background-color: $color7;border: 1px solid $color5;}
            span.js-ticket-wrapper-textcolor {color: $color7;}
            div.js-ticket-sorting {background: $color2;color: $color7;}
            div.js-ticket-wrapper div.js-ticket-data1 div.js-ticket-data-row .js-ticket-data-tit {color: $color2;}
            div.js-ticket-wrapper div.js-ticket-data1 div.js-ticket-data-row .js-ticket-data-val {color: $color4;}
            div.js-ticket-sorting-right div.js-ticket-sort select.js-ticket-sorting-select {background: $color7;color: $color2;border: 1px solid $color5;}
            div.js-ticket-sorting-right div.js-ticket-sort a.js-admin-sort-btn {background: $color7;border: 1px solid $color5;border-left:none;}
            div.js-ticket-search-wrp div.js-ticket-form-wrp form.js-tk-combinesearch div.js-filter-field-wrp input.js-ticket-input-field {background-color: $color7;border: 1px solid $color5;color: $color4;}
            div.js-myticket-link a.js-myticket-link.js-ticket-green:hover {border-color: #14A76C;}
            div.js-myticket-link a.js-myticket-link.js-ticket-green.active {border-color: #14A76C;}
            div.js-myticket-link a.js-myticket-link.js-ticket-red:hover {border-color: #e82d3e;}
            div.js-myticket-link a.js-myticket-link.js-ticket-red.active {border-color: #e82d3e;}
            div.js-myticket-link a.js-myticket-link.js-ticket-pink:hover {border-color: #D79922;}
            div.js-myticket-link a.js-myticket-link.js-ticket-pink.active {border-color: #D79922;}
            div.js-myticket-link a.js-myticket-link.js-ticket-blue.active {border-color: #5ab9ea;}
            span#js-filter-wrapper-toggle-btn .js-search-filter-btn:hover {border-color: $color1;}
            div.js-ticket-wrapper:hover {border: 1px solid$color1;box-shadow: 0 4px 4px 0px rgba(162, 162, 162, 0.71);}
            div.js-ticket-search-wrp div.js-ticket-form-wrp form.js-tk-combinesearch div.js-filter-field-wrp select#filter_department,div.js-ticket-search-wrp div.js-ticket-form-wrp form.js-tk-combinesearch div.js-filter-field-wrp select {background-color: $color7 !important;border: 1px solid $color5;color: $color4;}
            div.js-ticket-search-wrp div.js-ticket-form-wrp form.js-tk-combinesearch div.js-filter-field-wrp select#filter_priority {background-color: $color7 !important;border: 1px solid $color5;color: $color4;}
            div.js-myticket-link a.js-myticket-link:hover {box-shadow: 0 1px 3px 0 rgba(60,64,67,0.302), 0 4px 8px 3px rgba(60,64,67,0.149);background-color: #fafafb;}
            /*div.js-ticket-search-wrp div.js-ticket-form-wrp form.js-tk-combinesearch span.js-filter-button-wrp button.js-ticket-reset-btn:hover {border-color: $color1;}
            div.js-combine-search-wrapper div.js-combine-search-form-wrp form.js-tk-combinesearch div.js-filter-wrapper div.js-filter-form-fields-wrp input.js-ticket-input-field {background-color: $color7;border: 1px solid $color5;color: $color4;}
            div.js-combine-search-wrapper div.js-combine-search-form-wrp form.js-tk-combinesearch div.js-filter-field-wrp input {background-color: $color7;border: 1px solid $color5;color: $color4;}
            div.js-combine-search-wrapper div.js-combine-search-form-wrp form.js-tk-combinesearch div.js-filter-field-wrp div.input-append button {background-color: $color3;border: 1px solid $color5;border-left: 0px;}
            div.js-combine-search-wrapper div.js-combine-search-form-wrp form.js-tk-combinesearch span.js-filter-button-wrp button.js-ticket-search-btn {background-color: $color1;color: $color7;border: 1px solid $color5;}
            div.js-combine-search-wrapper div.js-combine-search-form-wrp form.js-tk-combinesearch span.js-filter-button-wrp button.js-ticket-reset-btn {background-color: $color2;color: $color7;border: 1px solid $color5;}
            div#js-tk-wrapper {border: 1px solid $color5;box-shadow: 0 8px 6px -6px #dedddd;}
            div.name span.js-tk-value {color: $color1;text-decoration: underline;}
            span.js-ticket-priorty-box {color: $color7;}
            div.js-right div.js-wrapper span.js-tk-title {color: $color2;}
            div.js-right div.js-wrapper span.js-tk-value {color: $color4;}
            
            /* TICKET DETAIL*/ 
            .js-tkt-det-cnt {background: $color7;border: 1px solid $color5;box-shadow: 0 0 3px 2px rgba(162, 162, 162, 0.71);}
            div.js-ticket-user-name-wrp{color: $color1!important;}
            .js-tkt-det-user .js-tkt-det-user-cnt div.js-ticket-user-subject-wrp {color: $color2;}
            .js-tkt-det-user .js-tkt-det-user-cnt div.js-ticket-user-email-wrp {color: $color4;}
            .js-tkt-det-tkt-msg {color: $color4;border-top: 1px solid $color5;}
            .js-ticket-btn-box .js-button {border: 1px solid $color5;background: $color3;}
            .js-ticket-btn-box .js-button span {color: $color4;text-transform:capitalize;}
            .js-ticket-thread-heading {color: $color7;background: $color2;}
            .js-ticket-thread {border: 1px solid $color5;background: $color7;box-shadow: 0 0 3px 2px rgba(0,0,0,0.2)}
            .js-ticket-user-name-wrp span{color: $color1;}
            div.js-ticket-attachments-wrp {border-top: 1px solid $color5;}
            div.js-ticket-attachments-wrp div.js_ticketattachment {border: 1px solid $color5;background-color: $color7;}
            div.js-ticket-attachments-wrp a.js-all-download-button {background-color: $color1;color: $color7;border: 1px solid $color5;}
            .js-ticket-time-stamp-wrp {border-top: 1px solid $color5;}
            .js-ticket-time-stamp-wrp .js-ticket-ticket-created-date {color: $color4;}
            .js-ticket-rows-wrp {background: $color7;border: 1px solid $color5;}
            div.js-tkt-detail-cnt {box-shadow: 0 0 3px 2px rgba(162, 162, 162, 0.71);}
            .js-tkt-det-status {background: #5bb02f;color: $color7}
            .js-ticket-row div.js-ticket-field-title {color: $color2;}
            .js-ticket-row .js-ticket-field-value {color: $color4;}
            .js-ticket-priorty {float: left;width: 100%;text-align: center;padding: 15px;color: $color7;margin-bottom: 15px;font-size: 18px;line-height: initial;}
            div.js-ticket-reply-forms-heading {background-color: $color2;border: 1px solid $color5;color: $color7;text-transform:capitalize;}
            div.js-attachment-wrp div.js-form-title {color: $color2;}
            div#js-attachment-files {border: 1px solid $color5;background: $color7;}
            div#js-attachment-option {color: $color4;}
            div.js-ticket-reply-form-button-wrp {border-top: 2px solid $color2;}
            .js-button:hover {border-color: $color2;cursor: pointer;}
            div.js-ticket-attachments-wrp a.js-all-download-button:hover {border-color: $color2;}
            div.js-ticket-reply-form-button-wrp input.js-ticket-save-button {background-color: $color1;color: $color7;border: 1px solid $color5;}
            .js-ticket-post-reply-box {border: 1px solid $color5;background: $color7;}
            .js-ticket-btn-box {float: left;width: 100%;padding: 10px 15px 15px;}
            .js-ticket-thread-add-btn .js-ticket-thread-add-btn-link {color: $color7;background: $color1;border: 1px solid $color5;}
            .js-ticket-thread-add-btn .js-ticket-thread-add-btn-link:hover {border-color: $color1;color: $color7;}
            div.js-ticket-attachments-wrp div.js_ticketattachment a.js-download-button {background-color: $color3;color: $color4;border: 1px solid $color5;}
            div.js-myticket-link a.js-myticket-link.active {border-color: #14A76C;}
            .js-tkt-go-to-all-wrp {background-color:#fef1e6;border-top: 1px solid $color5;}
            a.js-tkt-go-to-all {color: $color1;}
            .js-tkt-det-hdg .js-tkt-det-hdg-txt {color: $color2;}
            div.timer-wrp {background-color: #f5f5f5;}
            div.js-tkt-det-user div.timer span{background-color: $color7;border:1px solid $color5;padding: 15px 10px;margin:1px 1px 10px;color: $color4 }
            div.js-tkt-det-user div.timer_1 span{background-color: $color7;border:1px solid $color5;padding: 15px 10px;margin:1px 1px 10px;color: $color4 }
            div.js-tkt-det-user div.timer-buttons span.timer-button{background-color: $color7;border:1px solid $color5;padding: 0px 5px;color: $color4 }
            .js-ticket-field-value .js-tkt-det-hdg .js-tkt-det-hdg-txt {color: $color4;text-transform:capitalize;}
            .js-tkt-det-trsfer-dep {border-top: 1px solid $color5;}
            .js-tkt-det-hdg .js-tkt-det-hdg-btn {color: $color1;text-decoration: underline;}
            .js-tkt-det-trsfer-dep .js-tkt-det-trsfer-dep-txt {color: $color4;}
            .js-tkt-det-trsfer-dep .js-tkt-det-trsfer-dep-txt span.js-tkt-det-trsfer-dep-txt-tit {color: $color2;}
            .js-tkt-det-trsfer-dep .js-tkt-det-hdg-btn {color: $color1;}
            div.js-ticket-assigntome-wrp div.js-ticket-assigntome-field-wrp {border: 1px solid $color5;background-color: $color7;}
            div.js-ticket-closeonreply-wrp div.js-form-title-position-reletive-left {border: 1px solid $color5;background-color: $color7;color: $color2;}
            div.js-ticket-premade-msg-wrp div.js-ticket-premade-field-title {color: $color2;}
            select.js-ticket-premade-select {background-color: $color7 !important;border: 1px solid $color5;}
            span.js-ticket-apend-radio-btn {border: 1px solid $color5;background-color: $color7;}
            .js-tkt-det-user .js-tkt-det-user-cnt .js-tkt-det-user-data {color: $color4;}
            div#js-history-popup {background: $color7;}
            div#js-history-popup div#js-history-head {background: $color1;color: $color7;}
            div#js-private-crendentials-popup {background: $color7;}
            div#js-private-crendentials-popup div#js-private-crendentials-head {background: $color1;color: $color7;}
            div.js-ticket-priorty-btn-wrp button.js-ticket-priorty-cancel {background-color: $color1;border:1px solid $color1;color: $color7;}
            div.js-ticket-priorty-btn-wrp button.js-ticket-priorty-cancel:hover {border:1px solid $color2;}
            div#userpopupforchangepriority {background: $color7;}
            div#userpopupforchangepriority div.js-ticket-priorty-header {background: $color1;color: $color7;}
            div#userpopupforchangepriority div.js-ticket-priorty-fields-wrp div.js-ticket-select-priorty select#priorityid {background-color: $color7;border: 1px solid $color5;}
            div#popupforagenttransfer {background: $color7;}
            div#popupforagenttransfer div.jsst-popup-header {background: $color1;color: $color7;}
            div.js-ticket-premade-msg-wrp div.js-ticket-premade-field-wrp select#assigntostaff {padding-top: 11px;padding-bottom: 11px;    display: inline-block;width: 100%;float: left;border-radius: 0px;margin-bottom: 0px;background-color: $color3 !important;}
            div.js-ticket-post-reply-wrapper div.js-ticket-white-background {background-color: $color7;}
            div.js-ticket-post-reply-wrapper div.js-ticket-detail-box div.js-ticket-detail-right {background: $color7;}
            div.js-ticket-post-reply-wrapper div.js-ticket-time-stamp-wrp {border-top: 1px solid $color5;}
            div.js-ticket-post-reply-wrapper div.js-ticket-detail-box div.js-ticket-detail-right div.js-ticket-rows-wrp .js-ticket-field-value.name {color: $color1;font-size: 17px;text-decoration: underline;text-transform: capitalize;line-height: initial;}
            div#popupfordepartmenttransfer {background: $color7;}
            div#popupfordepartmenttransfer div.jsst-popup-header {background: $color1;color: $color7;}
            div.js-ticket-premade-msg-wrp div.js-ticket-premade-field-wrp select#departmentid {padding-top: 11px;padding-bottom: 11px;    display: inline-block;width: 100%;float: left;border-radius: 0px;margin-bottom: 0px;background-color: $color3 !important;}
            div#popupforinternalnote {background: $color7;}
            div#popupforinternalnote div.jsst-popup-header {background: $color1;color: $color7;}
            div.js-ticket-internalnote-wrp div.js-ticket-internalnote-field-wrp input.js-ticket-internalnote-input {border: 1px solid $color5;background-color: $color3;}
            div#js_attachment_files_internalnote {border: 1px solid $color5;background: $color7;}
            div.jsst-popup-wrapper {background-color: $color7;}
            div.jsst-popup-wrapper div.jsst-popup-header {background: $color1;color: $color7;}
            div.js-ticket-edit-field-wrp input.js-ticket-edit-field-input {padding: 10px 5px;min-height: 42px;width: 100%;border-radius: 0px;background-color: $color3;}
            div.js-ticket-edit-field-wrp textarea {padding: 10px 5px;min-height: 42px;width: 100%;border-radius: 0px;background-color: $color3;}
            div.js-ticket-priorty-btn-wrp input.js-ticket-priorty-save {background-color: $color1;color: $color7;border: 1px solid transparent;}
            div.js-ticket-priorty-btn-wrp input.js-ticket-priorty-cancel {background-color: #48484a;color: $color7;border: 1px solid transparent;}
            div.jsst-merge-popup-wrapper {background-color: $color7;} 
            div.jsst-merge-popup-wrapper div.jsst-popup-header {background: $color1;color: $color7;}
            div.js-ticket-merge-white-bg {background-color: $color7;}
            div.js-tickets-list-wrp {border-top: 2px solid $color5;background-color: $color3;border-bottom: 1px solid $color1;}
            div.js-merge-form-value input.inputbox {border: 1px solid $color5;}
            span.js-merge-btn input.js-search {background-color: $color1 !important;color: $color7;border: 1px solid transparent;min-height: 42px;width:100%;}
            span.js-merge-btn input.js-cancel {background-color: #48484a !important;color: $color7;border: 1px solid transparent;min-height: 42px;width:100%;}
            div.js-ticket-wrapper div.js-ticket-data span.js-ticket-title {color: $color4;}
            div.js-view-tickets {border-top: 1px solid $color1;}
            div.jsst_userpages {border: 1px solid $color5;width: calc(100% - 30px);display: inline-block;text-align: right;vertical-align: middle;margin: 0 15px;padding:5px;}
            div.js-form-button-wrapper {text-align: center;border-top: 1px solid $color1;width: 94%;margin: 0px 3%;margin-top: 20px;}
            input.js-merge-cancel-btn {background-color: #48484a !important;color: $color7;border: 1px solid transparent;}
            a.js-merge-btn {color: $color7 !important; background-color: $color1  !important;}
            div.js-form-button-wrapper input.js-merge-save-btn {background-color: $color1 !important;color: $color7;border: 1px solid transparent;}
            div.jsst-ticket-detail-timer-wrapper {float: left;width: 100%;border: 1px solid $color5;background-color: $color3;padding: 20px 10px;}
            div.js-ticket-tabs-wrapper div.jsst-ticket-detail-timer-wrapper {background-color: #fafafa;}
            div.js-ticket-post-reply-wrapper div.jsst-ticket-detail-timer-wrapper {background-color: #fafafa;}
            div.jsst-ticket-detail-timer-wrapper div.timer-right div.timer{float: left;border:1px solid $color5;padding: 5px 10px;margin-bottom:2px;}
            div.js-ticket-edit-options-wrp a.js-button {color: $color4;border: 1px solid $color5}
            div.jsst-ticket-detail-timer-wrapper div.timer-right div.timer-buttons span.timer-button{background-color: $color7;border:1px solid $color5;padding: 3px 2px 8px; width:27px;margin:1px;}
            div.js-ticket-tabs-body div.jsst-ticket-detail-timer-wrapper div.timer-right div.timer-buttons span.timer-button{background-color:$color2;padding: 4px 2px 6px 2px;border-color:$color2;}
            div.js-ticket-tabs-body div.jsst-ticket-detail-timer-wrapper div.timer-right div.timer-buttons span.timer-button:hover{background-color:$color1;border-color:$color2;}
            .js-tkt-det-tkt-msg p{border-bottom: 1px solid $color5; padding-bottom: 15px;padding-top: 10px;margin-bottom: 0px;}
            div#js-history-popup div.js-ticket-history-table-wrp table.js-table-striped {float: left;width: 100%;padding: 20px 0px;text-align: center;border: 1px solid $color5;color:$color2;}
            div.js-ticket-priorty-btn-wrp button.js-ticket-priorty-save {background-color: $color1;color: $color7;border: 1px solid $color1;}
            div.js-ticket-priorty-btn-wrp button.js-ticket-priorty-save:hover{border: 1px solid $color2;}
            div.js-ticket-edit-field-wrp textarea#ttt_desc{width: 100%;border-radius: 0px;background-color: $color3;}

            /* roles */
            div.js-ticket-table-header {background-color: #ecf0f5;border: 1px solid $color5;}
            div.js-ticket-table-header div.js-ticket-table-header-col {color: $color2;text-transform:capitalize;}
            div.js-ticket-table-body div.js-ticket-data-row{border: 1px solid $color5}
            div.js-ticket-table-body div.js-ticket-data-row div.js-ticket-table-body-col .js-ticket-title-anchor {color:$color1}
            div.js-ticket-table-body div.js-ticket-data-row div.js-ticket-table-body-col .js-ticket-title-anchor{text-decoration:none;display: inline-block;height: 25px;width: 95%;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
            div.js-ticket-table-body div.js-ticket-data-row div.js-ticket-table-body-col {color:grey}
            div.js-ticket-table-body div.js-ticket-data-row div.js-ticket-table-body-col a.js-tk-button {border: 1px solid $color5;background: $color7;}
            div.js-ticket-table-body div.js-ticket-data-row div.js-ticket-table-body-col a.js-tk-button:hover {border-color: $color1;text-decoration:none;}
            div.js-ticket-search-heading-wrp div.js-ticket-heading-right a.js-ticket-add-download-btn{background: $color2;color: $color7;border: 1px solid $color5;}
            div.js-ticket-search-heading-wrp div.js-ticket-heading-right a.js-ticket-add-download-btn:hover{border-color: $color1;}
            div.js-ticket-table-body div.js-ticket-data-row div.js-ticket-table-body-col:first-child a{color: $color4;font-weight: bold;}
            div.js-per-subheading {background-color: $color2;color: $color7;border: 1px solid $color5;}
            div.js-per-wrapper div.js-per-data{background-color: $color7;border: 1px solid $color5;}
            div#js-tk-per-ajax-bottom-border{display: inline-block;width: 96%; margin: 6% 2%; float: left; border-bottom: 1px solid $color5;}
            div.js-per-wrapper div.js-per-data label{color: $color4;display: inline-block; padding-left: 2px;margin-bottom:0px;padding-top:2px;line-height:18px;text-transform:capitalize;}

            /*staff*/
            select.js-ticket-select-field{float: left;width: 100%;border-radius: 0px;background: url(../../images/selecticon.png) 96% / 4% no-repeat;min-height: 50px;margin-bottom: 0px;background-color: $color7;border: 1px solid $color5;}
            div.js-ticket-table-body div.js-ticket-data-row div.js-ticket-table-body-col.js-ticket-first-child{text-align: left;padding: 15px;font-weight: bold;color: $color4;}
            div.js-ticket-add-form-wrapper div.js-ticket-from-field-wrp div.js-ticket-from-field.js-ticket-from-field-wrp-full-width select#status{float: left;width: 100%;border-radius: 0px;background-image: url(../../../include/images/selecticon.png);background-repeat: no-repeat;background-position: calc(100% - 12px);-webkit-appearance: none;-moz-appearance: none;appearance: none;background-size: 13px; min-height: 50px;padding:10px;}
            div.js-ticket-add-form-wrapper div.js-ticket-from-field-wrp.js-ticket-from-field-wrp-full-width div.js-ticket-from-field select#status{float: left;width: 100%;border-radius: 0px;background-image: url(../../../include/images/selecticon.png);background-repeat: no-repeat;background-position: calc(100% - 12px);-webkit-appearance: none;-moz-appearance: none;appearance: none;background-size: 13px; min-height: 50px;padding:10px;}
            div.js-ticket-select-user-btn a#userpopup {background-color: $color1;color: $color7;border: 1px solid $color5;border-left: none;}
            div#userpopup{background-color: $color7;}
            div.jsst-popup-header {background: $color1;color: $color7;}
            {text-align: center;border-top: 1px solid #e0dce0;width: 94%;margin: 0px 3%;margin-top: 20px;}
            div#records div.js-ticket-table-wrp div.js-ticket-table-header{float: left;width: 100%;border: 0 !important;border-bottom: 1px solid $color5 !important;}
            div.js-ticket-select-user-btn a#userpopup:hover {border:1px solid $color2;}
            
            /* department*/
            div.js-ticket-form-btn-wrp a.js-ticket-cancel-button {background: $color2;color: $color7;border: 1px solid $color5;}
            div.js-ticket-form-btn-wrp input.js-ticket-save-button:hover {border-color: $color2;}
            div.js-ticket-form-btn-wrp a.js-ticket-cancel-button:hover {border-color: $color1;}
            div.js-ticket-append-signature-wrp div.js-ticket-append-field-title {color: $color2;} 
            div.js-ticket-append-signature-wrp div.js-ticket-signature-radio-box {border: 1px solid $color5;background-color: $color7;color: $color4;}
            div.js-ticket-radio-btn-wrp{background-color: $color7;border: 1px solid $color5;color: $color4;}
            div.js-ticket-table-body div.js-ticket-data-row div.js-ticket-table-body-col.js-ticket-first-child a{color: $color1;}
            
            /*popup*/
            div.js-ticket-search-top div.js-ticket-search-right div.js-ticket-search-btn-wrp input.js-ticket-search-btn {background: $color1;color: $color7;border: 1px solid $color5;}
            div.js-ticket-search-top div.js-ticket-search-right div.js-ticket-search-btn-wrp input.js-ticket-reset-btn {background: $color2;color: $color7;border: 1px solid $color5;}
            span.jsst_userlink.selected {background: $color2;color: $color7;}
            /*category*/
            div.js-ticket-table-body div.js-ticket-data-row div.js-ticket-table-body-col:first-child a.js-ticket-title-anchor {color: $color1;}
            div.js-ticket-table-body div.js-ticket-data-row div.js-ticket-table-body-col span.js-ticket-title {display: inline;}

            div.js-ticket-add-form-wrapper div.js-ticket-from-field-wrp div.js-ticket-from-field textarea#metadesc {background-color: $color7;border: 1px solid $color5;color: $color4;}
            span.js-ticket-sub-fields {background-color: $color7 !important;border: 1px solid $color5;color: $color4;}
            
            /*mail*/
            div.field-calendar div.input-append button#startdate_btn{border:1px solid $color5;}
            div.field-calendar div.input-append button#enddate_btn{ border:1px solid $color5;}
            div.js-ticket-mails-btn-wrp div.js-ticket-mail-btn a.js-add-link {background-color: $color3;border: 1px solid $color5;color: $color2;}
            div.js-ticket-mails-btn-wrp div.js-ticket-mail-btn a.js-add-link:hover {background-color: $color1;border: 1px solid $color2;color: $color7;}
            div.js-ticket-mails-btn-wrp div.js-ticket-mail-btn a.js-add-link.active {background-color: $color1 !important;border: 1px solid $color2 !important;color: $color7 !important;}
            div.js-ticket-detail-left div.js-ticket-user-name-wrp{color: $color4;}
            div.js-ticket-detail-left div.js-ticket-user-email-wrp{color: $color4;font-size: 15px;}
            div.js-ticket-detail-box div.js-ticket-detail-right{border-left: 1px solid $color5;}
            div.js-ticket-detail-box div.js-ticket-detail-right div.js-ticket-rows-wrp{color: $color2;}
            div.js-ticket-detail-right-null-border{border-left: none!important;}

            /*controlpanel*/
            div#js-dash-menu-link-wrp {border: 1px solid $color5;}
            div.js-section-heading {border-bottom: 1px solid $color5;color: $color2;}
            a.js-ticket-dash-menu {border-bottom: 1px solid $color5;}
            a.js-ticket-dash-menu span.js-ticket-dash-menu-text {color: $color4;}
            a.js-ticket-dash-menu span.js-ticket-dash-menu-text:hover {color: $color2;}
            .js-support-ticket-cont {border: 1px solid $color5;}
            .js-support-ticket-cont .js-support-ticket-box {background: #f6f6f6;border: 1px solid $color5;box-shadow: 3px solid rgba(0,0,0,0.5);}
            .js-ticket-count {background: $color7;border: 1px solid $color5;}
            .js-ticket-count a.js-ticket-link {background-color: $color7;border: 1px solid $color5;}
            .js-ticket-count div.js-ticket-link a.js-ticket-link:hover {box-shadow: 0 1px 3px 0 rgba(60,64,67,0.302), 0 4px 8px 3px rgba(60,64,67,0.149);}
            .js-ticket-count div.js-ticket-link a.js-ticket-link div.js-ticket-cricle-wrp div.loader {box-shadow: 0 10px 25px 0 rgba(0, 0, 0, .08) !important;}
            .js-ticket-count div.js-ticket-link a.js-ticket-link div.js-ticket-cricle-wrp div.loader div.loader-bg {border-color: #d6dadc;}
            .js-ticket-count div.js-ticket-link.js-ticket-open:hover a.js-ticket-link {border-color: #14a76c;}
            .js-ticket-count div.js-ticket-link.js-ticket-open a.js-ticket-link div.js-ticket-cricle-wrp div.loader div.loader-spinner {border-color: #14a76c;}
            .js-ticket-count div.js-ticket-link.js-ticket-open div.js-ticket-link-text {color: #14a76c;}
            .js-ticket-count div.js-ticket-link.js-ticket-answer:hover a.js-ticket-link {border-color: #d89922;}
            .js-ticket-count div.js-ticket-link.js-ticket-answer a.js-ticket-link div.js-ticket-cricle-wrp div.loader div.loader-spinner {border-color: #d89922;}
            .js-ticket-count div.js-ticket-link.js-ticket-answer div.js-ticket-link-text {color: #d89922;}
            .js-ticket-count div.js-ticket-link.js-ticket-overdue:hover a.js-ticket-link {border-color: #ff652f;}
            .js-ticket-count div.js-ticket-link.js-ticket-overdue a.js-ticket-link div.js-ticket-cricle-wrp div.loader div.loader-spinner {border-color: #ff652f;}
            .js-ticket-count div.js-ticket-link.js-ticket-overdue div.js-ticket-link-text {color: #ff652f;}
            .js-ticket-count div.js-ticket-link.js-ticket-close:hover a.js-ticket-link {border-color: #e92d3e;}
            .js-ticket-count div.js-ticket-link.js-ticket-close a.js-ticket-link div.js-ticket-cricle-wrp div.loader div.loader-spinner {border-color: #e92d3e;}
            .js-ticket-count div.js-ticket-link.js-ticket-close div.js-ticket-link-text {color: #e92d3e;}
            .js-ticket-count div.js-ticket-link.js-ticket-allticket:hover a.js-ticket-link {border-color: #5ab9ea;}
            .js-ticket-count div.js-ticket-link.js-ticket-allticket a.js-ticket-link div.js-ticket-cricle-wrp div.loader div.loader-spinner {border-color: #5ab9ea;}
            .js-ticket-count div.js-ticket-link.js-ticket-allticket div.js-ticket-link-text {color: #5ab9ea;}
            div.js-combine-search-wrapper {border: 1px solid $color5;}
            div.js-ticket-haeder {background-color: $color1;color: $color7;}
            div.js-ticket-haeder a.js-ticket-header-link:hover {color: $color1;}
            div.js-ticket-latest-ticket-wrapper {border: 1px solid $color5;}
            div.js-ticket-haeder div.js-ticket-header-txt {color: $color7;}
            div.js-ticket-haeder a.js-ticket-header-link {color: $color2;background: $color7;border: 1px solid $color5;}
            div.js-ticket-latest-tickets-wrp div.js-ticket-row {border-bottom: 1px dashed $color5;}
            div.js-ticket-latest-tickets-wrp div.js-ticket-row div.js-ticket-first-left div.js-ticket-ticket-subject div.js-ticket-data-row {color: $color4;}
            div.js-ticket-latest-tickets-wrp div.js-ticket-row div.js-ticket-first-left div.js-ticket-ticket-subject div.js-ticket-data-row.name {color: $color2;}
            div.js-ticket-latest-tickets-wrp div.js-ticket-row div.js-ticket-first-left div.js-ticket-ticket-subject div.js-ticket-data-row span.js-ticket-title {color: $color2;}
            div.js-ticket-latest-tickets-wrp div.js-ticket-row div.js-ticket-second-left span.js-ticket-status {border: 1px solid $color5;background: $color7;}
            div.js-ticket-latest-tickets-wrp div.js-ticket-row div.js-ticket-third-left {color: $color4;}
            .js-ticket-data-list-wrp {border: 1px solid $color5;}
            .js-ticket-data-list .js-ticket-data {border-bottom: 1px dashed $color5;}
            .js-ticket-data-list .js-ticket-data .js-ticket-data-tit {color: $color4;}
            .js-ticket-data-list .js-ticket-data .js-ticket-data-tit:hover {color: $color2;}
            .js-ticket-data-list .js-ticket-data .js-ticket-data-btn {border: 1px solid $color1;background: $color3;color: $color1;}
            .js-ticket-data-list .js-ticket-data .js-ticket-data-btn:hover {border-color: $color2;background: $color7;color: $color2;}
            div#js-pm-graphtitle {border: 1px solid $color5;background-color: $color7;border-bottom: 1px solid $color5;color: $color2;}
            div.js-ticket-latest-ticket-header {background-color: $color1;color: $color7;}
            div#js-ticket-main-downloadallbtn div.js-ticket-download-btn a.js-ticket-download-btn-style {background-color: $color1;color: $color7;border-color: $color1;}
            div#js-ticket-main-downloadallbtn div.js-ticket-download-btn a.js-ticket-download-btn-style:hover {border-color: $color2;}
            .js-support-ticket-cont .js-support-ticket-box .js-support-ticket-title {color: $color2;}
            .js-support-ticket-cont .js-support-ticket-box .js-support-ticket-desc {color: $color2;}
            .js-support-ticket-cont .js-support-ticket-box .js-support-ticket-btn {color: $color7;background: $color2;border-bottom: 3px solid rgba(0,0,0,0.5);}
            .js-support-ticket-cont .js-support-ticket-box .js-support-ticket-btn:hover{background: $color1;}
            div.js-ticket-latest-tickets-wrp div.js-ticket-row div.js-ticket-first-left div.js-ticket-ticket-subject div.js-ticket-data-row a.js-ticket-data-link {color: $color1;}
            div.js-ticket-latest-tickets-wrp div.js-ticket-row div.js-ticket-fourth-left span.js-ticket-priorty {color: $color7;}
            div.js-ticket-latest-tickets-wrp div.js-ticket-row div.js-ticket-fourth-left div.js-ticket-header-priorty {color: $color7;}
            div#jl_pagination div#jl_pagination_pageslink ul li a {margin: 0 1px; border: 1px solid $color5;padding: 12px 15px;line-height: initial;font-size: 16px;}

            /*staff report*/
            div.js-admin-report-box-wrapper div.js-admin-box{background:$color7;border:1px solid #cccccc;padding:0px;width: calc(100% / 5 - 5px);margin: 0px 2.5px; float: left;}
            div.js-admin-report-box-wrapper div.js-admin-box.box1 div.js-admin-box-content div.js-admin-box-content-number{color:#1EADD8;}
            div.js-admin-report-box-wrapper div.js-admin-box.box2 div.js-admin-box-content div.js-admin-box-content-number{color:#179650;}
            div.js-admin-report-box-wrapper div.js-admin-box.box3 div.js-admin-box-content div.js-admin-box-content-number{color:#D98E11;}
            div.js-admin-report-box-wrapper div.js-admin-box.box4 div.js-admin-box-content div.js-admin-box-content-number{color:#DB624C;}
            div.js-admin-report-box-wrapper div.js-admin-box.box5 div.js-admin-box-content div.js-admin-box-content-number{color:#5F3BBB;}
            div.js-admin-report-box-wrapper div.js-admin-box.box1 div.js-admin-box-label{height:20px;background:#1EADD8;}
            div.js-admin-report-box-wrapper div.js-admin-box.box2 div.js-admin-box-label{height:20px;background:#179650;}
            div.js-admin-report-box-wrapper div.js-admin-box.box3 div.js-admin-box-label{height:20px;background:#D98E11;}
            div.js-admin-report-box-wrapper div.js-admin-box.box4 div.js-admin-box-label{height:20px;background:#DB624C;}
            div.js-admin-report-box-wrapper div.js-admin-box.box5 div.js-admin-box-label{height:20px;background:#5F3BBB;}
            a.js-admin-report-wrapper div.js-admin-overall-report-type-wrapper{box-shadow: 0px 0px 10px #aaaaaa;border-bottom:8px solid #6AA108;color:#6AA108;margin:10px 0px;background:url(../../images/report/overall.png)  98% center no-repeat #EAF1DD;}
            a.js-admin-report-wrapper div.js-admin-staff-report-type-wrapper{box-shadow: 0px 0px 10px #aaaaaa;border-bottom:8px solid #1EADD8;color:#1EADD8;margin:10px 0px;background:url(../../images/report/staffbox.png)  98% center no-repeat #EEF9FD;}
            a.js-admin-report-wrapper div.js-admin-user-report-type-wrapper{box-shadow: 0px 0px 10px #aaaaaa;border-bottom:8px solid #D98E11;color:#D98E11;margin:10px 0px;background:url(../../images/report/userbox.png)  98% center no-repeat #FFF5EB;}
            div.js-admin-staff-wrapper{display: inline-block;width:100%;background:$color7;margin-top:10px;margin-bottom:5px;border:1px solid #cccccc;}
            div.js-admin-staff-wrapper div.js-report-staff-name{display: block;padding:3px 0px;font-weight: bold;font-size: 15px;color: $color1;margin-bottom:5px;text-decoration: underline;}
            div.js-admin-staff-wrapper div.js-departmentname{font-weight: bold;font-size: 18px;color:#666666; margin: 15px 0px;}
            div.js-admin-staff-wrapper div.js-report-staff-username{display: block;padding:3px 0px;font-size: 14px;color:#666666;}
            div.js-admin-staff-wrapper div.js-report-staff-email{display: block;padding:3px 0px;font-size: 14px;color:#666666;}
            div.js-admin-staff-wrapper div.js-admin-report-box{background:#f1f1f1;border:1px solid #cccccc;margin-left:8px;padding:0px;padding-top:10px;width: calc(100% / 5 - 8px);float: left;}
            div.js-admin-staff-wrapper div.js-admin-report-box span.js-report-box-number{color:#989898;display: block;font-size:22px;font-weight: bold;text-align: center;margin:5px 0px 10px 0px;}
            div.js-admin-staff-wrapper div.js-admin-report-box span.js-report-box-title{color:#989898;display: block;font-size:12px;text-align: center;padding:5px 4px 10px 4px;white-space: nowrap;text-overflow:ellipsis;overflow: hidden;}
            div.js-admin-staff-wrapper div.js-admin-report-box.box1 div.js-report-box-color{height:5px;background:#1EADD8;}
            div.js-admin-staff-wrapper div.js-admin-report-box.box2 div.js-report-box-color{height:5px;background:#179650;}
            div.js-admin-staff-wrapper div.js-admin-report-box.box3 div.js-report-box-color{height:5px;background:#D98E11;}
            div.js-admin-staff-wrapper div.js-admin-report-box.box4 div.js-report-box-color{height:5px;background:#DB624C;}
            div.js-admin-staff-wrapper div.js-admin-report-box.box5 div.js-report-box-color{height:5px;background:#5F3BBB;}
            table.js-admin-report-tickets tr th{background:#cccccc;color:#333333;padding:8px;font-size:18px;}
            table.js-admin-report-tickets tr td.overflow{white-space: nowrap;overflow: hidden;text-overflow:ellipsis;text-align: left;}
            table.js-admin-report-tickets tr td{text-align: center;background:$color7;padding:8px;}
            a#js-admin-ticketviaemail{display: block;float:left;border:1px solid #666555;padding:8px 15px 8px 40px;background:url(../../images/button_ticketviaemail.png);background-size:100% 100%;color:$color7;font-weight: bold;border-radius: 4px;text-decoration: none;position: relative;}
            div#js-admin-ticketviaemail-msg.server-error{background:#FEEFB3;color:#B98324;border:1px solid #B98324;}
            div#js-admin-ticketviaemail-msg.imap-error{background:#FEEFB3;color:#B98324;border:1px solid #B98324;}
            div#js-admin-ticketviaemail-msg.email-error{background:#FEEFB3;color:#B98324;border:1px solid #B98324;}
            div#js-admin-ticketviaemail-msg.no-error{background:#DFF2BF;color:#387B00;border:1px solid #387B00;}
            div#no_message{background: #f6f6f6 none repeat scroll 0 0; border: 1px solid #d4d4d5; color: #723776; display: inline-block; font-size: 15px; left: 50%; min-width: 80%; padding: 15px 20px; position: absolute; text-align: center; top: 50%; transform: translate(-50%, -50%); }
            div.js-admin-report-box-wrapper div.js-admin-box div.js-admin-box-content div.js-admin-box-content-label{text-align: right;font-size:12px;padding:0px;margin-top:5px;color:#989898;white-space: nowrap;overflow: hidden;text-overflow:ellipsis;}
            div.js-ticket-table-body div.js-ticket-data-row div.js-ticket-table-body-col span.js-ticket-priority {color: $color7;}
            div.js-ticket-profile-wrp div.js-ticket-profile-left div.js-ticket-user-img-wrp {background-color: $color7;border: 1px solid $color5;}
            label.js-ticket-file-upload-label {background-color: $color2;border: 1px solid $color2;color: $color7;}
            #ticket_date_start_btn{background-color: $color7 !important;border-color: $color1 !important;color: $color1 !important;}
            #ticket_date_start_btn:hover{background-color: $color7 !important;border-color: $color2 !important;color: $color2 !important;}
            #ticket_date_end_btn{background-color: $color7 !important;border-color: $color1 !important;color: $color1 !important;}
            #ticket_date_end_btn:hover{background-color: $color7 !important;border-color: $color2 !important;color: $color2 !important;}
            .js-ticket-btn-box .js-button .js-tkt-det-actn-btn span {color: $color4;}
  

            @media (max-width: 480px){
                  
            }
            @media (min-width: 481px) and (max-width: 667px){

                
            }
        ";
      $language = Factory::getLanguage();
      if($language->isRTL()){
            $style .= "
                  

            ";
      }
      $document = Factory::getDocument();
      $document->addStyleDeclaration($style);
?>
