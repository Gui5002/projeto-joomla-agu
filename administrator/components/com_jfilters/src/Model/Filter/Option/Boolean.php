<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2021-2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Component\Jfilters\Administrator\Model\Filter\Option;

\defined('_JEXEC') or die();

use Bluecoder\Component\Jfilters\Administrator\Model\Filter\Option;
use Joomla\CMS\Language\Text;

class Boolean extends Option
{
    /**
     * The available labels
     *
     * @var string[]
     * @since 1.18.0
     */
    protected array $labels = [
        '0' => 'JNO',
        '1' => 'JYES'
    ];

    public function getLabel(): string
    {
        if ($this->labelResolved === false) {
            $this->setLabel(!empty($this->labels[$this->getValue()]) ? Text::_($this->labels[$this->getValue()]) : $this->label);
            $this->labelResolved = true;
        }

        return parent::getLabel();
    }
}