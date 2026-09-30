<?php
/**
 * Shared admin page header: top bar (breadcrumb, config button, version)
 * plus the page title / add-button heading block.
 *
 * Expected variables, set by the including template before this include:
 * - $jsstPageTitle     (string, required unless Raw variant used) translation key,
 *                       wrapped in Text::_() here; fallback breadcrumb label too.
 * - $jsstPageTitleRaw  (string, optional) already-final title text, used as-is
 *                       instead of $jsstPageTitle (for titles built from a
 *                       ternary/condition or a concatenation upstream).
 * - $jsstBreadcrumb    (array, optional) [['label' => key, 'label_raw' => text, 'link' => string|null], ...]
 *                       defaults to Dashboard > page title.
 * - $jsstAddLink       (string, optional) href for the "add new" button
 * - $jsstAddLabel      (string, optional) translation key for the "add new" button
 * - $jsstAddLabelRaw   (string, optional) already-final label text, used as-is
 * - $jsstHeadingClass  (string, optional) extra class appended to #js-tk-heading
 * - $jsstSubtitle      (string, optional) translation key for a <p> subtitle
 *                       shown under the <h1>
 * - $jsstSubtitleWrap  (bool, optional) when true, wraps the <h1>+<p> pair in
 *                       an extra <div> (the .jsst-permission-heading layout);
 *                       when false (default), they're direct siblings (the
 *                       .jsst-tve-heading layout)
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;

if (!isset($jsstBreadcrumb)) {
    $jsstBreadcrumb = array(
        array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
        array(
            'label' => isset($jsstPageTitle) ? $jsstPageTitle : null,
            'label_raw' => isset($jsstPageTitleRaw) ? $jsstPageTitleRaw : null,
            'link' => null,
        ),
    );
}
?>
<div id="jsstadmin-wrapper-top">
    <div id="jsstadmin-wrapper-top-left">
        <div id="jsstadmin-breadcrunbs">
            <ul>
                <?php foreach ($jsstBreadcrumb as $jsstCrumb) :
                    $jsstCrumbText = !empty($jsstCrumb['label_raw']) ? $jsstCrumb['label_raw'] : Text::_($jsstCrumb['label']); ?>
                    <li>
                        <?php if (!empty($jsstCrumb['link'])) : ?>
                            <a href="<?php echo $jsstCrumb['link']; ?>" title="<?php echo $jsstCrumbText; ?>"><?php echo $jsstCrumbText; ?></a>
                        <?php else : ?>
                            <?php echo $jsstCrumbText; ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div id="jsstadmin-wrapper-top-right">
        <div id="jsstadmin-config-btn">
            <a title="<?php echo htmlspecialchars(Text::_('Configuration'), ENT_QUOTES, 'UTF-8'); ?>" href="index.php?option=com_jssupportticket&c=config&layout=config">
                <img alt="<?php echo htmlspecialchars(Text::_('Configuration'), ENT_QUOTES, 'UTF-8'); ?>" src="components/com_jssupportticket/include/images/config.png">
            </a>
        </div>
        <div id="jsstadmin-vers-txt">
            <?php echo Text::_('Version') . ' : '; ?>
            <span class="jsstadmin-ver">
                <?php echo isset($this->versionDisplay) ? $this->versionDisplay : ''; ?>
            </span>
        </div>
    </div>
</div>
<?php
$jsstHeadingTitleHtml = '<h1 class="jsstadmin-head-text">' . (!empty($jsstPageTitleRaw) ? $jsstPageTitleRaw : Text::_($jsstPageTitle)) . '</h1>';
$jsstHeadingSubtitleHtml = !empty($jsstSubtitle) ? '<p>' . Text::_($jsstSubtitle) . '</p>' : '';
?>
<div id="js-tk-heading"<?php echo !empty($jsstHeadingClass) ? ' class="' . $jsstHeadingClass . '"' : ''; ?>>
    <?php if (!empty($jsstSubtitleWrap)) : ?>
        <div>
            <?php echo $jsstHeadingTitleHtml . $jsstHeadingSubtitleHtml; ?>
        </div>
    <?php else : ?>
        <?php echo $jsstHeadingTitleHtml . $jsstHeadingSubtitleHtml; ?>
    <?php endif; ?>
    <?php if (!empty($jsstAddLink)) : ?>
        <a class="tk-heading-addbutton" href="<?php echo $jsstAddLink; ?>">
            <img class="js-heading-addimage" src="components/com_jssupportticket/include/images/plus.png">
            <?php echo !empty($jsstAddLabelRaw) ? $jsstAddLabelRaw : Text::_($jsstAddLabel); ?>
        </a>
    <?php endif; ?>
</div>
