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
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Table\Table;

class TableFieldOrdering extends Table {

    var $id = null;
    var $field = null;
    var $fieldtitle = null;
    var $ordering = null;
    var $section = null;
    var $fieldfor = null;
    var $published = null;
    var $sys = null;
    var $cannotunpublish = null;
    var $required = null;
    var $isuserfield = null;
    var $depandant_field = null;
    var $showonlisting = null;
    var $search_user = null;
    var $userfieldparams = null;
    var $userfieldtype = null;
    var $size = null;
    var $maxlength = null;
    var $cols = null;
    var $rows = null;
    var $isvisitorpublished = null;
    var $search_visitor = null;
    var $cannotsearch = null;
    var $cannotshowonlisting = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_fieldsordering', 'id', $db);
    }

    /**
     * Validation
     * 
     * @return boolean true if buffer is valid
     * 
     */
    function check() {
      return true;
    }
}
?>