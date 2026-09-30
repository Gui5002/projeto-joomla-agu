<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

class messagesLayout{
    private static function renderMessage($type, $title, $message, $buttonHtml = ''){
        $type = preg_replace('/[^a-z0-9_-]/i', '', (string) $type);
        $layout = "<div id='js-messagelayout-wrapper' class='js-ticket-box-shadow jsst-message-layout jsst-message-" . $type . "'>
                    <div id='js-imgfor-message' class='jsst-message-icon' aria-hidden='true'>
                        <span></span>
                    </div>
                    <div id='js-datafor-message'>
                        <div class='js-message-title'>" . $title . "</div>
                        <div class='js-message-detail'>" . $message . "</div>" . $buttonHtml . "
                    </div>
                </div>";
        echo $layout;
    }

    public static function getRecordNotFound(){
        self::renderMessage('empty', Text::_('No records found'), Text::_('There is no data to show for the current filters.'));
    }

    public static function getSystemOffline($title, $message){
        self::renderMessage('offline', $title, $message);
    }

    public static function getUserNotLogin(){
        $buttonHtml = "<div class='js-message-button'><a href='#'>" . Text::_('Login') . "</a></div>";
        self::renderMessage('login', Text::_('Login required'), Text::_('Please log in to continue.'), $buttonHtml);
    }
}
?>
