<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 * Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Project:     JS Tickets
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
if (JVERSION >= 3) {
    HTMLHelper::_('jquery.framework');
}

$document = Factory::getDocument();

$theme = isset($this->result[0]) && is_array($this->result[0]) ? $this->result[0] : array();
$defaults = array(
    'color1' => '#2563eb',
    'color2' => '#1e293b',
    'color3' => '#f8fafc',
    'color4' => '#334155',
    'color5' => '#dbe4ef',
    'color6' => '#eff6ff',
    'color7' => '#ffffff',
);
for ($i = 1; $i <= 7; $i++) {
    $key = 'color' . $i;
    if (empty($theme[$key]) || !preg_match('/^#[0-9a-fA-F]{6}$/', $theme[$key])) {
        $theme[$key] = $defaults[$key];
    }
}
$colorFileExists = !empty($theme['color_file_exists']);
$colorFileWritable = !empty($theme['color_file_writable']);
$colorFilePath = isset($theme['color_file_path']) ? $theme['color_file_path'] : '';

$paletteData = htmlspecialchars(json_encode($defaults), ENT_QUOTES, 'UTF-8');
$presetBlue = htmlspecialchars(json_encode($defaults), ENT_QUOTES, 'UTF-8');
$presetEmerald = htmlspecialchars(json_encode(array(
    'color1' => '#047857', 'color2' => '#064e3b', 'color3' => '#f0fdf4', 'color4' => '#1f2937', 'color5' => '#bbf7d0', 'color6' => '#d1fae5', 'color7' => '#ffffff'
)), ENT_QUOTES, 'UTF-8');
$presetSlate = htmlspecialchars(json_encode(array(
    'color1' => '#334155', 'color2' => '#0f172a', 'color3' => '#f8fafc', 'color4' => '#1e293b', 'color5' => '#cbd5e1', 'color6' => '#e2e8f0', 'color7' => '#ffffff'
)), ENT_QUOTES, 'UTF-8');
$presetPurple = htmlspecialchars(json_encode(array(
    'color1' => '#7c3aed', 'color2' => '#3b0764', 'color3' => '#faf5ff', 'color4' => '#312e81', 'color5' => '#ddd6fe', 'color6' => '#f3e8ff', 'color7' => '#ffffff'
)), ENT_QUOTES, 'UTF-8');
?>
<style>
#jsst-admin #jsst-theme-modern{--t1:<?php echo htmlspecialchars($theme['color1'], ENT_QUOTES, 'UTF-8'); ?>;--t2:<?php echo htmlspecialchars($theme['color2'], ENT_QUOTES, 'UTF-8'); ?>;--t3:<?php echo htmlspecialchars($theme['color3'], ENT_QUOTES, 'UTF-8'); ?>;--t4:<?php echo htmlspecialchars($theme['color4'], ENT_QUOTES, 'UTF-8'); ?>;--t5:<?php echo htmlspecialchars($theme['color5'], ENT_QUOTES, 'UTF-8'); ?>;--t6:<?php echo htmlspecialchars($theme['color6'], ENT_QUOTES, 'UTF-8'); ?>;--t7:<?php echo htmlspecialchars($theme['color7'], ENT_QUOTES, 'UTF-8'); ?>;}
#jsst-admin #jsst-theme-modern .jsst-theme-alert{margin:0 32px 22px;padding:16px 18px;border-radius:16px;border:1px solid #fed7aa;background:#fff7ed;color:#9a3412;font-weight:700;}
#jsst-admin #jsst-theme-modern .jsst-theme-alert span{display:block;margin-top:6px;font-weight:600;color:#9a3412;opacity:.82;}
#jsst-admin #jsst-theme-modern .jsst-theme-shell{padding:28px 32px 34px;}
#jsst-admin #jsst-theme-modern .jsst-theme-intro{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:22px;align-items:stretch;margin-bottom:22px;padding:28px;border-radius:22px;background:linear-gradient(135deg,#eff6ff 0%,#f0fdfa 100%);border:1px solid #dbeafe;}
#jsst-admin #jsst-theme-modern .jsst-theme-badge{display:inline-flex;align-items:center;width:max-content;border-radius:999px;background:#dbeafe;color:#0b57d0;padding:6px 11px;font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px;}
#jsst-admin #jsst-theme-modern .jsst-theme-intro h2{margin:0;color:#101827;font-size:24px;line-height:1.25;font-weight:900;}
#jsst-admin #jsst-theme-modern .jsst-theme-intro p{max-width:720px;margin:9px 0 0;color:#53657f;font-size:14px;line-height:1.6;font-weight:600;}
#jsst-admin #jsst-theme-modern .jsst-theme-steps{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
#jsst-admin #jsst-theme-modern .jsst-theme-step{display:flex;gap:10px;align-items:center;padding:14px 16px;border-radius:16px;background:rgba(255,255,255,.8);border:1px solid #d8e7ff;color:#101827;font-weight:900;box-shadow:0 10px 24px rgba(37,99,235,.06);}
#jsst-admin #jsst-theme-modern .jsst-theme-step strong{display:grid;place-items:center;width:24px;height:24px;border-radius:999px;background:#2563eb;color:#fff;font-size:12px;flex:0 0 auto;}
#jsst-admin #jsst-theme-modern .jsst-theme-grid{display:grid;grid-template-columns:minmax(320px,430px) minmax(0,1fr);gap:24px;align-items:start;}
#jsst-admin #jsst-theme-modern .jsst-theme-card{background:#fff;border:1px solid #dbe4ef;border-radius:22px;box-shadow:0 18px 40px rgba(15,23,42,.05);overflow:hidden;}
#jsst-admin #jsst-theme-modern .jsst-theme-card-head{display:flex;justify-content:space-between;align-items:flex-start;gap:14px;padding:20px 22px;border-bottom:1px solid #e5edf7;}
#jsst-admin #jsst-theme-modern .jsst-theme-card-head h3{margin:0;color:#101827;font-size:18px;font-weight:900;}
#jsst-admin #jsst-theme-modern .jsst-theme-card-head p{margin:5px 0 0;color:#64748b;font-size:13px;font-weight:600;line-height:1.45;}
#jsst-admin #jsst-theme-modern .jsst-theme-preset-wrap{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;padding:18px 22px;border-bottom:1px solid #e5edf7;background:#fbfdff;}
#jsst-admin #jsst-theme-modern .jsst-theme-preset{appearance:none;border:1px solid #cfe0f5;background:#f8fbff;color:#0b57d0;border-radius:14px;padding:12px;display:flex;align-items:center;justify-content:space-between;gap:10px;font-weight:900;cursor:pointer;transition:.18s ease;}
#jsst-admin #jsst-theme-modern .jsst-theme-preset:hover{transform:translateY(-1px);box-shadow:0 12px 24px rgba(37,99,235,.12);border-color:#93c5fd;}
#jsst-admin #jsst-theme-modern .jsst-theme-preset.is-active{background:#2563eb;color:#fff;border-color:#2563eb;box-shadow:0 14px 30px rgba(37,99,235,.18);}
#jsst-admin #jsst-theme-modern .jsst-theme-preset.is-active .jsst-theme-preset-dots i{border-color:rgba(255,255,255,.45);}
#jsst-admin #jsst-theme-modern .jsst-theme-preset-dots{display:flex;gap:4px;}
#jsst-admin #jsst-theme-modern .jsst-theme-preset-dots i{width:13px;height:13px;border-radius:50%;display:block;border:1px solid rgba(15,23,42,.08);}
#jsst-admin #jsst-theme-modern .jsst-theme-fields{padding:18px 22px;display:grid;gap:13px;}
#jsst-admin #jsst-theme-modern .jsst-theme-field{display:grid;grid-template-columns:46px minmax(0,1fr);gap:12px;align-items:center;padding:13px;border-radius:16px;background:#f8fbff;border:1px solid #dde8f7;}
#jsst-admin #jsst-theme-modern .jsst-theme-field input[type=color]{width:46px;height:46px;padding:0;border:1px solid #c9d8ea;border-radius:13px;background:#fff;cursor:pointer;overflow:hidden;}
#jsst-admin #jsst-theme-modern .jsst-theme-field label{display:block;color:#101827;font-size:13px;font-weight:900;margin:0 0 4px;}
#jsst-admin #jsst-theme-modern .jsst-theme-field span{display:block;color:#64748b;font-size:12px;font-weight:600;line-height:1.35;}
#jsst-admin #jsst-theme-modern .jsst-theme-field input[type=text]{width:100%;min-height:38px;border:1px solid #d8e2ef;border-radius:12px;background:#fff;color:#101827;font-weight:800;padding:8px 11px;margin-top:9px;box-shadow:none;}
#jsst-admin #jsst-theme-modern .jsst-theme-field.jsst-theme-field-warning{border-color:#f59e0b;background:#fffbeb;}
#jsst-admin #jsst-theme-modern .jsst-theme-field.jsst-theme-field-error{border-color:#ef4444;background:#fff1f2;}
#jsst-admin #jsst-theme-modern .jsst-theme-actions{display:flex;justify-content:center;align-items:center;gap:12px;padding:20px 22px;border-top:1px solid #e5edf7;background:#fff;}
#jsst-admin #jsst-theme-modern .jsst-theme-save,#jsst-admin #jsst-theme-modern .jsst-theme-reset{border:0;border-radius:12px;min-height:44px;padding:0 22px;font-weight:900;cursor:pointer;}
#jsst-admin #jsst-theme-modern .jsst-theme-save{background:#2563eb;color:#fff;box-shadow:0 14px 28px rgba(37,99,235,.18);}
#jsst-admin #jsst-theme-modern .jsst-theme-reset{background:#111827;color:#fff;}
#jsst-admin #jsst-theme-modern .jsst-theme-contrast{margin:18px 22px 0;border:1px solid #bfdbfe;background:#eff6ff;border-radius:16px;padding:14px 16px;color:#1e3a8a;font-weight:700;line-height:1.5;}
#jsst-admin #jsst-theme-modern .jsst-theme-contrast strong{display:block;color:#0f172a;font-size:14px;font-weight:900;margin-bottom:3px;}
#jsst-admin #jsst-theme-modern .jsst-theme-contrast ul{margin:7px 0 0 18px;padding:0;}
#jsst-admin #jsst-theme-modern .jsst-theme-contrast li{margin:3px 0;}
#jsst-admin #jsst-theme-modern .jsst-theme-contrast.is-ok{border-color:#bbf7d0;background:#f0fdf4;color:#166534;}
#jsst-admin #jsst-theme-modern .jsst-theme-contrast.is-warning{border-color:#fed7aa;background:#fff7ed;color:#9a3412;}
#jsst-admin #jsst-theme-modern .jsst-theme-contrast.is-error{border-color:#fecaca;background:#fff1f2;color:#991b1b;}
#jsst-admin #jsst-theme-modern .jsst-theme-contrast.is-error strong{color:#7f1d1d;}
#jsst-admin #jsst-theme-modern .jsst-theme-save[disabled]{opacity:.55;cursor:not-allowed;box-shadow:none;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview{padding:22px;background:#f8fafc;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-frame{border:1px solid var(--t5);border-radius:22px;background:var(--t3);overflow:hidden;box-shadow:0 24px 50px rgba(15,23,42,.08);}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-nav{display:flex;align-items:center;gap:12px;background:var(--t1);color:var(--t7);padding:16px;border-bottom:4px solid var(--t2);}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-logo{width:40px;height:40px;border-radius:14px;display:grid;place-items:center;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.3);font-size:20px;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-nav a{color:var(--t7);text-decoration:none;border-radius:12px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.22);padding:11px 15px;font-weight:900;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-body{padding:26px;color:var(--t4);}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-body h3{margin:0 0 8px;color:var(--t2);font-size:25px;font-weight:900;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-body p{margin:0 0 18px;color:var(--t4);font-weight:600;line-height:1.55;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:18px;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-stat{border:1px solid var(--t5);background:var(--t3);border-radius:16px;padding:17px;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-stat strong{display:block;color:var(--t2);font-size:24px;font-weight:900;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-stat span{display:block;color:var(--t4);font-weight:800;margin-top:4px;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-ticket{border:1px solid var(--t5);border-radius:16px;background:var(--t3);overflow:hidden;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-ticket-head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;background:var(--t2);color:var(--t7);font-weight:900;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-ticket-row{display:grid;grid-template-columns:1fr auto;align-items:center;gap:18px;padding:18px 16px;color:var(--t4);}
#jsst-admin #jsst-theme-modern .jsst-theme-pill{border-radius:999px;background:var(--t6);color:var(--t6text,#101827);border:1px solid var(--t5);padding:8px 14px;font-weight:900;}
#jsst-admin #jsst-theme-modern .jsst-theme-preview-button{display:inline-flex;align-items:center;justify-content:center;border-radius:12px;background:var(--t1);border:1px solid var(--t5);color:var(--t7);padding:11px 18px;font-weight:900;text-decoration:none;}
#jsst-admin #jsst-theme-modern .jsst-theme-guidance{margin-top:18px;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;}
#jsst-admin #jsst-theme-modern .jsst-theme-guide{border:1px solid #dbe4ef;background:#fff;border-radius:16px;padding:15px;color:#53657f;font-weight:700;line-height:1.45;}
#jsst-admin #jsst-theme-modern .jsst-theme-guide strong{display:block;color:#101827;font-weight:900;margin-bottom:5px;}
@media(max-width:1200px){#jsst-admin #jsst-theme-modern .jsst-theme-intro,#jsst-admin #jsst-theme-modern .jsst-theme-grid{grid-template-columns:1fr;}#jsst-admin #jsst-theme-modern .jsst-theme-guidance{grid-template-columns:1fr;}}
@media(max-width:760px){#jsst-admin #jsst-theme-modern .jsst-theme-shell{padding:18px;}#jsst-admin #jsst-theme-modern .jsst-theme-intro{padding:20px;}#jsst-admin #jsst-theme-modern .jsst-theme-steps,#jsst-admin #jsst-theme-modern .jsst-theme-preset-wrap,#jsst-admin #jsst-theme-modern .jsst-theme-preview-stats{grid-template-columns:1fr;}#jsst-admin #jsst-theme-modern .jsst-theme-preview-nav{overflow:auto;}#jsst-admin #jsst-theme-modern .jsst-theme-actions{display:grid;grid-template-columns:1fr;}}

#jsst-admin div#js-tk-admin-wrapper.jsst-theme-builder-v81 form#adminForm{display:block !important;grid-template-columns:none !important;gap:0 !important;width:100% !important;max-width:100% !important;}
#jsst-admin div#js-tk-admin-wrapper.jsst-theme-builder-v81 form#adminForm > #jsst-theme-modern{display:block !important;grid-column:1 / -1 !important;width:100% !important;max-width:100% !important;min-width:0 !important;}
#jsst-admin div#js-tk-admin-wrapper.jsst-theme-builder-v81 #jsstadmin-data-wrp{padding:0 !important;overflow:visible !important;}
#jsst-admin div#js-tk-admin-wrapper.jsst-theme-builder-v81 #jsst-theme-modern *{box-sizing:border-box;}
</style>

<div id="js-tk-admin-wrapper" class="jsst-screen jsst-screen-form jsst-theme-builder-v81">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <?php
$jsstPageTitle = 'Themes';
$jsstBreadcrumb = array(
    array('label' => 'Dashboard', 'link' => 'index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel'),
    array('label_raw' => Text::_('Themes'), 'link' => null),
);
include_once('components/com_jssupportticket/views/partials/pageheader.php');
?>
        <div id="jsstadmin-data-wrp" class="js-ticket-pagination-shadow">
            <form action="index.php" method="POST" name="adminForm" id="adminForm" class="form-validate">
                <div id="jsst-theme-modern" data-defaults="<?php echo $paletteData; ?>">
                    <?php if (!$colorFileExists || !$colorFileWritable) { ?>
                        <div class="jsst-theme-alert">
                            <?php echo Text::_('Theme color file needs attention.'); ?>
                            <span>
                                <?php echo Text::_('The theme can still be prepared here, but saving requires the color file or its folder to be writable.'); ?>
                                <?php if ($colorFilePath !== '') { echo ' ' . htmlspecialchars($colorFilePath, ENT_QUOTES, 'UTF-8'); } ?>
                            </span>
                        </div>
                    <?php } ?>
                    <div class="jsst-theme-shell">
                        <div class="jsst-theme-intro">
                            <div>
                                <span class="jsst-theme-badge"><?php echo Text::_('Theme builder'); ?></span>
                                <h2><?php echo Text::_('Control the customer-facing support colors from one safe screen.'); ?></h2>
                                <p><?php echo Text::_('Choose a preset, adjust the exact colors, preview the result, and save. Hex colors are validated before they are written to the theme color file.'); ?></p>
                            </div>
                            <div class="jsst-theme-steps">
                                <div class="jsst-theme-step"><strong>1</strong><?php echo Text::_('Choose preset'); ?></div>
                                <div class="jsst-theme-step"><strong>2</strong><?php echo Text::_('Fine tune colors'); ?></div>
                                <div class="jsst-theme-step"><strong>3</strong><?php echo Text::_('Check preview'); ?></div>
                                <div class="jsst-theme-step"><strong>4</strong><?php echo Text::_('Save theme'); ?></div>
                            </div>
                        </div>

                        <div class="jsst-theme-grid">
                            <div class="jsst-theme-card">
                                <div class="jsst-theme-card-head">
                                    <div>
                                        <h3><?php echo Text::_('Color settings'); ?></h3>
                                        <p><?php echo Text::_('These fields map to the existing seven theme color variables, so old saved themes remain compatible.'); ?></p>
                                    </div>
                                </div>
                                <div class="jsst-theme-preset-wrap">
                                    <button type="button" class="jsst-theme-preset" data-preset-name="Default blue" data-preset="<?php echo $presetBlue; ?>"><span><?php echo Text::_('Default blue'); ?></span><span class="jsst-theme-preset-dots"><i style="background:#2563eb"></i><i style="background:#1e293b"></i><i style="background:#eff6ff"></i></span></button>
                                    <button type="button" class="jsst-theme-preset" data-preset-name="Emerald" data-preset="<?php echo $presetEmerald; ?>"><span><?php echo Text::_('Emerald'); ?></span><span class="jsst-theme-preset-dots"><i style="background:#047857"></i><i style="background:#064e3b"></i><i style="background:#d1fae5"></i></span></button>
                                    <button type="button" class="jsst-theme-preset" data-preset-name="Slate" data-preset="<?php echo $presetSlate; ?>"><span><?php echo Text::_('Slate'); ?></span><span class="jsst-theme-preset-dots"><i style="background:#334155"></i><i style="background:#0f172a"></i><i style="background:#e2e8f0"></i></span></button>
                                    <button type="button" class="jsst-theme-preset" data-preset-name="Purple" data-preset="<?php echo $presetPurple; ?>"><span><?php echo Text::_('Purple'); ?></span><span class="jsst-theme-preset-dots"><i style="background:#7c3aed"></i><i style="background:#3b0764"></i><i style="background:#f3e8ff"></i></span></button>
                                </div>
                                <div class="jsst-theme-fields">
                                    <?php
                                    $fields = array(
                                        'color1' => array(Text::_('Top menu background'), Text::_('Main navigation and header background color.')),
                                        'color2' => array(Text::_('Heading and accent color'), Text::_('Heading text, top line, selected and hover accents.')),
                                        'color3' => array(Text::_('Content background'), Text::_('Main content surface behind lists and detail cards.')),
                                        'color4' => array(Text::_('Content text color'), Text::_('Normal text, meta text, and customer-facing labels.')),
                                        'color5' => array(Text::_('Borders and dividers'), Text::_('Card borders, rows, separators, and soft lines.')),
                                        'color6' => array(Text::_('Secondary button and badge background'), Text::_('Soft badges and secondary button surfaces.')),
                                        'color7' => array(Text::_('Header and action text'), Text::_('Text color inside top menu, dark section bars, and primary actions.')),
                                    );
                                    foreach ($fields as $key => $labels) {
                                        $value = htmlspecialchars($theme[$key], ENT_QUOTES, 'UTF-8');
                                    ?>
                                        <div class="jsst-theme-field">
                                            <input type="color" class="jsst-theme-picker" data-target="<?php echo $key; ?>" value="<?php echo $value; ?>" aria-label="<?php echo htmlspecialchars($labels[0], ENT_QUOTES, 'UTF-8'); ?>">
                                            <div>
                                                <label for="<?php echo $key; ?>"><?php echo $labels[0]; ?></label>
                                                <span><?php echo $labels[1]; ?></span>
                                                <input type="text" class="jsst-theme-hex required" name="<?php echo $key; ?>" id="<?php echo $key; ?>" value="<?php echo $value; ?>" maxlength="7" data-var="--t<?php echo (int) substr($key, -1); ?>">
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="jsst-theme-actions">
                                    <button type="button" class="jsst-theme-reset" id="jsst-theme-reset"><?php echo Text::_('Reset to default'); ?></button>
                                    <button type="submit" class="jsst-theme-save"><?php echo Text::_('Save Theme'); ?></button>
                                </div>
                            </div>

                            <div class="jsst-theme-card">
                                <div class="jsst-theme-card-head">
                                    <div>
                                        <h3><?php echo Text::_('Live preview'); ?></h3>
                                        <p><?php echo Text::_('Preview uses the same color variables that are saved for the frontend support pages.'); ?></p>
                                    </div>
                                </div>
                                <div class="jsst-theme-contrast is-ok" id="jsst-theme-contrast" aria-live="polite">
                                    <strong><?php echo Text::_('Readability check'); ?></strong>
                                    <span><?php echo Text::_('Theme colors and all built-in presets are checked before save so unreadable customer pages are not created.'); ?></span>
                                </div>
                                <div class="jsst-theme-preview">
                                    <div class="jsst-theme-preview-frame">
                                        <div class="jsst-theme-preview-nav">
                                            <div class="jsst-theme-preview-logo">☸</div>
                                            <a href="#" onclick="return false;"><?php echo Text::_('Dashboard'); ?></a>
                                            <a href="#" onclick="return false;"><?php echo Text::_('New Ticket'); ?></a>
                                            <a href="#" onclick="return false;"><?php echo Text::_('My Tickets'); ?></a>
                                            <a href="#" onclick="return false;"><?php echo Text::_('Knowledge Base'); ?></a>
                                        </div>
                                        <div class="jsst-theme-preview-body">
                                            <h3><?php echo Text::_('Support Center'); ?></h3>
                                            <p><?php echo Text::_('Customers see these colors on support navigation, cards, headings, buttons and content areas.'); ?></p>
                                            <div class="jsst-theme-preview-stats">
                                                <div class="jsst-theme-preview-stat"><strong>2</strong><span><?php echo Text::_('Open tickets'); ?></span></div>
                                                <div class="jsst-theme-preview-stat"><strong>1</strong><span><?php echo Text::_('Answered'); ?></span></div>
                                                <div class="jsst-theme-preview-stat"><strong>0</strong><span><?php echo Text::_('Overdue'); ?></span></div>
                                            </div>
                                            <div class="jsst-theme-preview-ticket">
                                                <div class="jsst-theme-preview-ticket-head"><span><?php echo Text::_('Latest Ticket'); ?></span><span>#JS-1042</span></div>
                                                <div class="jsst-theme-preview-ticket-row">
                                                    <div>
                                                        <strong><?php echo Text::_('Login issue after update'); ?></strong><br>
                                                        <span><?php echo Text::_('Department'); ?>: <?php echo Text::_('Support'); ?> · <?php echo Text::_('Priority'); ?>: <?php echo Text::_('Normal'); ?></span>
                                                    </div>
                                                    <span class="jsst-theme-pill"><?php echo Text::_('NEW'); ?></span>
                                                </div>
                                            </div>
                                            <p style="margin-top:18px;"><a href="#" class="jsst-theme-preview-button" onclick="return false;"><?php echo Text::_('Create Ticket'); ?></a></p>
                                        </div>
                                    </div>
                                    <div class="jsst-theme-guidance">
                                        <div class="jsst-theme-guide"><strong><?php echo Text::_('Readable contrast'); ?></strong><?php echo Text::_('Keep menu text readable against the selected top menu background.'); ?></div>
                                        <div class="jsst-theme-guide"><strong><?php echo Text::_('Soft borders'); ?></strong><?php echo Text::_('Use a light border color for cleaner cards and tables.'); ?></div>
                                        <div class="jsst-theme-guide"><strong><?php echo Text::_('Safe storage'); ?></strong><?php echo Text::_('Only validated hex colors are saved. No secrets are written in the theme file.'); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="option" value="com_jssupportticket" />
                    <input type="hidden" name="c" value="jssupportticket" />
                    <input type="hidden" name="task" value="savetheme" />
                    <?php echo HTMLHelper::_('form.token'); ?>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once('components/com_jssupportticket/views/partials/pagefooter.php'); ?>
<script type="text/javascript">
(function(){
    function getRoot(){ return document.getElementById('jsst-theme-modern'); }
    function isHex(value){ return /^#[0-9a-fA-F]{6}$/.test(value || ''); }
    function normalizeHex(value){
        value = (value || '').trim();
        if(value !== '' && value.charAt(0) !== '#'){ value = '#' + value; }
        return value.toLowerCase();
    }
    function hexToRgb(value){
        value = normalizeHex(value);
        if(!isHex(value)){ return null; }
        return {
            r: parseInt(value.substr(1, 2), 16),
            g: parseInt(value.substr(3, 2), 16),
            b: parseInt(value.substr(5, 2), 16)
        };
    }
    function luminance(value){
        var rgb = hexToRgb(value);
        if(!rgb){ return 0; }
        var values = [rgb.r / 255, rgb.g / 255, rgb.b / 255].map(function(channel){
            return channel <= 0.03928 ? channel / 12.92 : Math.pow((channel + 0.055) / 1.055, 2.4);
        });
        return (0.2126 * values[0]) + (0.7152 * values[1]) + (0.0722 * values[2]);
    }
    function contrastRatio(foreground, background){
        var first = luminance(foreground);
        var second = luminance(background);
        var light = Math.max(first, second);
        var dark = Math.min(first, second);
        return (light + 0.05) / (dark + 0.05);
    }
    function bestTextOn(background){
        var white = contrastRatio('#ffffff', background);
        var dark = contrastRatio('#101827', background);
        return white >= dark ? '#ffffff' : '#101827';
    }
    function getValue(key){
        var field = document.getElementById(key);
        return field ? normalizeHex(field.value) : '';
    }
    function setColor(key, value){
        var root = getRoot();
        var text = document.getElementById(key);
        var picker = document.querySelector('.jsst-theme-picker[data-target="' + key + '"]');
        value = normalizeHex(value);
        if(!isHex(value)){ return false; }
        if(text){ text.value = value; }
        if(picker){ picker.value = value; }
        if(root){ root.style.setProperty('--t' + key.replace('color',''), value); }
        updateThemePreviewSafety();
        return true;
    }
    function applyPalette(palette){
        for(var i = 1; i <= 7; i++){
            if(palette['color' + i]){ setColor('color' + i, palette['color' + i]); }
        }
        updateThemePreviewSafety();
        updateActivePreset();
    }
    function paletteMatchesCurrent(palette){
        for(var i = 1; i <= 7; i++){
            if(normalizeHex(palette['color' + i] || '') !== getValue('color' + i)){ return false; }
        }
        return true;
    }
    function updateActivePreset(){
        document.querySelectorAll('#jsst-theme-modern .jsst-theme-preset').forEach(function(button){
            var active = false;
            try { active = paletteMatchesCurrent(JSON.parse(button.getAttribute('data-preset') || '{}')); } catch(e) {}
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    }
    function showFieldError(field, hasError){
        if(!field){ return; }
        field.style.borderColor = hasError ? '#ef4444' : '#d8e2ef';
        field.style.boxShadow = hasError ? '0 0 0 4px rgba(239,68,68,.08)' : 'none';
        var wrap = field.closest ? field.closest('.jsst-theme-field') : null;
        if(wrap){ wrap.classList.toggle('jsst-theme-field-error', !!hasError); }
    }
    function clearContrastMarkers(){
        document.querySelectorAll('#jsst-theme-modern .jsst-theme-field').forEach(function(field){
            field.classList.remove('jsst-theme-field-warning');
        });
    }
    function markFields(keys){
        keys.forEach(function(key){
            var input = document.getElementById(key);
            var field = input && input.closest ? input.closest('.jsst-theme-field') : null;
            if(field){ field.classList.add('jsst-theme-field-warning'); }
        });
    }
    function makeIssue(label, foregroundKey, backgroundKey, ratio, minimum){
        markFields([foregroundKey, backgroundKey]);
        return label + ' (' + ratio.toFixed(2) + ':1, minimum ' + minimum + ':1)';
    }
    function updateThemePreviewSafety(){
        var root = getRoot();
        if(!root){ return true; }
        var c1 = getValue('color1'), c2 = getValue('color2'), c3 = getValue('color3'), c4 = getValue('color4'), c6 = getValue('color6'), c7 = getValue('color7');
        if(isHex(c6)){ root.style.setProperty('--t6text', bestTextOn(c6)); }

        clearContrastMarkers();
        var issues = [];
        var validHex = true;
        for(var i = 1; i <= 7; i++){
            if(!isHex(getValue('color' + i))){ validHex = false; }
        }
        if(validHex){
            var menuRatio = contrastRatio(c7, c1);
            var sectionRatio = contrastRatio(c7, c2);
            var headingRatio = contrastRatio(c2, c3);
            var bodyRatio = contrastRatio(c4, c3);
            if(menuRatio < 3){ issues.push(makeIssue('<?php echo addslashes(Text::_('Header and action text against top menu background')); ?>', 'color7', 'color1', menuRatio, 3)); }
            if(sectionRatio < 4.5){ issues.push(makeIssue('<?php echo addslashes(Text::_('Header and action text against dark section bars')); ?>', 'color7', 'color2', sectionRatio, 4.5)); }
            if(headingRatio < 3){ issues.push(makeIssue('<?php echo addslashes(Text::_('Heading color against content background')); ?>', 'color2', 'color3', headingRatio, 3)); }
            if(bodyRatio < 4.5){ issues.push(makeIssue('<?php echo addslashes(Text::_('Content text against content background')); ?>', 'color4', 'color3', bodyRatio, 4.5)); }
        }

        var box = document.getElementById('jsst-theme-contrast');
        var save = document.querySelector('#jsst-theme-modern .jsst-theme-save');
        if(!box){ return issues.length === 0 && validHex; }
        box.classList.remove('is-ok', 'is-warning', 'is-error');
        if(!validHex){
            box.classList.add('is-error');
            box.innerHTML = '<strong><?php echo addslashes(Text::_('Readability check')); ?></strong><span><?php echo addslashes(Text::_('Please enter valid 6-digit hex colors before saving.')); ?></span>';
            if(save){ save.disabled = true; }
            return false;
        }
        if(issues.length){
            box.classList.add('is-error');
            box.innerHTML = '<strong><?php echo addslashes(Text::_('Readability needs attention')); ?></strong><span><?php echo addslashes(Text::_('These color pairs are too close and may make the support pages unreadable.')); ?></span><ul><li>' + issues.join('</li><li>') + '</li></ul>';
            if(save){ save.disabled = true; }
            return false;
        }
        box.classList.add('is-ok');
        box.innerHTML = '<strong><?php echo addslashes(Text::_('Readability check passed')); ?></strong><span><?php echo addslashes(Text::_('The main menu, section bars, headings, and body text have safe contrast for the customer-facing support pages.')); ?></span>';
        if(save){ save.disabled = false; }
        return true;
    }

    document.querySelectorAll('#jsst-theme-modern .jsst-theme-picker').forEach(function(picker){
        picker.addEventListener('input', function(){ setColor(this.getAttribute('data-target'), this.value); updateActivePreset(); });
    });

    document.querySelectorAll('#jsst-theme-modern .jsst-theme-hex').forEach(function(input){
        input.addEventListener('input', function(){
            var value = normalizeHex(this.value);
            var valid = isHex(value);
            showFieldError(this, !valid);
            if(valid){ setColor(this.id, value); updateActivePreset(); } else { updateThemePreviewSafety(); updateActivePreset(); }
        });
        input.addEventListener('blur', function(){
            var value = normalizeHex(this.value);
            this.value = value;
            showFieldError(this, !isHex(value));
            updateThemePreviewSafety();
            updateActivePreset();
        });
    });

    document.querySelectorAll('#jsst-theme-modern .jsst-theme-preset').forEach(function(button){
        button.addEventListener('click', function(){
            try { applyPalette(JSON.parse(this.getAttribute('data-preset') || '{}')); } catch(e) {}
        });
    });

    var resetButton = document.getElementById('jsst-theme-reset');
    if(resetButton){
        resetButton.addEventListener('click', function(){
            var root = getRoot();
            try { applyPalette(JSON.parse(root.getAttribute('data-defaults') || '{}')); } catch(e) {}
        });
    }

    var form = document.getElementById('adminForm');
    if(form){
        form.addEventListener('submit', function(e){
            var valid = true;
            document.querySelectorAll('#jsst-theme-modern .jsst-theme-hex').forEach(function(input){
                var value = normalizeHex(input.value);
                input.value = value;
                var fieldValid = isHex(value);
                showFieldError(input, !fieldValid);
                if(!fieldValid){ valid = false; }
            });
            if(!valid){
                e.preventDefault();
                alert('<?php echo addslashes(Text::_('Please enter valid hex colors. Example: #2563eb')); ?>');
                return;
            }
            if(!updateThemePreviewSafety()){
                e.preventDefault();
                alert('<?php echo addslashes(Text::_('Please fix the readability warnings before saving the theme.')); ?>');
            }
        });
    }
    updateThemePreviewSafety();
    updateActivePreset();
})();
</script>
