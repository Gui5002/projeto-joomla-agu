<?php
/**
 * @package     Bluecoder.TagFilter
 *
 * @copyright   Copyright © 2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Module\SimpleTagFilter\Site\Field;

\defined('_JEXEC') or die();

use Joomla\CMS\Form\Field\RadioField;

class StfradioField extends RadioField
{
    use SetupTrait;
    use RenderFieldTrait;
}