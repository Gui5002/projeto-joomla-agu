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
defined('_JEXEC') or die('Not Allowed');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;    

class customfields {
    
    function formCustomFields($field , $obj_id, $obj_params , $isadmin ) { // $obj_id, $obj_params
        if($field->isuserfield != 1){
            return false;
        }
        $html = '';
        $flag = false;
        if($isadmin){
            $div1 = 'js-form-wrapper';
            $div2 = 'js-title';
            $div3 = 'js-value';
        }else{
            if($field->size == 100){
                $div1 = 'js-form-wrapper fullwidth';
                $div2 = 'js-form-title';
                $div3 = 'js-form-value';
            }elseif($field->size == 50){
                $div1 = 'js-form-wrapper';
                $div2 = 'js-form-title';
                $div3 = 'js-form-value';
            }
        }

        $required = $field->required;
        $showlabelforterm = false;
        if($field->userfieldtype == 'termsandconditions'){
            if (isset($obj_id) && is_numeric($obj_id) || $isadmin) {
                return false;
            }
            $required = 1;
            $showlabelforterm = true;
        }
        $cssclass = "";
        $class = "";
        $margin_bottom = "";
        $add_margin_bottom = "";

        $html .= '<div class="' . $div1 . '">
                    <div class="' . $div2 . '">';
                        if ($required == 1) {
                            if($showlabelforterm){
                                $html .= '<label for="' . $field->field . '" id="foruf_checkbox1">';
                            }
                            $html .= Text::_($field->fieldtitle) . '<font color="red">*</font>';
                            if($showlabelforterm){
                                $html .= '</label>';
                            }
                            if ($field->userfieldtype == 'email')
                                $cssclass = "required validate-email";
                            else
                                $cssclass = "required";
                        }else {
                            $html .= Text::_($field->fieldtitle);
                            if ($field->userfieldtype == 'email')
                                $cssclass = "email";
                            else
                                $cssclass = "";
                        }
        $html .= '  </div>
                    <div class="' . $div3 . '">';
                        //$maxlength = $field->maxlength ? "'maxlength' => '" . $field->maxlength : "";
                        $fvalue = "";
                        $value = "";
                        $userdataid = "";
                        if (isset($obj_id)) {
                            if($obj_params != ''){ // to handle not null check for json decode
                                $userfielddataarray = json_decode($obj_params);
                                $uffield = $field->field;
                                if (isset($userfielddataarray->$uffield) || !empty($userfielddataarray->$uffield)) {
                                    $value = $userfielddataarray->$uffield;
                                } else {
                                    $value = '';
                                }
                            }
                        }
                        if (isset($obj_params) && $obj_params != '') {
                            $userfielddataarray = json_decode($obj_params);
                            $uffield = $field->field;
                            if (isset($userfielddataarray->$uffield) || !empty($userfielddataarray->$uffield)) {
                                $value = $userfielddataarray->$uffield;
                            } else {
                                $value = '';
                            }
                        }
                        switch ($field->userfieldtype) {
                            case 'text':
                            case 'email':
                                $html .= $this->text($field->field, $value, array('class' => "inputbox js-form-input-field one $cssclass", 'data-validation' => $cssclass, 'size' => $field->size, 'maxlength' => $field->maxlength));
                                break;
                            case 'date':
                                $dash = '-';
                                $dateformat = JSSupportTicketModel::getJSModel('config')->getConfigurationByName("date_format");
                                $firstdash = getJSTicketPHPFunctionsClass()->jsticket_strpos($dateformat, $dash, 0);
                                $firstvalue = getJSTicketPHPFunctionsClass()->jsticket_substr($dateformat, 0, $firstdash);
                                $firstdash = $firstdash + 1;
                                $seconddash = getJSTicketPHPFunctionsClass()->jsticket_strpos($dateformat, $dash, $firstdash);
                                $secondvalue = getJSTicketPHPFunctionsClass()->jsticket_substr($dateformat, $firstdash, $seconddash - $firstdash);
                                $seconddash = $seconddash + 1;
                                $thirdvalue = getJSTicketPHPFunctionsClass()->jsticket_substr($dateformat, $seconddash, getJSTicketPHPFunctionsClass()->jsticket_strlen($dateformat) - $seconddash);
                                $js_dateformat = '%' . $firstvalue . $dash . '%' . $secondvalue . $dash . '%' . $thirdvalue;
                                
                                $html .= HTMLHelper::_('calendar', $value, $field->field, $field->field,$js_dateformat, array('class' => 'inputbox js-ticket-calender $cssclass', 'size' => '10', 'maxlength' => '19'));
                                break;
                            case 'textarea':
                                $html .= $this->textarea($field->field, $value, array('class' => "inputbox one js-ticket-textarea $cssclass", 'data-validation' => $cssclass, 'rows' => $field->rows, 'cols' => $field->cols));
                                break;
                            case 'checkbox':
                                if (!empty($field->userfieldparams)) {
                                    $comboOptions = array();
                                    $obj_option = json_decode($field->userfieldparams);
                                    $total_option = getJSTicketPHPFunctionsClass()->jsticket_count($obj_option);
                                    $i = 0;
                                    $valuearray = getJSTicketPHPFunctionsClass()->jsticket_explode(', ',$value);
                                    $html .= '<div class="js-ticket-signature-radio-box-wrp">';
                                        foreach ($obj_option AS $option) {                        
                                            $check = '';
                                            if(is_array($valuearray) && in_array($option, $valuearray)){
                                                $check = 'checked';
                                            }else if($option == $valuearray){
                                                $check = 'checked';
                                            }
                                            if($field->size == 50){ 
                                                if($total_option > 2)
                                                {
                                                    $add_margin_bottom = 'js-ticket-margin-bottom';
                                                }
                                            }
                                            $html .= '<div class="js-ticket-signature-radio-box js-ticket-white-background '.$add_margin_bottom.' ">';
                                                $html .= '<input type="checkbox" ' . $check . ' class="radiobutton js-ticket-append-radio-btn" value="' . $option . '" id="' . $field->field . '_' . $i . '" name="' . $field->field . '[]">';
                                                $html .= '<label for="' . $field->field . '_' . $i . '" id="foruf_checkbox1">' . $option . '</label>';
                                            $html .= '</div>';
                                            $i++;
                                        }
                                    $html .= '</div>';
                                } else {
                                    $comboOptions = array('1' => $field->fieldtitle);
                                    $html .= $this->checkbox($field->field, $comboOptions, $value, array('class' => 'radiobutton js-ticket-append-radio-btn'));
                                }
                                break;
        
                            case 'radio':
                                $comboOptions = array();
                                if (!empty($field->userfieldparams)) {
                                    $obj_option = json_decode($field->userfieldparams);
                                    $i = 0;
                                    $jsFunction = '';
                                    if ($field->depandant_field != null) {
                                        $jsFunction = "getDataForDepandantField('" . $field->field . "','" . $field->depandant_field . "',2,'". Factory::getSession()->getFormToken() ."');";
                                    }
                                    $valuearray = getJSTicketPHPFunctionsClass()->jsticket_explode(', ',$value);
                                    $html .= '<div class="js-ticket-signature-radio-box-wrp">';
                                    foreach ($obj_option AS $option) {
                                        $check = '';
                                        if(in_array($option, $valuearray)){
                                            $check = 'checked';
                                        }
                                        $html .= '<div class="js-ticket-radio-box js-ticket-white-background">';
                                            $html .= '<input type="radio" ' . $check . ' class="radiobutton js-ticket-radio-btn $cssclass" value="' . $option . '" id="' . $field->field . '_' . $i . '" name="' . $field->field . '" data-validation ="'.$cssclass.'" onclick = "'.$jsFunction.'"> ';
                                            $html .= '<label for="' . $field->field . '_' . $i . '" id="foruf_checkbox1">' . $option . '</label>';
                                        $html .= '</div>';
                                        $i++;
                                    }
                                    $html .= '</div>';
                                    
                                }
                                
                                break;
                            case 'combo':
                                $comboOptions = array();
                                if (!empty($field->userfieldparams)) {
                                    $obj_option = json_decode($field->userfieldparams);
                                    foreach ($obj_option as $opt) {
                                        $comboOptions[] = (object) array('id' => $opt, 'text' => $opt);
                                    }
                                }
                                //code for handling dependent field
                                $jsFunction = '';
                                if ($field->depandant_field != null) {
                                    $jsFunction = "getDataForDepandantField('" . $field->field . "','" . $field->depandant_field . "',1,'". Factory::getSession()->getFormToken() ."');";
                                }
                                //end
                                $class = 'js-ticket-halfwidth-select';
                                if($field->size == 100){
                                    $class = 'js-ticket-fullwidth-select';
                                }
                                $html .= $this->select($field->field, $comboOptions, $value, Text::_('Select') . ' ' . $field->fieldtitle, array('data-validation' => $cssclass, 'onchange' => $jsFunction, 'class' => "inputbox js-form-select-field one $cssclass $class"));
                                break;
       
                            case 'depandant_field':
                                $comboOptions = array();
                                if ($value != null) {
                                    if (!empty($field->userfieldparams)) {
                                        $obj_option = $this->getDataForDepandantFieldByParentField($field->field, $userfielddataarray);
                                        foreach ($obj_option as $opt) {
                                            $comboOptions[] = (object) array('id' => $opt, 'text' => $opt);
                                        }
                                    }
                                }
                                //code for handling dependent field
                                $jsFunction = '';
                                if ($field->depandant_field != null) {
                                    $jsFunction = "getDataForDepandantField('" . $field->field . "','" . $field->depandant_field . "',1,'". Factory::getSession()->getFormToken() ."');";
                                }
                                //end
                                $class = 'js-ticket-halfwidth-select';
                                if($field->size == 100){
                                    $class = 'js-ticket-fullwidth-select';
                                }
                                $html .= $this->select($field->field, $comboOptions, $value, Text::_('Select') . ' ' . $field->fieldtitle, array('data-validation' => $cssclass, 'onchange' => $jsFunction, 'class' => "inputbox js-form-select-field one $cssclass $class"));
                                break;

                            case 'multiple':
                                $comboOptions = array();
                                if (!empty($field->userfieldparams)) {
                                    $obj_option = json_decode($field->userfieldparams);
                                    foreach ($obj_option as $opt) {
                                        $comboOptions[] = (object) array('id' => $opt, 'text' => $opt);
                                    }
                                }
                                $array = $field->field;
                                $array .= '[]';
                                $valuearray = getJSTicketPHPFunctionsClass()->jsticket_explode(', ', $value);
                                $class = 'js-ticket-halfwidth-select';
                                if($field->size == 100){
                                    $class = 'js-ticket-fullwidth-select';
                                }
                                $html .= $this->select($array, $comboOptions, $valuearray, Text::_('Select') . ' ' . $field->fieldtitle, array('data-validation' => $cssclass, 'multiple' => 'multiple', 'class' => "inputbox js-form-select-field js-form-multi-select-field  one $cssclass $class"));
                                break;
                            case 'file':
                                $html .= '<span class="js-attachment-file-box js-attachment-custom-file-box">';
                                    $html .= '<input type="file" name="'.$field->field.'"  class="'.$cssclass.'" id="'.$field->field.'"/>';
                                    $fileext = JSSupportTicketModel::getJSModel('config')->getConfigurationByName('fileextension');
                                $html .= '</span>';
                                $html .= '<span id="js_cust_file_ext">'.Text::_('Files').'&nbsp;('.$fileext.')</span>';
                                    if($value != null){
                                        $html .= $this->hidden($field->field.'_1', 0);
                                        $html .= $this->hidden($field->field.'_2',$value);
                                        $jsFunction = "deleteCutomUploadedFile('".$field->field."_1')";
                                        if(Factory::getApplication()->isClient('administrator')){
                                            $html .='<span class='.$field->field.'_1>'.$value.'( ';
                                            $html .= "<a href='#' onClick=".$jsFunction." >". Text::_('Delete')."</a>";
                                            $html .= ' )</span>';
                                        }
                                    }
                                break;
                            case 'termsandconditions':
                                if (isset($obj_id) && !is_numeric($obj_id)) {
                                    break;
                                }
                                if (!empty($field->userfieldparams)) {
                                    $obj_option = json_decode($field->userfieldparams,true);

                                    $url = $obj_option['termsandconditions_link'];
                                    if( isset($obj_option['termsandconditions_linktype']) && $obj_option['termsandconditions_linktype'] == 2){
                                        // $url  = get_permalink($obj_option['termsandconditions_page']);
                                        $url = Route::_('index.php?option=com_content&view=article&id='.$obj_option['termsandconditions_page'].'&Itemid=');
                                    }

                                    $link_start = '<a href="' . $url . '" class="termsandconditions_link_anchor" target="_blank" >';
                                    $link_end = '</a>';

                                    if(getJSTicketPHPFunctionsClass()->jsticket_strstr($obj_option['termsandconditions_text'], '[link]') && getJSTicketPHPFunctionsClass()->jsticket_strstr($obj_option['termsandconditions_text'], '[/link]')){
                                        $label_string = getJSTicketPHPFunctionsClass()->jsticket_str_replace('[link]', $link_start, $obj_option['termsandconditions_text']);
                                        $label_string = getJSTicketPHPFunctionsClass()->jsticket_str_replace('[/link]', $link_end, $label_string);
                                    }else{
                                        $label_string = $obj_option['termsandconditions_text'].'&nbsp;'.$link_start.$field->fieldtitle.$link_end;
                                    }
                                    $c_field_required = '';
                                    if($field->required == 1){
                                        $c_field_required = 'required';
                                    }
                                    // ticket terms and conditonions are required.
                                    if($field->fieldfor == 1){
                                        $c_field_required = 'required';
                                    }

                                    $html .= '<div class="js-ticket-custom-terms-and-condition-box jsst-formfield-radio-button-wrap">';
                                    $html .= '<input type="checkbox" class="radiobutton js-ticket-append-radio-btn" value="1" id="' . $field->field . '" name="' . $field->field . '" required="'.$c_field_required.'" data-validation="'.$c_field_required.'">';
                                    $html .= '<label for="' . $field->field . '" id="foruf_checkbox1">' . $label_string . '</label>';
                                    $html .= '</div>';
                                }
                            break;

                        }
        
        $html .= '  </div></div>';


        echo $html;
    }

    function formCustomFieldsForSearch($field, &$col, $filter_params, $isadmin = 0) { // $filter_params
        if ($field->isuserfield != 1 || $field->userfieldtype == 'termsandconditions')
            return false;
        $cssclass = "";
        $class = "";
        $margin_bottom = "";
        $add_margin_bottom = "";
        $html = '';
        $i = $col;
        $i++;
        $col++;
        $required = $field->required;
        if($col != 2){
            $html .= '</div>';
        }
        $html .= '<div class="js-col-md-3 js-filter-field-wrp">';
        
        if($isadmin == 1){
            $html = ''; // only field send
        }
        $readonly = ''; //$field->readonly ? "'readonly => 'readonly'" : "";
        $maxlength = ''; //$field->maxlength ? "'maxlength' => '".$field->maxlength : "";
        $fvalue = "";
        $value = null;
        $userdataid = "";
        $userfielddataarray = array();
        if (isset($filter_params)) {
            $userfielddataarray = $filter_params;
            $uffield = $field->field;
            //had to user || oprator bcz of radio buttons

            if (isset($userfielddataarray[$uffield]) || !empty($userfielddataarray[$uffield])) {
                $value = $userfielddataarray[$uffield];
            } else {
                $value = '';
            }
        }
        switch ($field->userfieldtype) {
            case 'text':
            case 'email':
            case 'file':
                $html .= $this->text($field->field, $value, array('class' => "inputbox one js-ticket-input-field $cssclass", 'data-validation' => $cssclass,'placeholder' =>$field->fieldtitle, 'size' => $field->size, 'maxlength' => $maxlength));
                break;
            case 'date':
                $dash = '-';
                $dateformat = JSSupportTicketModel::getJSModel('config')->getConfigurationByName("date_format");
                $firstdash = getJSTicketPHPFunctionsClass()->jsticket_strpos($dateformat, $dash, 0);
                $firstvalue = getJSTicketPHPFunctionsClass()->jsticket_substr($dateformat, 0, $firstdash);
                $firstdash = $firstdash + 1;
                $seconddash = getJSTicketPHPFunctionsClass()->jsticket_strpos($dateformat, $dash, $firstdash);
                $secondvalue = getJSTicketPHPFunctionsClass()->jsticket_substr($dateformat, $firstdash, $seconddash - $firstdash);
                $seconddash = $seconddash + 1;
                $thirdvalue = getJSTicketPHPFunctionsClass()->jsticket_substr($dateformat, $seconddash, getJSTicketPHPFunctionsClass()->jsticket_strlen($dateformat) - $seconddash);
                $js_dateformat = '%' . $firstvalue . $dash . '%' . $secondvalue . $dash . '%' . $thirdvalue;
                
                $html .= HTMLHelper::_('calendar', $value, $field->field, $field->field, $js_dateformat, array('class' => 'inputbox js-ticket-calender', 'size' => '10', 'maxlength' => '19','placeholder' =>$field->fieldtitle));
                break;
            case 'editor':
                $html .= wp_editor(isset($value) ? $value : '', $field->field, array('media_buttons' => false, 'data-validation' => $cssclass));
                break;
            case 'textarea':
                $html .= $this->textarea($field->field, $value, array('class' => "inputbox one js-ticket-textarea $cssclass", 'data-validation' => $cssclass, 'rows' => $field->rows, 'cols' => $field->cols, $readonly));
                break;
            case 'checkbox':
                if (!empty($field->userfieldparams)) {
                    $comboOptions = array();
                    $obj_option = json_decode($field->userfieldparams);
                    $total_option = getJSTicketPHPFunctionsClass()->jsticket_count($obj_option);
                    if(empty($value))
                        $value = array();
                     $html .= '<div class="js-ticket-signature-radio-box-wrp">';
                        foreach ($obj_option AS $option) {
                            if( $option == $value){
                                $check = 'checked="true"';
                            }else{
                                $check = '';
                            }
                            if($total_option > 3)
                            {
                                $margin_bottom = 'js-ticket-margin-bottom';
                            }
                            $html .= '<div class="js-ticket-radio-box '.$margin_bottom.' js-ticket-white-background">';
                                $html .= '<input type="checkbox" ' . $check . ' class="radiobutton js-ticket-radio-btn" value="' . $option . '" id="' . $field->field . '_' . $i . '" name="' . $field->field . '">';
                                $html .= '<label for="' . $field->field . '_' . $i . '" id="foruf_checkbox1">' . $option . '</label>';
                            $html .= '</div>';
                            $i++;
                        }
                    $html .= '</div>';
                } else {
                    $comboOptions = array('1' => $field->fieldtitle);
                    $html .= $this->checkbox($field->field, $comboOptions, $value, array('class' => 'radiobutton'));
                }
                break;

            case 'radio':
                $comboOptions = array();
                if (!empty($field->userfieldparams)) {
                    $obj_option = json_decode($field->userfieldparams);
                    $i = 0;
                    $jsFunction = '';
                    if ($field->depandant_field != null) {
                        $jsFunction = "getDataForDepandantField('" . $field->field . "','" . $field->depandant_field . "',2,'". Factory::getSession()->getFormToken() ."');";
                    }
                    if(empty($value))
                    $value = array();
                    $html .= '<div class="js-ticket-signature-radio-box-wrp">';
                        foreach ($obj_option AS $option) {
                            $check = '';
                            if($option == $value){
                                $check = 'checked';
                            }
                            $html .= '<div class="js-ticket-radio-box js-ticket-white-background">';
                                $html .= '<input type="radio" ' . $check . ' class="radiobutton js-ticket-radio-btn $cssclass" value="' . $option . '" id="' . $field->field . '_' . $i . '" name="' . $field->field . '" data-validation ="'.$cssclass.'" onclick = "'.$jsFunction.'"> ';
                                $html .= '<label for="' . $field->field . '_' . $i . '" id="foruf_checkbox1">' . $option . '</label>';
                            $html .= '</div>';
                            $i++;
                        }
                    $html .= '</div>';
                    
                }
                
                break;

            case 'combo':
                $comboOptions = array();
                if (!empty($field->userfieldparams)) {
                    $obj_option = json_decode($field->userfieldparams);
                    foreach ($obj_option as $opt) {
                        $comboOptions[] = (object) array('id' => $opt, 'text' => $opt);
                    }
                }
                //code for handling dependent field
                $jsFunction = '';
                if ($field->depandant_field != null) {
                    $jsFunction = "getDataForDepandantField('" . $field->field . "','" . $field->depandant_field . "',1,'". Factory::getSession()->getFormToken() ."');";
                }
                //end
                $html .= $this->select($field->field, $comboOptions, $value, Text::_('Select') . ' ' . $field->fieldtitle, array('data-validation' => $cssclass, 'onchange' => $jsFunction, 'class' => "inputbox one js-form-select-field $cssclass"));
                break;
            
            case 'depandant_field':
                $comboOptions = array();
                if (!empty($field->userfieldparams)) {
                    $obj_option = $this->getDataForDepandantFieldByParentField($field->field, $userfielddataarray);
                    if (!empty($obj_option)) {
                        foreach ($obj_option as $opt) {
                            $comboOptions[] = (object) array('id' => $opt, 'text' => $opt);
                        }
                    }
                }
                //code for handling dependent field
                $jsFunction = '';
                if ($field->depandant_field != null) {
                    $jsFunction = "getDataForDepandantField('" . $field->field . "','" . $field->depandant_field . "',0,'". Factory::getSession()->getFormToken() ."');";
                }
                //end
                $html .= $this->select($field->field, $comboOptions, $value, Text::_('Select') . ' ' . $field->fieldtitle, array('data-validation' => $cssclass, 'onchange' => $jsFunction, 'class' => "inputbox one js-form-select-field $cssclass"));
                break;
            
            case 'multiple':
                $comboOptions = array();
                if (!empty($field->userfieldparams)) {
                    $obj_option = json_decode($field->userfieldparams);
                    foreach ($obj_option as $opt) {
                        $comboOptions[] = (object) array('id' => $opt, 'text' => $opt);
                    }
                }
                $array = $field->field;
                $array .= '[]';
                $html .= $this->select($array, $comboOptions, $value, Text::_('Select') . ' ' . $field->fieldtitle, array('data-validation' => $cssclass, 'multiple' => 'multiple', 'class' =>"js-form-select-field js-form-multi-select-field" ));
                break;
        }
            
            

        if($isadmin == 1){
            echo $html;
            return;
        }
        echo $html;
        
    }

    function showCustomFields($field, $fieldfor, $params , $ticketid) { // ticketid
        $html = '';
        $fvalue = '';        
        
        if(!empty($params)){
            $data = json_decode($params,true);
            if(array_key_exists($field->field, $data)){
                $fvalue = $data[$field->field];
            }
        }
        if($fieldfor == 1){  //FrontEnd mytickets
            $html = '<div class="js-col-md-12 js-wrapper js-ticket-body-data-elipses cusomfield-mg-bottom">';
            if($field->userfieldtype=='file'){
            $html .= '<span class="js-tk-title">' . Text::_($field->fieldtitle) . ':&nbsp</span>';
               if($fvalue !=null){
                    $path = "index.php?option=com_jssupportticket&c=ticket&task=downloadbyname&id=".$ticketid."&name=".$fvalue."&" . Factory::getSession()->getFormToken() . "=1";
                    $html .= '
                        <div class="js-ticket-custom-attachment">
                            <div class="js-tk-value js_ticketattachment">
                                ' . Text::_($field->fieldtitle) . ' ( ' . $fvalue . ' ) ' . '              
                            </div>
                            <a class="button js-ticket-downloadbtn" target="_blank" href="' . $path . '"><img class ="js-ticket-downloadicon" src="components/com_jssupportticket/include/images/download-all.png"> ' . Text::_('Download') . '</a>
                       </div> ';
                }
            }elseif($field->userfieldtype=='date' && !empty($fvalue)){
                $dateformat = JSSupportTicketModel::getJSModel('config')->getConfigurationByName("date_format");
                $fvalue = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($fvalue),$dateformat);
                $html .= '<span class="js-tk-title">' . Text::_($field->fieldtitle) . ':&nbsp</span>';
                $html .= '<span class="js-tk-value">' . $fvalue . '</span>';
            }
            else{
                $html .= '<span class="js-tk-title">' . Text::_($field->fieldtitle) . ':&nbsp</span>';
                $html .= '<span class="js-tk-value">' . $fvalue . '</span>';
            }
            
                $html .= '</div>';
        }elseif($fieldfor == 2){ // ticket detail front end
            $html = '<div class="js-col-md-12 js-second-row-data">';
            if($field->userfieldtype=='file'){
            $html .= '<span class="js-second-row-title">' . $field->fieldtitle . ':&nbsp</span>';
               if($fvalue != null){
                    $path = "index.php?option=com_jssupportticket&c=ticket&task=downloadbyname&id=".$ticketid."&name=".$fvalue."&" . Factory::getSession()->getFormToken() . "=1";
                    $html .= '
                        <div class="js_ticketattachment">
                            ' . $field->fieldtitle . ' ( ' . $fvalue . ' ) ' . '              
                            <a class="button" target="_blank" href="' . $path . '">' . Text::_('Download') . '</a>
                        </div>';
                }
            }elseif($field->userfieldtype=='date' && !empty($fvalue)){
                $dateformat = JSSupportTicketModel::getJSModel('config')->getConfigurationByName("date_format");
                $fvalue = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($fvalue),$dateformat);
                $html .=    '<span class="js-second-row-title">' . $field->fieldtitle . ':&nbsp</span>
                        <span class="js-ticket-value">' . $fvalue . '</span>';
            }
            else{
                $html .=    '<span class="js-second-row-title">' . $field->fieldtitle . ':&nbsp</span>
                        <span class="js-ticket-value">' . $fvalue . '</span>';
            }
            $html .=   '</div>';
        }elseif($fieldfor == 3){ // Admin ticket detail
            if($field->userfieldtype=='date' && !empty($fvalue)){
                $dateformat = JSSupportTicketModel::getJSModel('config')->getConfigurationByName("date_format");
                $fvalue = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($fvalue),$dateformat);
            }
            $html = '<div class="js-tkt-det-info-data">
                            <span class="js-title">
                                ' . $field->fieldtitle . '&nbsp;:
                            </span> ';
            $html .= '       <span class="js-value">' . $fvalue . '</span>
                    </div>';

            if($field->userfieldtype=='file'){
                $html = '<div class="js-tkt-det-info-data">
                            <div class="requester-data-inner-border">
                                <span class="js-title">
                                    ' . $field->fieldtitle. '&nbsp;:
                                </span> ';
                if($fvalue !=null){
                    $path = "index.php?option=com_jssupportticket&c=ticket&task=downloadbyname&id=".$ticketid."&name=".$fvalue."&" . Factory::getSession()->getFormToken() . "=1";
                    $html .= '<div class="requester-data-inner-value js-col-xs-12">' . $field->fieldtitle . ' ( ' . $fvalue . ' ) ' . '<a class="button" target="_blank" href="' . $path . '">' . Text::_('Download') . '</a></div>';
                }
                $html .= '</div></div>';
            }
        }elseif($fieldfor == 4){ // admin ticket listing
            $html = '<div class="js-col-md-12 js-col-xs-12 js-wrapper">';
            if($field->userfieldtype=='file'){
            $html .= '<span class="js-tk-title">' . Text::_($field->fieldtitle) . '<font>:</font>&nbsp;</span>';
               if($fvalue !=null){
                    $path = "index.php?option=com_jssupportticket&c=ticket&task=downloadbyname&id=".$ticketid."&name=".$fvalue."&" . Factory::getSession()->getFormToken() . "=1";
                    $html .= '
                        <div class="js-tk-value js_ticketattachment">
                            ' . $field->fieldtitle . ' ( ' . $fvalue . ' ) ' . '              
                            <a class="button" target="_blank" href="' . $path . '">' . Text::_('Download') . '</a>
                        </div>';
                }
            }elseif($field->userfieldtype=='date' && !empty($fvalue)){
                $dateformat = JSSupportTicketModel::getJSModel('config')->getConfigurationByName("date_format");
                $fvalue = HTMLHelper::_('date',getJSTicketPHPFunctionsClass()->jsticket_strtotime($fvalue),$dateformat);
                $html .= '<span class="js-tk-title">' . Text::_($field->fieldtitle) . ':&nbsp</span>';
                $html .= '<span class="js-tk-value">' . $fvalue . '</span>';
            }
            else{
            $html .= '<span class="js-tk-title">' . Text::_($field->fieldtitle) . ':&nbsp</span>';
                $html .= '<span class="js-tk-value">' . $fvalue . '</span>';
            }
            
                $html .= '</div>';
        }elseif($fieldfor == 5){ // export
            $returnarray = array();
            $returnarray['title'] = $field->fieldtitle;
            $returnarray['value'] = $fvalue;
            return $returnarray;
        }
        return $html;
    }

    function userFieldsData($fieldfor, $listing = null) {
        $db = Factory::getDbo();
        $user = JSSupportticketCurrentUser::getInstance();
        if ($user->getIsGuest()) {
            $published = ' isvisitorpublished = 1 ';
        } else {
            $published = ' published = 1 ';
        }
        $inquery = '';
        if ($listing == 1) {
            $inquery = ' AND showonlisting = 1 ';
        }
        $query = "SELECT field,fieldtitle,isuserfield,userfieldtype,userfieldparams  FROM `#__js_ticket_fieldsordering` WHERE isuserfield = 1 AND " . $published . " AND fieldfor =" . $fieldfor . $inquery." ORDER BY ordering";
        $db->setQuery($query);
        $data = $db->loadObjectList();
        return $data;
    }

    function userFieldsForSearch($fieldfor) {
        $db = Factory::getDbo();
        $user = JSSupportticketCurrentUser::getInstance();
        if ($user->getIsGuest()) {
            $inquery = ' isvisitorpublished = 1 AND search_visitor =1';
        } else {
            $inquery = ' published = 1 AND search_user =1';
        }
        $query = "SELECT required,cols,size ,`rows`,field,fieldtitle,isuserfield,userfieldtype,userfieldparams,depandant_field  FROM `#__js_ticket_fieldsordering` WHERE isuserfield = 1 AND " . $inquery . " AND fieldfor =" . $fieldfor ." ORDER BY ordering ";
        $db->setQuery($query);
        $data = $db->loadObjectList();
        return $data;
    }

    function getDataForDepandantFieldByParentField($fieldfor, $data) {
        $user = JSSupportticketCurrentUser::getInstance();
        if ($user->getIsGuest()) {
            $published = ' isvisitorpublished = 1 ';
        } else {
            $published = ' published = 1 ';
        }
        $db = Factory::getDbo();
        $value = '';
        $returnarray = array();
        $query = "SELECT field from `#__js_ticket_fieldsordering` WHERE isuserfield = 1 AND " . $published . " AND depandant_field ='" . $fieldfor . "'";
        $db->setQuery($query);
        $field = $db->loadResult();
        if ($data != null) {
            foreach ($data as $key => $val) {
                if ($key == $field) {
                    $value = $val;
                }
            }
        }
        $query = "SELECT userfieldparams from `#__js_ticket_fieldsordering` WHERE isuserfield = 1 AND " . $published . " AND field ='" . $fieldfor . "'";
        $db->setQuery($query);
        $field = $db->loadResult();
        $fieldarray = json_decode($field);
        foreach ($fieldarray as $key => $val) {
            if ($value == $key)
                $returnarray = $val;
        }
        return $returnarray;
    }
    
    // new
    private function text($name, $value, $extraattr = array()) {
        $textfield = '<input type="text" name="' . $name . '" id="' . $name . '" value="' . $value . '" ';
        if (!empty($extraattr))
            foreach ($extraattr AS $key => $val)
                $textfield .= ' ' . $key . '="' . $val . '"';
        $textfield .= ' />';
        return $textfield;
    }
    private function textarea($name, $value, $extraattr = array()) {
        $textarea = '<textarea name="' . $name . '" id="' . $name . '" ';
        if (!empty($extraattr))
            foreach ($extraattr AS $key => $val)
                $textarea .= ' ' . $key . '="' . $val . '"';
        $textarea .= ' >' . $value . '</textarea>';
        return $textarea;
    }
    private function hidden($name, $value, $extraattr = array()) {
        $textfield = '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" ';
        if (!empty($extraattr))
            foreach ($extraattr AS $key => $val)
                $textfield .= ' ' . $key . '="' . $val . '"';
        $textfield .= ' />';
        return $textfield;
    }
    private function select($name, $list, $defaultvalue, $title = '', $extraattr = array()) {
        $nameid = getJSTicketPHPFunctionsClass()->jsticket_str_replace('[]','',$name);
        $selectfield = '<select name="' . $name . '" id="' . $nameid . '" ';
        if (!empty($extraattr))
            foreach ($extraattr AS $key => $val) {
                $selectfield .= ' ' . $key . '="' . $val . '"';
            }
        $selectfield .= ' >';
        if ($title != '') {
            $selectfield .= '<option value="">' . $title . '</option>';
        }
        if (!empty($list))
            foreach ($list AS $record) {
                if ((is_array($defaultvalue) && in_array($record->id, $defaultvalue)) || $defaultvalue == $record->id)
                    $selectfield .= '<option selected="selected" value="' . $record->id . '">' . Text::_($record->text,'js-support-ticket') . '</option>';
                else
                    $selectfield .= '<option value="' . $record->id . '">' . Text::_($record->text,'js-support-ticket') . '</option>';
            }

        $selectfield .= '</select>';
        return $selectfield;
    }
    private function radiobutton($name, $list, $defaultvalue, $extraattr = array()) {
        $radiobutton = '';
        $count = 1;
        foreach ($list AS $value => $label) {
            $radiobutton .= '<input type="radio" name="' . $name . '" id="' . $name . $count . '" value="' . $value . '"';
            if ($defaultvalue == $value)
                $radiobutton .= ' checked="checked"';
            if (!empty($extraattr))
                foreach ($extraattr AS $key => $val) {
                    $radiobutton .= ' ' . $key . '="' . $val . '"';
                }
            $radiobutton .= '/><label id="for' . $name . '" for="' . $name . $count . '">' . $label . '</label>';
            $count++;
        }
        return $radiobutton;
    }
    private function checkbox($name, $list, $defaultvalue, $extraattr = array()) {
        $checkbox = '';
        $count = 1;
        foreach ($list AS $value => $label) {
            $checkbox .= '<input type="checkbox" name="' . $name . '" id="' . $name . $count . '" value="' . $value . '"';
            if ($defaultvalue == $value)
                $checkbox .= ' checked="checked"';
            if (!empty($extraattr))
                foreach ($extraattr AS $key => $val) {
                    $checkbox .= ' ' . $key . '="' . $val . '"';
                }
            $checkbox .= '/><label id="for' . $name . '" for="' . $name . $count . '">' . $label . '</label>';
            $count++;
        }
        return $checkbox;
    }    
}
?>
