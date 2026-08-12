<?php
/**
 * @package Mx Awesome News
 * @version 4.0.2
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2016 mixwebtemplates. All Rights Reserved.
 * @author mixwebtemplates http://www.mixwebtemplates.com
 * 
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

if(!isset($params) || !(count($params) > 0)) return;

$layout_name = $params->get('layout', 'default');

$moduleclass_sfx = $params->get('moduleclass_sfx');
require ModuleHelper::getLayoutPath('mod_mx_awesomenews', $params->get('get_style', 'default'));
