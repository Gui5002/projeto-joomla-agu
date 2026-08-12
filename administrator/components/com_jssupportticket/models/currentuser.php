<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');
use Joomla\CMS\Factory;

class JSSupportticketCurrentUser{

    private $id;
    private $name;
    private $username;
    private $email;
    private $isguest;
    private $isadmin;

    private function __construct() {

        $user = Factory::getUser();
        $app = Factory::getApplication();
        $this->id = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->isguest = $user->guest;
        $this->isadmin = $app->isClient('administrator');
    }
    //getters
    function getId(){
        return $this->id;
    }
    function getName(){
        return $this->name;
    }
    function getUserName(){
        return $this->username;
    }
    function getEmail(){
        return $this->email;
    }
    function getIsGuest(){
        return $this->isguest;
    }
    function getIsAdmin(){
        return $this->isadmin;
    }

    public static function getInstance()  // Singleton class concept
    {   
        static $inst = null;
        if ($inst === null) {
            $inst = new JSSupportticketCurrentUser();
        }
        return $inst;
    }

}
?>
