<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');
jimport('joomla.application.component.model');
jimport('joomla.html.html');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

class JSSupportticketModelUserFields extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

   
    function isFieldRequiredByField($field){
        $db = Factory::getDbo();
        $query = "SELECT field.required FROM `#__js_ticket_fieldsordering` AS field WHERE field.field = ".$db->quote($field);
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    function fieldPublished($field_id, $value) {
        if (is_numeric($field_id) == false)
            return false;
        $db = Factory::getDBO();

        $query = " UPDATE `#__js_ticket_fieldsordering` SET published = " . $value . " WHERE id = " . $field_id. " AND cannotunpublish = 0";

        $db->setQuery($query);
        if (!$db->execute()) {
            return false;
        }
        return true;
    }

    function visitorFieldPublished($field_id, $value) {
        if (is_numeric($field_id) == false)
            return false;
        $db = Factory::getDBO();

        $query = " UPDATE `#__js_ticket_fieldsordering` SET isvisitorpublished = " . $value . " WHERE id = " . $field_id. " AND cannotunpublish = 0";

        $db->setQuery($query);
        if (!$db->execute()) {
            return false;
        }
        return true;
    }

    function fieldRequired($field_id, $value) {
        if (is_numeric($field_id) == false)
            return false;
        $db = Factory::getDBO();

        $query = " UPDATE `#__js_ticket_fieldsordering` SET required = " . $value . " WHERE id = " . $field_id. " AND cannotunpublish = 0";
        
        $db->setQuery($query);
        if (!$db->execute()) {
            return false;
        }
        return true;
    }

    function fieldOrderingUp($field_id) {
        if (is_numeric($field_id) == false)
            return false;
        $db = Factory::getDBO();

        $query = "UPDATE `#__js_ticket_fieldsordering` AS f1, `#__js_ticket_fieldsordering` AS f2
                    SET f1.ordering = f1.ordering - 1 WHERE f1.ordering = f2.ordering + 1 AND f1.fieldfor = f2.fieldfor
                    AND f2.id = " . $field_id . " ; ";
        $db->setQuery($query);
        if (!$db->execute()) {
            return false;
        }

        $query = " UPDATE `#__js_ticket_fieldsordering` SET ordering = ordering + 1 WHERE id = " . $field_id . ";"
        ;
        
        $db->setQuery($query);
        if (!$db->execute()) {
            return false;
        }
        return true;
    }

    function fieldOrderingDown($field_id) {
        if (is_numeric($field_id) == false)
            return false;
        $db = Factory::getDBO();

        $query = "UPDATE `#__js_ticket_fieldsordering` AS f1, `#__js_ticket_fieldsordering` AS f2 SET f1.ordering = f1.ordering + 1
                    WHERE f1.ordering = f2.ordering - 1 AND f1.fieldfor = f2.fieldfor AND f2.id = " . $field_id . " ; ";

        $db->setQuery($query);
        if (!$db->execute()) {
            return false;
        }

        $query = " UPDATE `#__js_ticket_fieldsordering` SET ordering = ordering - 1 WHERE id = " . $field_id . ";";
        
        $db->setQuery($query);
        if (!$db->execute()) {
            return false;
        }
        return true;
    }

    

    // New_New
    function getFieldOrderingForList($fieldfor) {
        $db = Factory::getDbo();
        if (is_numeric($fieldfor) == false)
            return false;
        $query = "SELECT * FROM `#__js_ticket_fieldsordering` WHERE fieldfor = ".$fieldfor." ORDER BY ordering ";
        $db->setQuery($query);
        $result[0] = $db->loadObjectList();
        return $result;
    }

    function getFieldsForListing($fieldfor) {
        if (is_numeric($fieldfor) == false)
            return false;
        $db = Factory::getDbo();
        $query = "SELECT field,showonlisting FROM `#__js_ticket_fieldsordering`
                WHERE  fieldfor =  " . $fieldfor ." ORDER BY ordering";
        $db->setQuery($query);
        $fields = $db->loadObjectList();
        $fielddata = array();
        foreach ($fields AS $field) {
            $fielddata[$field->field] = $field->showonlisting;
        }
        return $fielddata;
    }

    function getFieldsOrderingforForm($fieldfor) {
        if (is_numeric($fieldfor) == false)
            return false;
        $db = $this->getDBO();

        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsGuest()){
            $published = ' isvisitorpublished = 1 ';
        }else{
            $published = ' published = 1 ';
        }

        $query = "SELECT  * FROM `#__js_ticket_fieldsordering`
                    WHERE ".$published." AND fieldfor =  " . $fieldfor 
                . " ORDER BY ordering";
        
        $db->setQuery($query);
        $fieldordering = $db->loadObjectList();
        return $fieldordering;
    }

    function storeUserField() {
        $db = Factory::getDBO();
        $data = Factory::getApplication()->input->post->getArray();
        $data = getJSTicketPHPFunctionsClass()->jsticket_sanitizeData($data);// Sanitize entire array to string
        if (empty($data)) {
            return false;
        }
        if ($data['isuserfield'] == 1) {
            // value to add as field ordering
            if ($data['id'] == '') { // only for new
                $query = "SELECT max(ordering) FROM #__js_ticket_fieldsordering WHERE fieldfor=".$_SESSION['ffusr'];
                $db->setQuery($query);
                $var = $db->loadResult();
                $data['ordering'] = $var + 1;
                if(isset($data['userfieldtype']) && $data['userfieldtype'] == 'file' || $data['userfieldtype'] == 'termsandconditions'){
                    $data['cannotsearch'] = 1;
                    $data['cannotshowonlisting'] = 1;
                }else{
                    $data['cannotsearch'] = 0;
                }
                // Unique field
                $query = "SELECT max(id) FROM #__js_ticket_fieldsordering ";
                $db->setQuery($query);
                $var = $db->loadResult();
                $data['field'] = 'ufield_'. ($var + 1);
                $data['fieldfor'] = $_SESSION['ffusr'];

            }

            $params = array();
            //code for depandetn field
            if (isset($data['userfieldtype']) && $data['userfieldtype'] == 'depandant_field') {
                if (!empty($data['arraynames2']) && empty($data['arraynames'])) {
                    //to handle edit case of depandat field
                    $data['arraynames'] = $data['arraynames2'];
                }
                $flagvar = $this->updateParentField($data['parentfield'], $data['field']);
                if ($flagvar == false) {
                    return SAVE_ERROR;
                }
                if (!empty($data['arraynames'])) {
                    $valarrays = getJSTicketPHPFunctionsClass()->jsticket_explode(',', $data['arraynames']);
                    foreach ($valarrays as $key => $value) {
                        $keyvalue = $value;
                        $value = getJSTicketPHPFunctionsClass()->jsticket_str_replace(' ','_',$value);
                        if ($data[$value] != null) {
                            $params[$keyvalue] = array_filter($data[$value]);
                        }
                    }
                }
            }
            if (!empty($data['values'])) {
                foreach ($data['values'] as $key => $value) {
                    if ($value != null) {
                        $params[] = getJSTicketPHPFunctionsClass()->jsticket_trim($value);
                    }
                }
            }

            if (isset($data['userfieldtype']) && $data['userfieldtype'] == 'termsandconditions') { // to manage terms and condition field
                $params['termsandconditions_text'] = $data['termsandconditions_text'];
                $params['termsandconditions_linktype'] = $data['termsandconditions_linktype'];
                $params['termsandconditions_link'] = $data['termsandconditions_link'];
                $params['termsandconditions_page'] = $data['termsandconditions_page'];
            }

            if(!empty($params)){
                $params = json_encode($params);
                $data['userfieldparams'] = $params;
            }
        }
        if($data['isuserfield'] == "") $data['isuserfield'] = NULL;
        $row = $this->getTable('fieldordering');
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        return SAVED;
    }

    function updateField($data) {
        if (empty($data)) {
            return false;
        }

        $db = Factory::getDBO();

        $inquery = '';
        $clasue = '';
        if(isset($data['fieldtitle']) && $data['fieldtitle'] != null){
            $inquery .= $clasue." fieldtitle = '". $data['fieldtitle']."'";
            $clasue = ' , ';
        }
        if(isset($data['published']) && $data['published'] != null){
            $inquery .= $clasue." published = ". $data['published'];
            $clasue = ' , ';
        }
        if(isset($data['isvisitorpublished']) && $data['isvisitorpublished'] != null){
            $inquery .= $clasue." isvisitorpublished = ". $data['isvisitorpublished'];
            $clasue = ' , ';
        }
        if(isset($data['required']) && $data['required'] != null){
            $inquery .= $clasue." required = ". $data['required'];
            $clasue = ' , ';
        }
        if(isset($data['search_user']) && $data['search_user'] != null){
            $inquery .= $clasue." search_user = ". $data['search_user'];
            $clasue = ' , ';
        }
        if(isset($data['search_visitor']) && $data['search_visitor'] != null){
            $inquery .= $clasue." search_visitor = ". $data['search_visitor'];
            $clasue = ' , ';
        }
        if(isset($data['showonlisting']) && $data['showonlisting'] != null){
            $inquery .= $clasue." showonlisting = ". $data['showonlisting'];
            $clasue = ' , ';
        }
        
        $query = "UPDATE `#__js_ticket_fieldsordering` SET ".$inquery." WHERE id = " . $data['id'] ;
        $db->setQuery($query);
        $db->execute();        
        return true;
    }

    function updateParentField($parentfield, $field) {
        $db = $this->getDBO();
        if(!is_numeric($parentfield)) return false;
        $query = "UPDATE `#__js_ticket_fieldsordering` SET depandant_field = '' WHERE depandant_field = '" . $field . "'";
        $db->setQuery($query);
        $db->execute();
        $query = "UPDATE `#__js_ticket_fieldsordering` SET depandant_field = '" . $field . "' WHERE id = " . $parentfield;
        $db->setQuery($query);
        $db->execute();
        return true;
    }

    function getOptionsForFieldEdit($field) {
        $db = $this->getDBO();

        $yesno = array(
            '0' => array('value' => '1', 'text' => Text::_('Yes')),
            '1' => array('value' => '0', 'text' => Text::_('No')),);

        $query = "SELECT * FROM `#__js_ticket_fieldsordering` WHERE id = $field";
        $db->setQuery($query);
        $data = $db->loadObject();
        $html = '<span class="popup-top">
                    <span id="popup_title" >
                    ' . Text::_("Edit Field") . '
                    </span>
                    <span id="popup_cross" onClick="close_popup();" class="close"></span>
                </span>';
        $html .= '<form id="adminForm" class="popup-field-from" method="post" action="index.php?option=com_jssupportticket&c=userfields&task=storeUserFields&'.Factory::getSession()->getFormToken().'=1">';
        $title_value = Text::_('Text');
        if(isset($data->fieldtitle)){
            $title_value = $data->fieldtitle;
        }
        $html .= '<div class="popup-field-wrapper">
                    <div class="popup-field-title">' . Text::_('Field Title') . '<font class="required-notifier">*</font></div>
                    <div class="popup-field-obj">
                        <input type="text" id="fieldtitle" name="fieldtitle" value="'.$title_value.'" class="inputbox one" data-validation="required" />
                    </div>
                </div>';
        if ($data->cannotunpublish == 0 && $data->userfieldtype != 'termsandconditions') {
            $html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . Text::_('User Published') . '</div>
                        <div class="popup-field-obj">' . HTMLHelper::_('select.genericList', $yesno , 'published','class="inputbox one"', 'value' , 'text' , isset($data->published) ? $data->published : 0) . '</div>
                    </div>';
            $html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . Text::_('Visitor published') . '</div>
                        <div class="popup-field-obj">' . HTMLHelper::_('select.genericList', $yesno , 'isvisitorpublished','class="inputbox one"', 'value' , 'text' , isset($data->isvisitorpublished) ? $data->isvisitorpublished : 0) . '</div>
                    </div>';

            $html .= '<div class="popup-field-wrapper">
                    <div class="popup-field-title">' . Text::_('Required') . '</div>
                    <div class="popup-field-obj">' . HTMLHelper::_('select.genericList', $yesno , 'required','class="inputbox one"', 'value' , 'text' , isset($data->required) ? $data->required : 0) . '</div>
                </div>';
        }

        if ($data->cannotsearch == 0 && $data->userfieldtype != 'termsandconditions') {
            $html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . Text::_('User Search') . '</div>
                        <div class="popup-field-obj">' . HTMLHelper::_('select.genericList', $yesno , 'search_user','class="inputbox one"', 'value' , 'text' , isset($data->search_user) ? $data->search_user : 0) . '</div>
                    </div>';
            $html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . Text::_('Visitor Search') . '</div>
                        <div class="popup-field-obj">' . HTMLHelper::_('select.genericList', $yesno , 'search_visitor','class="inputbox one"', 'value' , 'text' , isset($data->search_visitor) ? $data->search_visitor : 0) . '</div>
                    </div>';
        }
        if (($data->isuserfield == 1 || $data->cannotshowonlisting == 0) && $data->userfieldtype != 'termsandconditions') {
            $html .= '<div class="popup-field-wrapper">
                        <div class="popup-field-title">' . Text::_('Show On Listing') . '</div>
                        <div class="popup-field-obj">' . HTMLHelper::_('select.genericList', $yesno , 'showonlisting','class="inputbox one"', 'value' , 'text' , isset($data->showonlisting) ? $data->showonlisting : 0) . '</div>
                    </div>';
        }
        

        $html .= '
            <input type="hidden" id="id" name="id" value="'.$data->id.'" />
            <input type="hidden" id="isuserfield" name="isuserfield" value="'.$data->isuserfield.'" />
            <input type="hidden" id="fieldfor" name="fieldfor" value="'.$data->fieldfor.'" />';
        $html .=     HTMLHelper::_('form.token');    
        $html .='<div class="js-submit-container">
                <input type="submit" id="save" name="save" value="'.Text::_('Save').'" class="button" />';
        if ($data->isuserfield == 1) {
            $html .= '<a class="button" style="margin-left:15px;" id="user-field-anchor" href="index.php?option=com_jssupportticket&c=userfields&view=userfields&layout=formuserfield&cid[]=' . $data->id .'&ff='.$data->fieldfor.'"> ' . Text::_('Advanced') . ' </a>';
        }

        $html .='</div>
            </form>';
        return $html;
    }

    function deleteUserField($id){
        if (is_numeric($id) == false)
           return false;
        $db = $this->getDBO();

        $query = "SELECT field,fieldfor FROM `#__js_ticket_fieldsordering` WHERE id = $id";
        $db->setQuery($query);
        $result = $db->loadObject();
        if ($this->userFieldCanDelete($result) == true) {
            $query = "DELETE FROM `#__js_ticket_fieldsordering` WHERE id = $id";
            $db->setQuery($query);
            $db->execute();
            return DELETED;
        }else{
            return IN_USE;
        }
    }

    function enforceDeleteUserField($id){
        if (is_numeric($id) == false)
           return false;
        $db = $this->getDBO();
        $query = "SELECT field,fieldfor FROM `#__js_ticket_fieldsordering` WHERE id = $id";
        $db->setQuery($query);
        $result = $db->loadObject();
        if (true) {
            $query = "DELETE FROM `js_ticket_fieldsordering` WHERE id = $id";
            $db->setQuery($query);
            $db->execute();
            return DELETE;
        }else{
            return IN_USE;
        }
    }

    function userFieldCanDelete($field) {

        $fieldname = $field->field;
        $fieldfor = $field->fieldfor; 

        $db = $this->getDBO();
        
        if($fieldfor == 1){//for deleting a ticket field
            $table = "tickets";
        }
        $query = ' SELECT
                    ( SELECT COUNT(id) FROM `#__js_ticket_'.$table.'` WHERE
                        params LIKE \'%"' . $fieldname . '":%\' 
                    )
                    AS total';
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total > 0)
            return false;
        else
            return true;
    }

    function getFieldTitleByFieldfor($fieldfor) {
        if (!is_numeric($fieldfor))
            return false;
        $db = $this->getDBO();
        $query = "SELECT field,fieldtitle FROM `#__js_ticket_fieldsordering` WHERE fieldfor = " . $fieldfor ;
        $db->setQuery($query);
        $fields = $db->loadObjectList();
        $fielddata = array();
        foreach ($fields as $value) {
            $fielddata[$value->field] = $value->fieldtitle;
        }
        return $fielddata;
    }

    function getUserFieldbyId($id,$fieldfor) {
        if ($id) {
            if (is_numeric($id) == false)
                return false;
            $db = $this->getDBO();
            $query = "SELECT * FROM `#__js_ticket_fieldsordering` WHERE id = " . $id;
            $db->setQuery($query);
            $result['userfield'] = $db->loadObject();
            $params = $result['userfield']->userfieldparams;

            $result['userfieldparams'] = !empty($params) ? json_decode($params, true) : '';
        }
        $result['fieldfor'] = $fieldfor;
        $result['joomlaarticles'] = $this->getJSModel('jssupportticket')->joomlaContentArticles();
        return $result;
    }

    function dataForDepandantField( $val , $childfield){ 
        $db = $this->getDBO();
        $query = "SELECT userfieldparams,fieldtitle,field,depandant_field FROM `#__js_ticket_fieldsordering` WHERE field = '".$childfield."'"; 
        $db->setQuery($query);
        $data = $db->loadObject();
        $decoded_data = json_decode($data->userfieldparams); 
        $comboOptions = array(); 
        $flag = 0; 
        foreach ($decoded_data as $key => $value) { 
            if($key == $val){ 
               for ($i=0; $i < getJSTicketPHPFunctionsClass()->jsticket_count($value) ; $i++) {
                if($flag == 0){
                    $comboOptions[] = array('value' => null, 'text' => Text::_('Select').' '.$data->fieldtitle); 
                }
                $comboOptions[] = array('value' => $value[$i], 'text' => $value[$i]); 
                $flag = 1; 
               } 
            } 
        }
        $jsFunction = ''; 
        if ($data->depandant_field != null) {
            $jsFunction = "onchange=getDataForDepandantField('" . $data->field . "','" . $data->depandant_field . "',1,'". Factory::getSession()->getFormToken() ."');";
        }
        $html = HTMLHelper::_('select.genericList', $comboOptions , $childfield,'class="inputbox js-form-select-field one"'.$jsFunction, 'value' , 'text' ,'');
        return $html; 
    }

    function getFieldsForComboByFieldFor( $fieldfor, $parentfield ) {
        $db = Factory::getDbo();
        $query = "SELECT fieldtitle AS text , id AS value FROM `#__js_ticket_fieldsordering` WHERE fieldfor = $fieldfor AND (userfieldtype = 'radio' OR userfieldtype = 'combo' OR userfieldtype = 'depandant_field') ";
        $db->setQuery($query);
        $data = $db->loadObjectList();
        $result = array(array('text' => Text::_('Select parent field'), 'value' => null));
        foreach($data AS $da){
            $result[] = $da;
        }
        $query = "SELECT id FROM `#__js_ticket_fieldsordering` WHERE fieldfor = $fieldfor AND (userfieldtype = 'radio' OR userfieldtype = 'combo' OR userfieldtype = 'depandant_field') AND depandant_field = '" . $parentfield . "' ";
        $db->setQuery($query);
        $parent = $db->loadResult();
        $jsFunction = 'onchange="getDataOfSelectedField();"';
        $html = HTMLHelper::_('select.genericList', $result, 'parentfield', 'class="inputbox inputbox one" ' .$jsFunction, 'value', 'text', $parent);

        return $html;
    }

    function getSectionToFillValues( $field ) {
            if( ! is_numeric($field))
                return false;
        $db = Factory::getDbo();
        $query = "SELECT userfieldparams FROM `#__js_ticket_fieldsordering` WHERE id = $field";
        $db->setQuery($query);
        $data = $db->loadResult();
        $datas = json_decode($data);
        $html = '';
        $fieldsvar = '';
        $comma = '';
        foreach ($datas as $data) {
            if(is_array($data)){
                for ($i = 0; $i < getJSTicketPHPFunctionsClass()->jsticket_count($data); $i++) {
                    $fieldsvar .= $comma . "$data[$i]";
                    $textvar = $data[$i];
                    $textvar .='[]';
                    $html .= "<div class='js-field-wrapper js-row no-margin'>";
                    $html .= "<div class='js-field-title js-col-lg-3 js-col-md-3 no-padding'>" . $data[$i] . "</div>";
                    $html .= "<div class='js-col-lg-9 js-col-md-9 no-padding combo-options-fields' id=" . $data[$i] . ">
                                    <span class='input-field-wrapper'>
                                        <input type='text' id='".$textvar."' name='".$textvar."' class='inputbox one user-field' />
                                        <img class='input-field-remove-img' src='components/com_jssupportticket/include/images/deletes.png' />
                                    </span>
                                    <input type='button' id='depandant-field-button' onClick='getNextField(\"" . $data[$i] . "\",this);'  value='Add More' />
                                </div>";
                    $html .= "</div>";
                    $comma = ',';
                }
            }else{
                $fieldsvar .= $comma . "$data";
                $textvar = $data;
                $textvar .='[]';
                $html .= "<div class='js-field-wrapper js-row no-margin'>";
                $html .= "<div class='js-field-title js-col-lg-3 js-col-md-3 no-padding'>" . $data . "</div>";
                $html .= "<div class='js-col-lg-9 js-col-md-9 no-padding combo-options-fields' id=" . $data . ">
                                <span class='input-field-wrapper'>
                                    <input type='text' id='".$textvar."' name='".$textvar."' class='inputbox one user-field' />
                                    <img class='input-field-remove-img' src='components/com_jssupportticket/include/images/deletes.png' />
                                </span>
                                <input type='button' id='depandant-field-button' onClick='getNextField(\"" . $data . "\",this);'  value='Add More' />
                            </div>";
                $html .= "</div>";
                $comma = ',';
            }
        }

        $html .= " <input type='hidden' name='arraynames' value='" . $fieldsvar . "' />";
        return $html;
    }

    function getUserUnpublishFieldsfor($fieldfor) {
        if (!is_numeric($fieldfor))
            return false;
        $db = Factory::getDbo();
        
        $user = JSSupportticketCurrentUser::getInstance();
        if ($user->getIsGuest()) {
            $published = ' isvisitorpublished = 0 ';
        } else {
            $published = ' published = 0 ';
        }

        $query = "SELECT field FROM `#__js_ticket_fieldsordering` WHERE fieldfor = " . $fieldfor . " AND isuserfield = 1 AND " . $published;
        $db->setQuery($query);
        $fields = $db->loadObjectList();
        return $fields;
    }


    function getUserfieldsfor($fieldfor) {        
        if (!is_numeric($fieldfor))
            return false;
        $db = Factory::getDbo();
        $user = JSSupportticketCurrentUser::getInstance();
        if ($user->getIsGuest()) {
            $published = ' isvisitorpublished = 1 ';
        } else {
            $published = ' published = 1 ';
        }
        $query = "SELECT field,userfieldparams,userfieldtype,fieldtitle FROM `#__js_ticket_fieldsordering` WHERE fieldfor = " . $fieldfor . " AND isuserfield = 1 AND " . $published;
        $db->setQuery($query);
        $fields = $db->loadObjectList();
        return $fields;
    }    
}
?>
