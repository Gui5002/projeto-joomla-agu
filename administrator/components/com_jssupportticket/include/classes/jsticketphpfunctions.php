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

use Joomla\Filter\InputFilter;

defined('_JEXEC') or die('Not Allowed');

class jsticketphpfunctions {

    static function jsticket_str_replace($search,$replace,$content){
        if($content == ''){
            return $content;
        }
        $content = str_replace($search, $replace, $content);
        return $content;
    }

    static function jsticket_safe_encoding($string){
        if($string == ''){
            return $string;
        }
        $string = base64_encode($string);
        return $string;
    }

    static function jsticket_safe_decoding($string){
        if($string == ''){
            return $string;
        }
        $string = base64_decode($string);
        return $string;
    }

    public static function jsticket_strstr($haystack, $needle) {
        if($haystack == '' || $needle == ''){
            return false;
        }
        return strstr($haystack, $needle);
    }

    public static function jsticket_explode($separator, $haystack) {
        if($separator == ''){
            return false;
        }
        return explode($separator, $haystack);
    }

    public static function jsticket_strip_tags($string, $allowable_tags = NULL) {
      if (!is_null($string)) {
        return strip_tags($string, $allowable_tags);
      }
      return $string;
    }

    // used onec in admin
    public static function jsticket_htmlentities($string) {
        if($string == ''){
            return '';
        }
        return htmlentities($string);
    }

    public static function jsticket_strtoupper($string) {
        if($string == ''){
            return '';
        }
        return strtoupper($string);
    }

    public static function jsticket_basename($string,$suffix = '') {
        $basename = '';
        if($string !== ''){
           $basename = basename($string,$suffix);
        }
        return $basename;
    }

    public static function jsticket_dirname($string,$lvls = 1) {
        $dirname = '';
        if($string !== ''){
           $dirname = dirname($string,$lvls);
        }
        return $dirname;
    }


    public static function jsticket_substr($str, $start, $length = null) {
        $output = null;
        if ($str !== null) {
            if ($length !== null) {
                $output = substr($str, $start, $length);
            } else {
                $output = substr($str, $start);
            }
        }
        return $output;
    }


    // public static function jsticket_mb_substr($str, $start, $length = null, $encoding = 'UTF-8') {
    //     $output = null;
    //     if ($str !== null) {
    //         if ($length !== null) {
    //             $output = mb_substr($str, $start, $length, $encoding);
    //         } else {
    //             $output = mb_substr($str, $start);
    //         }
    //     }
    //     return $output;
    // }


    // public static function jsticket_ucwords($str, $delimiters = "") {
    //     $output = null;
    //     if ($str !== null) {
    //         $output = ucwords($str, $delimiters);
    //     }
    //     return $output;
    // }

    // public static function jsticket_str_rot13($str){
    //     $output = null;
    //     if ($str !== null) {
    //         $output = str_rot13($str);
    //     }
    //     return $output;
    // }

    public static function jsticket_preg_replace($pattern, $replacement, $subject, $limit = -1, &$count = null){
        $output = null;
        if ($pattern !== null && $replacement !== null && $subject !== null) {
            $output = preg_replace($pattern, $replacement, $subject, $limit, $count);
        }
        return $output;
    }

    public static function jsticket_strlen($str){
        $output = null;
        if ($str !== null) {
            $output = strlen($str);
        }
        return $output;
    }

    // public static function jsticket_mb_strlen($str, $encoding = 'UTF-8'){
    //     $output = null;
    //     if ($str !== null) {
    //         if ($encoding !== null) {
    //             $output = mb_strlen($str, $encoding);
    //         } else {
    //             $output = mb_strlen($str);
    //         }
    //     }
    //     return $output;
    // }

    public static function jsticket_md5($str, $raw_output = false){
        $output = null;
        if ($str !== null) {
            $output = md5($str, $raw_output);
        }
        return $output;
    }

    public static function jsticket_preg_match($pattern, $subject, &$matches = null, $flags = 0, $offset = 0){
        $output = null;
        if ($pattern !== null && $subject !== null) {
            $output = preg_match($pattern, $subject, $matches, $flags, $offset);
        }
        return $output;
    }

    public static function jsticket_strtolower($str){
        $output = null;
        if ($str !== null) {
            $output = strtolower($str);
        }
        return $output;
    }

    public static function jsticket_strpos($haystack, $needle, $offset = 0){
        $output = null;
        if ($haystack !== null && $needle !== null) {
            $output = strpos($haystack, $needle, $offset);
        }
        return $output;
    }

    public static function jsticket_str_repeat($input, $multiplier){
        $output = null;
        if ($input !== null && $multiplier !== null) {
            $output = str_repeat($input, $multiplier);
        }
        return $output;
    }

    public static function jsticket_stripslashes($str){
        $output = null;
        if ($str !== null) {
            $output = stripslashes($str);
        }
        return $output;
    }

    public static function jsticket_htmlspecialchars($string, $flags = ENT_COMPAT | ENT_HTML401, $encoding = 'UTF-8', $double_encode = true){
        $output = null;
        if ($string !== null) {
            $output = htmlspecialchars($string, $flags, $encoding, $double_encode);
        }
        return $output;
    }

    // public static function jsticket_setcookie($name, $value = "", $expires = 0, $path = "", $domain = "", $secure = false, $httponly = false){
    //     $output = null;
    //     if ($name !== null) {
    //         $output = setcookie($name, $value, $expires, $path, $domain, $secure, $httponly);
    //     }
    //     return $output;
    // }

    public static function jsticket_urlencode($str){
        $output = null;
        if ($str !== null) {
            $output = urlencode($str);
        }
        return $output;
    }

    // public static function jsticket_crypt($str, $salt = null){
    //     $output = null;
    //     if ($str !== null) {
    //         if ($salt !== null) {
    //             $output = crypt($str, $salt);
    //         } else {
    //             $output = crypt($str);
    //         }
    //     }
    //     return $output;
    // }

    // public static function jsticket_urldecode($str){
    //     $output = null;
    //     if ($str !== null) {
    //         $output = urldecode($str);
    //     }
    //     return $output;
    // }

    public static function jsticket_trim($str, $charlist = ""){
        $output = null;
        if ($str !== null) {
            $output = trim($str, $charlist);
        }
        return $output;
    }

    public static function jsticket_rtrim($str, $chars = null){
        $output = null;
        if ($str !== null) {
            if ($chars !== null) {
                $output = rtrim($str, $chars);
            } else {
                $output = rtrim($str);
            }
        }
        return $output;
    }

    // public static function jsticket_stristr($haystack, $needle, $before_needle = false)
    // {
    //     $output = null;
    //     if ($haystack !== null && $needle !== null) {
    //         $output = stristr($haystack, $needle, $before_needle);
    //     }
    //     return $output;
    // }

    // public static function jsticket_ucfirst($str){
    //     $output = null;
    //     if ($str !== null) {
    //         $output = ucfirst($str);
    //     }
    //     return $output;
    // }

    public static function jsticket_parse_str($str, &$output){
        if ($str !== null) {
            parse_str($str, $output);
        }
    }



    // public static function jsticket_preg_split($pattern, $subject, $limit = -1, $flags = 0){
    //     $output = null;
    //     if ($pattern !== null && $subject !== null) {
    //         $output = preg_split($pattern, $subject, $limit, $flags);
    //     }
    //     return $output;
    // }

    public static function jsticket_sanitizeData($data){
        if($data == null){
            return $data;
        }
        // create an instance of InputFilter
        $inputFilter = new InputFilter();
        // sanitize the input data using the STRING constant
        return $sanitizedData = $inputFilter->clean($data, 'STRING');
    }

    public static function jsticket_strtotime($str){
        $output = '0000-00-00 00:00:00';
        if ($str !== null) {
            $output = strtotime($str);
        }
        return $output;
    }

    public static function jsticket_round($value, $precision = 0){
        if ($value !== null) {
            return round($value, $precision);
        }
        return null; // Return null if $value is null
    }

    public static function jsticket_count($value){
    //if (is_array($value) || $value instanceof Countable) {
    if (is_array($value)) {
        return count($value);
    }
    return 0; // Return 0 if $value is neither an array nor Countable
}

}
?>