<?php
/**
 * @package     Bluecoder.TagFilter
 *
 * @copyright   Copyright © 2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Module\SimpleTagFilter\Site\Field;

use Joomla\CMS\Form\Field\TagField;

defined('_JEXEC') or die();

class StftagField extends TagField
{
    use SetupTrait;
    use RenderFieldTrait;
}