/*!
 * @package     Guest Support
 * @author      RcaTheme.com <support@rcatheme.com>
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link        https://rcatheme.com
 * @copyright   2025 RcaTheme.com
 */
jQuery(document).ready(function($) {
    // Add fields
    var $wtPreloader = '<div class="r-gs-loader"><span></span><span></span><span></span><span></span><span></span></div>';
    $(document).on('change', 'select#r_gs_add_field_type', function() {
        $('div#r_gs_modal_main_content').html($wtPreloader);
        var $value = $(this).val();
        if ( $value ) {
            var $content = 'The Pro version is required to add new fields.';
            setTimeout(function(){
                $('div#r_gs_modal_main_content').html($content);
            }, 500);
        } else {
            $('div#r_gs_modal_main_content').html("Select input field type to continue.");
        }
    });
});