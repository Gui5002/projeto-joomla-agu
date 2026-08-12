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
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

	class messagesLayout
	{
		public static function getRecordNotFound($popupfor=0){
			$layout='
					<div class="js-ticket-error-message-wrapper js-ticket-box-shadow">
					<div class="js-ticket-message-image-wrapper">
						<img class="js-ticket-message-image" src="'.Uri::root().'components/com_jssupportticket/include/images/error/no-record-icon.png"/>
					</div>
					<div class="js-ticket-messages-data-wrapper">
						<span class="js-ticket-messages-main-text">
					    	' . Text::_('Sorry') . '!
						</span>
						<span class="js-ticket-messages-block_text">
					    	' . Text::_('No record found') . '...!
						</span>
					</div>
				</div>';
			if($popupfor == 0){
				echo $layout;
			}else{
				return $layout;
			}
			
		}
		public static function getPermissionNotAllow(){
			$layout='
					<div class="js-ticket-error-message-wrapper js-ticket-box-shadow">
						<div class="js-ticket-message-image-wrapper">
							<img class="js-ticket-message-image" src="'.Uri::root().'components/com_jssupportticket/include/images/error/not-permission-icon.png"/>
						</div>
						<div class="js-ticket-messages-data-wrapper">
							<span class="js-ticket-messages-main-text">
						    	' . Text::_('Access Denied') . '!
							</span>
							<span class="js-ticket-messages-block_text">
						    	' . Text::_('You have no permission to access this page').'. ' . '
							</span>
						</div>
					</div>
					';
			echo $layout;
		}
		
		public static function getUserNotAllowedToViewTicket(){
			$layout='
				<div class="js-ticket-error-message-wrapper js-ticket-box-shadow">
					<div class="js-ticket-message-image-wrapper">
						<img class="js-ticket-message-image" src="'.Uri::root().'components/com_jssupportticket/include/images/error/not-permission-icon.png"/>
					</div>
					<div class="js-ticket-messages-data-wrapper">
						<span class="js-ticket-messages-main-text">
					    	' . Text::_('Sorry') . '!
						</span>
						<span class="js-ticket-messages-block_text">
					    	' . Text::_('Ticket was created by Visitor, User is not allowed to view this Ticket') . '
						</span>
					</div>
				</div>
		';
			echo $layout;
		}

		public static function getNotStaffMember(){
			$layout='
				<div class="js-ticket-error-message-wrapper js-ticket-box-shadow">
					<div class="js-ticket-message-image-wrapper">
						<img class="js-ticket-message-image" src="'.Uri::root().'components/com_jssupportticket/include/images/error/not-permission-icon.png"/>
					</div>
					<div class="js-ticket-messages-data-wrapper">
						<span class="js-ticket-messages-main-text">
					    	' . Text::_('Access Denied') . '!
						</span>
						<span class="js-ticket-messages-block_text">
					    	' . Text::_('User are not allowed to access this page.') . '
						</span>
					</div>
				</div>
		';
			echo $layout;
		}
		public static function getSystemOffline($title,$message){
			$layout='
				<div class="js-ticket-error-message-wrapper js-ticket-box-shadow">
					<div class="js-ticket-message-image-wrapper">
						<img class="js-ticket-message-image" src="'.Uri::root().'components/com_jssupportticket/include/images/error/offline-icon.png"/>
					</div>
					<div class="js-ticket-messages-data-wrapper">
						<span class="js-ticket-messages-main-text">
					    		'.$title.'
						</span>
						<span class="js-ticket-messages-block_text">
					    		'.$message.'
						</span>
					</div>
				</div>
		';
			echo $layout;
		}
		
		public static function getStaffDisable(){
			$layout= '
				<div class="js-ticket-error-message-wrapper js-ticket-box-shadow">
					<div class="js-ticket-message-image-wrapper">
						<img class="js-ticket-message-image" src="'.Uri::root().'components/com_jssupportticket/include/images/error/not-permission-icon.png"/>
					</div>
					<div class="js-ticket-messages-data-wrapper">
						<span class="js-ticket-messages-main-text">
					    	' . Text::_('Access Denied') . '!
						</span>
						<span class="js-ticket-messages-block_text">
					    	' . Text::_('Your account has been disabled, Please contact to the administrator.') . '
						</span>
					</div>
				</div>
		';
			echo $layout;
		}

		public static function getFeedbackMessage($msg_type,$contect_text){
			if($msg_type == 2){
    		$img_var = '3.png';
    		$text_var_1 = Text::_('Sorry');
    		$text_var_1 .= '!';
    		$text_var_2 = Text::_('You have already given the feedback for this ticket.');
    	}elseif($msg_type == 3){
    		$img_var = 'no-record-icon.png';
    		$text_var_1 = Text::_('Sorry');
    		$text_var_1 .= '!';
    		$text_var_2 = Text::_('Ticket not found');
    		$text_var_2 .= '...!';
    	}else{
    		$img_var = 'not-permission-icondd.png';
    		$text_var_1 = Text::_('Sorry');
    		$text_var_1 .= '!';
    		$text_var_2 = Text::_('User is not allowed to view this page');
    	}
    	if($msg_type == 4){
			$layout = '   
					<div class="js-ticket-error-message-wrapper js-ticket-box-shadow">
						<div class="js-ticket-message-image-wrapper">
							<img class="js-ticket-message-image" src="'.Uri::root().'components/com_jssupportticket/include/images/error/success.png"/>
						</div>
						<div class="js-ticket-messages-data-wrapper">
							<span class="js-ticket-messages-main-text">
						    	'. Text::_('Thank you so much for your feedback') .'
							</span>
							<span class="js-ticket-messages-block_text">
						    	'. jssupportticket::$_config['feedback_thanks_message'] .'
							</span>
						</div>
					</div>';
    	}else{
	        $layout .= '
					<div class="js-ticket-error-message-wrapper js-ticket-box-shadow">
					<div class="js-ticket-message-image-wrapper">
						<img class="js-ticket-message-image" src="'.Uri::root().'components/com_jssupportticket/include/images/error/'.$img_var.'"/>
					</div>
					<div class="js-ticket-messages-data-wrapper">
						<span class="js-ticket-messages-main-text">
					    	' . $text_var_1 . '
						</span>
						<span class="js-ticket-messages-block_text">
					    	' .$text_var_2. '
						</span>
					</div>
				</div>
			';
		}
        echo $layout;
	}
			

		public static function getUserGuest($layout,$Itemid){
			$c = Factory::getApplication()->input->get('c','');
			$link = "index.php?option=com_jssupportticket&c=".$c."&layout=".$layout."&Itemid=".$Itemid;
        	$link = getJSTicketPHPFunctionsClass()->jsticket_urlencode(getJSTicketPHPFunctionsClass()->jsticket_safe_encoding($link));
        	$link = '&return='.$link;
        	$finalurl = 'index.php?option=com_users&view=login'.$link;
/*			$loginval = JSSTincluder::getJSModel('configuration')->getConfigValue('set_login_link');
*//*	        $loginlink = JSSTincluder::getJSModel('configuration')->getConfigValue('login_link');
*/	        $layout = '
	                <div class="js-ticket-error-message-wrapper js-ticket-box-shadow">
						<div class="js-ticket-message-image-wrapper">
							<img class="js-ticket-message-image" src="'.Uri::root().'components/com_jssupportticket/include/images/error/not-login-icon.png"/>
						</div>
						<div class="js-ticket-messages-data-wrapper">
							<span class="js-ticket-messages-main-text">
						    	' . Text::_('You are not logged in') . '
							</span>
							<span class="js-ticket-messages-block_text">
						    	' . Text::_('To access the page, please login') . '
							</span>
							<span class="js-ticket-user-login-btn-wrp">';
                    			$layout .= '<a class="js-ticket-login-btn" href="'.$finalurl.'" title="Login">' . Text::_('Login') . '</a>';

		                        /*if($loginval == 2 && $loginlink != ""){
		                            $layout .= '<a class="js-ticket-login-btn" href="'.$finalurl.'" title="Login">' . Text::_('Login') . '</a>';
		                        }else{
		                            $layout .= '<a class="js-ticket-login-btn" href="'.jssupportticket::makeUrl(array('jstmod'=>'jssupportticket', 'jstlay'=>'login', 'js_redirecturl'=>$redirect_url)).'" title="Login">' . Text::_('Login') . '</a>';
		                        }*/
		                        // $is_enable = get_option('users_can_register');/*check to make sure user registration is enabled*/
	                         //    if ($is_enable) { 
		                        // 	$layout .= '<a class="js-ticket-register-btn" href="'.jssupportticket::makeUrl(array('jstmod'=>'jssupportticket', 'jstlay'=>'userregister', 'js_redirecturl'=>$redirect_url)).'" title="Login">' . Text::_('Register') . '</a>';
		                        // }

	                    	$layout .= '</span> 
	                    </div>
					</div>
	        ';
	        echo $layout;
		}
	}
?>
