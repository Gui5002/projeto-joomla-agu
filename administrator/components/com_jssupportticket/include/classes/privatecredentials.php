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

class privatecredentials{

    // The cipher method. openssl_get_cipher_methods returns list of all available methods
    private $cipher_method = "AES-256-CBC";

    // to return data on as is
    private $options = OPENSSL_RAW_DATA;

    private $secretKey;

    public function encrypt($text_for_enrcyption) {
        $this->secretKey = $this->secretKey();
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher_method));

        $encrypted_text = openssl_encrypt( $text_for_enrcyption, $this->cipher_method, $this->secretKey, $this->options, $iv );
        $return_data = getJSTicketPHPFunctionsClass()->jsticket_safe_encoding($encrypted_text) . ':'. getJSTicketPHPFunctionsClass()->jsticket_safe_encoding($iv);

        return $return_data;
    }

    public function decrypt($encrypted_string) {
        $parts = getJSTicketPHPFunctionsClass()->jsticket_explode(':', $encrypted_string);
        if (getJSTicketPHPFunctionsClass()->jsticket_count($parts) != 2) {
            die('failed');
        }
        $encrypted_text = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($parts[0]);
        $iv = getJSTicketPHPFunctionsClass()->jsticket_safe_decoding($parts[1]);
        return openssl_decrypt( $encrypted_text, $this->cipher_method, $this->getSecretKeyForDcrpt(), $this->options, $iv);
    }

    private function secretKey(){
        $secretkey = JSSupportTicketModel::getJSModel('config')->getConfigurationByName('private_credentials_secretkey');
        if($secretkey == ''){
            if(defined('JSST_PRIVATE_CREDENTIALS_KEY')){
                $secretkey = hash('sha256', JSST_PRIVATE_CREDENTIALS_KEY);
            }else{
                $secretkey = $this->generateSecretKey();
                $secretkey = hash('sha256', $secretkey);
                $query = "UPDATE `#__js_ticket_config` set configvalue = $secretkey WHERE configname = 'private_credentials_secretkey'";
                $db->setQuery($query);
                if(!$db->execute()){
                    $error_message = 'Secrect key store failed';
                    JSSupportTicketModel::getJSModel('systemerror')->addSystemError($error_message);
                }
            }
            // store secret key in configuration
        }
        return $secretkey;
    }

    private function getSecretKeyForDcrpt(){
        if(defined('JSST_PRIVATE_CREDENTIALS_KEY')){
            $secretkey = hash('sha256', JSST_PRIVATE_CREDENTIALS_KEY);
        }else{
            $secretkey = JSSupportTicketModel::getJSModel('config')->getConfigurationByName('private_credentials_secretkey');
        }
        return $secretkey;
    }

   // Generate a key of given length;

    public static function generateSecretKey($length = 128){
        return  bin2hex(openssl_random_pseudo_bytes($length));
    }
}
