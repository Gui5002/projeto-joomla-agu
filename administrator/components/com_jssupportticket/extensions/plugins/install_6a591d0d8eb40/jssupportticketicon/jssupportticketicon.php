<?php
/**
 + Created by:  Ahmad Bilal
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
                www.joomsky.com, ahmad@joomsky.com
 * Created on:  Dec 2, 2009
 ^
 + Project:         JS Jobs 
 * File Name:   module/hotjsjobs.php
 ^ 
 * Description: Module for JS Jobs
 ^ 
 * History:     1.0.2 - Nov 27, 2010
 ^ 
 */
 // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;    
use Joomla\CMS\Plugin\CMSPlugin;    

jimport('joomla.plugin.plugin');

/**
 * JS Support Ticket icon system plugin
 */
class plgSystemJSSupportticketicon extends CMSPlugin{
    /**
    * Constructor.
    *
    * @access protected
    * @param object $subject The object to observe
    * @param array $config  An array that holds the plugin configuration
    * @since 1.0
    */
    public function __construct( &$subject, $config ){
        parent::__construct( $subject, $config );
        // Do some extra initialisation in this constructor if required
    }

  /**
     * onAfterRender Hook.
     */
    function onAfterRender() {
    $position = $this->params->get('position',1);
    $version = new Version;
    $joomla = $version->getShortVersion();
    $jversion = substr($joomla, 0, 3);
    $document = Factory::getDocument();
    if (!defined('JVERSION')) {
        define('JVERSION', $jversion);
    }
    if (JVERSION < 3) {
        HTMLHelper::_('behavior.mootools');
        $document->addScript('components/com_jssupportticket/include/js/jquery.js');
    } else {
        HTMLHelper::_('bootstrap.framework');
        HTMLHelper::_('jquery.framework');
    }

    if (!Factory::getApplication()->isadmin()) { // we need to show the support ticket tag
        $location = 'left';
        $borderradius = '0px 8px 8px 0px';
        $padding = '5px 10px 5px 20px';
        switch ($position) {
            case 1: // Top left
                $top = "30px";
                $left = "0px";
                $right = "none";
                $bottom = "none";
            break;
            case 2: // Top right
                $top = "30px";
                $left = "none";
                $right = "0px";
                $bottom = "none";
                $location = 'right';
                $borderradius = '8px 0px 0px 8px';
                $padding = '5px 20px 5px 10px';
            break;
            case 3: // middle left
                $top = "48%";
                $left = "0px";
                $right = "none";
                $bottom = "none";
            break;
            case 4: // middle right
                $top = "48%";
                $left = "none";
                $right = "0px";
                $bottom = "none";
                $location = 'right';
                $borderradius = '8px 0px 0px 8px';
                $padding = '5px 20px 5px 10px';
            break;
            case 5: // bottom left
                $top = "none";
                $left = "0px";
                $right = "none";
                $bottom = "30px";
            break;
            case 6: // bottom right
                $top = "none";
                $left = "none";
                $right = "0px";
                $bottom = "30px";
                $location = 'right';
                $borderradius = '8px 0px 0px 8px';
                $padding = '5px 20px 5px 10px';
            break;
        }
        $html = '<style type="text/css">
                    div#jsjobs_screentag{opacity:0;position:fixed;top:'.$top.';left:'.$left.';right:'.$right.';bottom:'.$bottom.';padding:'.$padding.';background:rgba(149,149,149,.50);z-index:9999;border-radius:'.$borderradius.';}
                    div#jsjobs_screentag img{margin-'.$location.':10px;display:inline-block;}
                    div#jsjobs_screentag a{color:#ffffff;text-decoration:none;}
                </style>
                <div id="jsjobs_screentag">';
        if($location == 'right'){
            $html .= '<img src="'.Uri::root().'components/com_jssupportticket/include/images/support-icon.png" /><a href="'.Route::_('index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel').'">'.Text::_("Support").'</a>';
        }else{
            $html .= '<a href="'.Route::_('index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel').'">'.Text::_("Support").'</a><img src="'.Uri::root().'components/com_jssupportticket/include/images/support-icon.png" />';
        }
        $html .= '
                </div>
                <script type="text/javascript">
                window.onload = function(){
                    jQuery(document).ready(function(){
                        jQuery("div#jsjobs_screentag").css("'.$location.'","-"+(jQuery("div#jsjobs_screentag a").width() + 25)+"px");
                        jQuery("div#jsjobs_screentag").css("opacity",1);
                        jQuery("div#jsjobs_screentag").hover(
                            function(){
                                jQuery(this).animate({'.$location.': "+="+(jQuery("div#jsjobs_screentag a").width() + 25)}, 1000);
                            },
                            function(){
                                jQuery(this).animate({'.$location.': "-="+(jQuery("div#jsjobs_screentag a").width() + 25)}, 1000);
                            }
                        );
                    });
                };
                </script>
                ';
        echo $html;
    }
  }
}

?>
