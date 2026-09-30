<?php

/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  April 05, 2012
  ^
  + Project:        JS Tickets
  ^
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\View\HtmlView;

if (JVERSION < 3) {
    jimport('joomla.application.component.model');
    jimport('joomla.application.component.view');
    jimport('joomla.application.component.controller');

    if (!class_exists('JSSupportTicketController', false)) {

        abstract class JSSupportTicketController extends JController {

            function __construct() {
                parent::__construct();
            }

            static function getJSModel($model) {
                return JSSupportTicketModel::getJSModel($model);
            }

        }

    }
    if (!class_exists('JSSupportTicketModel', false)) {

        abstract class JSSupportTicketModel extends JModel {

            function __construct() {
                parent::__construct();
            }

            static function getJSModel($model) {
                require_once JPATH_COMPONENT . '/models/' . $model . '.php';
                $modelclass = 'JSSupportticketModel' . $model;
                $model_object = new $modelclass;
                return $model_object;
            }

            static function getJSModelForMP($model) {
                require_once JPATH_BASE.'/administrator/components/com_jssupportticket/models/' . $model . '.php';
                $modelclass = 'JSSupportticketModel' . $model;
                $model_object = new $modelclass;
                return $model_object;
            }
            
            static function getJSModelForAdminMP($model) {
                require_once JPATH_BASE.'/components/com_jssupportticket/models/' . $model . '.php';
                $modelclass = 'JSSupportticketModel' . $model;
                $model_object = new $modelclass;
                return $model_object;
            }

        }

    }
    if (!class_exists('JSSupportTicketView', false)) {

        abstract class JSSupportTicketView extends JView {

            function __construct() {
                parent::__construct();
            }

            static function getJSModel($model) {
                return JSSupportTicketModel::getJSModel($model);
            }

        }

    }
} elseif (JVERSION < 6) {
    if (!class_exists('JSSupportTicketController', false)) {

        abstract class JSSupportTicketController extends JControllerLegacy {

            function __construct() {
                parent::__construct();
            }

            static function getJSModel($model) {
                return JSSupportTicketModel::getJSModel($model);
            }

        }

    }
    if (!class_exists('JSSupportTicketModel', false)) {

        abstract class JSSupportTicketModel extends JModelLegacy {

            function __construct() {
                parent::__construct();
            }

            static function getJSModel($model) {
                require_once JPATH_COMPONENT . '/models/' . $model . '.php';
                $modelclass = 'JSSupportticketModel' . $model;
                $model_object = new $modelclass;
                return $model_object;
            }

            static function getJSModelForMP($model) {
                require_once JPATH_BASE.'/administrator/components/com_jssupportticket/models/' . $model . '.php';
                $modelclass = 'JSSupportticketModel' . $model;
                $model_object = new $modelclass;
                return $model_object;
            }
            
            static function getJSModelForAdminMP($model) {
                require_once JPATH_BASE.'/components/com_jssupportticket/models/' . $model . '.php';
                $modelclass = 'JSSupportticketModel' . $model;
                $model_object = new $modelclass;

                return $model_object;
            }

        }

    }
    if (!class_exists('JSSupportTicketView', false)) {

        abstract class JSSupportTicketView extends JViewLegacy {

            function __construct() {
                parent::__construct();
            }

            static function getJSModel($model) {
                return JSSupportTicketModel::getJSModel($model);
            }

        }

    }
} else {
    if (!class_exists('JSSupportTicketController', false)) {

        abstract class JSSupportTicketController extends BaseController {

            function __construct() {
                parent::__construct();
            }

            static function getJSModel($model) {
                return JSSupportTicketModel::getJSModel($model);
            }

        }

    }
    if (!class_exists('JSSupportTicketModel', false)) {

        abstract class JSSupportTicketModel extends BaseDatabaseModel {

            function __construct() {
                parent::__construct();
            }

            static function getJSModel($model) {
                require_once JPATH_COMPONENT . '/models/' . $model . '.php';
                $modelclass = 'JSSupportticketModel' . $model;
                $model_object = new $modelclass;
                return $model_object;
            }

            static function getJSModelForMP($model) {
                require_once JPATH_BASE.'/administrator/components/com_jssupportticket/models/' . $model . '.php';
                $modelclass = 'JSSupportticketModel' . $model;
                $model_object = new $modelclass;
                return $model_object;
            }
            
            static function getJSModelForAdminMP($model) {
                require_once JPATH_BASE.'/components/com_jssupportticket/models/' . $model . '.php';
                $modelclass = 'JSSupportticketModel' . $model;
                $model_object = new $modelclass;

                return $model_object;
            }

        }

    }
    if (!class_exists('JSSupportTicketView', false)) {

        abstract class JSSupportTicketView extends HtmlView {

            function __construct() {
                parent::__construct();
            }

            static function getJSModel($model) {
                return JSSupportTicketModel::getJSModel($model);
            }

        }

    }    
}
?>
