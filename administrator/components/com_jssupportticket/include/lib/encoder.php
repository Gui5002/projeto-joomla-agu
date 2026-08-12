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


class JSSTEncoder {

    private $securekey, $iv;

    function __construct($textkey) {
        $this->securekey = hash('sha256', $textkey, TRUE);
        $this->iv = mcrypt_create_iv(32);
    }

    function encrypt($input) {
        return getJSTicketPHPFunctionsClass()->jsticket_safe_encoding(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv));
    }

    function decrypt($input) {
        return getJSTicketPHPFunctionsClass()->jsticket_trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($input), MCRYPT_MODE_ECB, $this->iv));
    }

}

?>
