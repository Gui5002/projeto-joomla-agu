<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.jssupportticketicon
 *
 * @copyright   Copyright (C) 2015 - 2026 Joom Sky. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JoomSky\Plugin\System\JSSupportTicketIcon\Extension;

use Joomla\CMS\Event\Application\AfterRenderEvent;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\SubscriberInterface;

\defined('_JEXEC') or die;

/**
 * Shows a floating JS Support Ticket tab on the site frontend.
 */
final class JSSupportTicketIcon extends CMSPlugin implements SubscriberInterface
{
    /**
     * Automatically load plugin language files.
     *
     * @var bool
     */
    protected $autoloadLanguage = true;

    /**
     * Map Joomla events to plugin handlers.
     *
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterRender' => 'injectSupportTicketIcon',
        ];
    }

    /**
     * Inject the floating support icon into frontend HTML output.
     *
     * @param   AfterRenderEvent  $event  Joomla application render event.
     *
     * @return  void
     */
    public function injectSupportTicketIcon(AfterRenderEvent $event): void
    {
        $app = $this->getApplication() ?: $event->getApplication();

        if (!method_exists($app, 'isClient') || !$app->isClient('site')) {
            return;
        }

        if (!method_exists($app, 'getBody') || !method_exists($app, 'setBody')) {
            return;
        }

        if (method_exists($app, 'getInput')) {
            $format = strtolower((string) $app->getInput()->getCmd('format', 'html'));

            if ($format !== 'html') {
                return;
            }
        }

        $body = (string) $app->getBody();

        if ($body === '') {
            return;
        }

        if (stripos($body, 'id="jsjobs_screentag"') !== false || stripos($body, "id='jsjobs_screentag'") !== false) {
            return;
        }

        $bodyClosePosition = stripos($body, '</body>');

        if ($bodyClosePosition === false) {
            return;
        }

        $html = $this->renderIconHtml((int) $this->params->get('position', 1));
        $body = substr_replace($body, $html . "\n", $bodyClosePosition, 0);

        $app->setBody($body);
    }

    /**
     * Build the floating support icon HTML.
     *
     * @param   int  $position  Configured tab position.
     *
     * @return  string
     */
    private function renderIconHtml(int $position): string
    {
        $position = in_array($position, [1, 2, 3, 4, 5, 6], true) ? $position : 1;

        $isRight = in_array($position, [2, 4, 6], true);

        $positionStyles = [
            1 => 'top:30px;left:0;right:auto;bottom:auto;border-radius:0 8px 8px 0;',
            2 => 'top:30px;right:0;left:auto;bottom:auto;border-radius:8px 0 0 8px;',
            3 => 'top:48%;left:0;right:auto;bottom:auto;border-radius:0 8px 8px 0;',
            4 => 'top:48%;right:0;left:auto;bottom:auto;border-radius:8px 0 0 8px;',
            5 => 'bottom:30px;left:0;right:auto;top:auto;border-radius:0 8px 8px 0;',
            6 => 'bottom:30px;right:0;left:auto;top:auto;border-radius:8px 0 0 8px;',
        ];

        $classes = [
            'jssupportticket-screentag',
            $isRight ? 'jssupportticket-screentag-right' : 'jssupportticket-screentag-left',
            'jssupportticket-screentag-position-' . $position,
        ];

        $url   = Route::_('index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel');
        $icon  = Uri::root(true) . '/components/com_jssupportticket/include/images/support-icon.png';
        $label = trim((string) $this->params->get('label', ''));

        if ($label === '') {
            $label = Text::_('PLG_SYSTEM_JSSUPPORTTICKETICON_SUPPORT');
        }

        $background = $this->normaliseHexColour((string) $this->params->get('bgcolor', ''), '#2563EB');
        $foreground = $this->contrastColour($background);

        // Move the hover shade away from the text colour so the label stays readable.
        $backgroundHover = $this->shiftBrightness($background, $foreground === '#ffffff' ? 0.12 : -0.12);

        $safeUrl     = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        $safeIcon    = htmlspecialchars($icon, ENT_QUOTES, 'UTF-8');
        $safeLabel   = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
        $safeClasses = htmlspecialchars(implode(' ', $classes), ENT_QUOTES, 'UTF-8');
        $safeStyle   = htmlspecialchars($positionStyles[$position], ENT_QUOTES, 'UTF-8');

        $iconHtml = '<img src="' . $safeIcon . '" alt="" loading="lazy" aria-hidden="true">';
        $textHtml = '<span>' . $safeLabel . '</span>';
        $content  = $isRight ? $iconHtml . $textHtml : $textHtml . $iconHtml;

        return <<<HTML
<style id="jsjobs_screentag_style" type="text/css">
#jsjobs_screentag,
#jsjobs_screentag * {
    box-sizing: border-box;
}
#jsjobs_screentag {
    position: fixed;
    display: block;
    z-index: 2147483000;
    margin: 0;
    padding: 0;
    background: {$background};
    box-shadow: 0 8px 24px rgba(0,0,0,.22);
    line-height: 1;
    opacity: 1;
}
#jsjobs_screentag a {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 42px;
    padding: 8px 12px;
    color: {$foreground};
    text-decoration: none;
    white-space: nowrap;
    font: 600 14px/1.2 system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}
#jsjobs_screentag a:hover,
#jsjobs_screentag a:focus {
    color: {$foreground};
    text-decoration: none;
    background: {$backgroundHover};
}
#jsjobs_screentag img {
    display: inline-block;
    width: 24px;
    height: 24px;
    max-width: 24px;
    max-height: 24px;
    margin: 0;
    padding: 0;
    object-fit: contain;
    vertical-align: middle;
}
#jsjobs_screentag span {
    display: inline-block;
    margin: 0;
    padding: 0;
}
@media (max-width: 480px) {
    #jsjobs_screentag a {
        min-height: 38px;
        padding: 7px 10px;
        font-size: 13px;
    }
    #jsjobs_screentag img {
        width: 22px;
        height: 22px;
        max-width: 22px;
        max-height: 22px;
    }
}
</style>
<div id="jsjobs_screentag" class="{$safeClasses}" style="{$safeStyle}"><a href="{$safeUrl}" aria-label="{$safeLabel}">{$content}</a></div>
HTML;
    }

    /**
     * Validate a hex colour, falling back to a default when it is unusable.
     *
     * @param   string  $colour    Raw parameter value.
     * @param   string  $fallback  Colour to use when $colour is not a hex value.
     *
     * @return  string  Normalised six digit hex colour.
     */
    private function normaliseHexColour(string $colour, string $fallback): string
    {
        if (preg_match('/^#?([0-9a-f]{3}|[0-9a-f]{6})$/i', trim($colour), $match) !== 1) {
            return $fallback;
        }

        $hex = strtolower($match[1]);

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return '#' . $hex;
    }

    /**
     * Split a normalised hex colour into its RGB channels.
     *
     * @param   string  $colour  Six digit hex colour.
     *
     * @return  array<int, int>
     */
    private function toRgb(string $colour): array
    {
        return [
            (int) hexdec(substr($colour, 1, 2)),
            (int) hexdec(substr($colour, 3, 2)),
            (int) hexdec(substr($colour, 5, 2)),
        ];
    }

    /**
     * Pick a readable text colour for the given background.
     *
     * @param   string  $background  Six digit hex colour.
     *
     * @return  string
     */
    private function contrastColour(string $background): string
    {
        [$r, $g, $b] = $this->toRgb($background);

        // Perceived brightness, ITU-R BT.601 weighting.
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return $brightness >= 150 ? '#111827' : '#ffffff';
    }

    /**
     * Move a colour towards white or black.
     *
     * @param   string  $colour  Six digit hex colour.
     * @param   float   $amount  Positive lightens, negative darkens (-1 to 1).
     *
     * @return  string
     */
    private function shiftBrightness(string $colour, float $amount): string
    {
        $target = $amount < 0 ? 0 : 255;
        $amount = min(1.0, abs($amount));

        $channels = array_map(
            static function (int $channel) use ($target, $amount): int {
                return max(0, min(255, (int) round($channel + (($target - $channel) * $amount))));
            },
            $this->toRgb($colour)
        );

        return sprintf('#%02x%02x%02x', $channels[0], $channels[1], $channels[2]);
    }
}
