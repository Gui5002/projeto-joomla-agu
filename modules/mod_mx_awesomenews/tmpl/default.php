<?php
/**
* @title			Mx Awesome News
* @version   		4.2.4
* @copyright   		Copyright (C) 2020 mixwebtemplates.com, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   	http://www.mixwebtemplates.com/
* @developers   	mixwebtemplates.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
$doc = Factory::getDocument();
defined('_JEXEC') or die ('Restricted access');
$cacheFolder = Uri::base(true).'/cache/';
$modID = $module->id;
$modPath = Uri::base(true).'/modules/mod_mx_awesomenews/';
$document = Factory::getDocument(); 
$jqueryload = $params->get('jqueryload');
$customone = $params->get('customone');
$csm_back = $params->get('csm_back');
$tx_color = $params->get('tx_color');
$bgm_color = $params->get('bgm_color');
$item_height = $params->get('item_height');
$ol_height = $params->get('ol_height');
$sliderid = $params->get('sliderid');
$get_layout = $params->get('get_layout');
$ga_items = $params->get('ga_items');
$darklayer = $params->get('darklayer');
$categoryFilter = $params->get('categoryFilter');
$image = $params->get('image');
$author = $params->get('author');
$slices_origin = $params->get('slices_origin');
$slices_orientation = $params->get('slices_orientation');
$slices_total = $params->get('slices_total');
$fontload = $params->get('fontload');
$ju_image_width = $params->get('ju_image_width');
$ju_image_width_tabl = $params->get('ju_image_width_tabl');
$ju_image_width_tabp = $params->get('ju_image_width_tabp');
$ju_image_width_mobl = $params->get('ju_image_width_mobl');
$ju_image_width_mobp = $params->get('ju_image_width_mobp');
$itemTitle 			= $params->get('itemTitle', 1);
$itemIntroText		= $params->get('itemIntroText', 1);
$itemImage			= $params->get('itemImage', 1);
$itemAuthor			= $params->get('itemAuthor', 0);
$itemCategory		= $params->get('itemCategory', 0);
$itemDateCreated	= $params->get('itemDateCreated', 0);
$itemHits			= $params->get('itemHits', 0);
$itemReadMore		= $params->get('itemReadMore', 1);
$css_code = $params->get('css_code');
if($fontload) $document->addStyleSheet($modPath.'assets/font-awesome.css');
$document->addStyleSheet($modPath.'assets/css/style3.css');
if($css_code) $document->addStyleDeclaration(' '.$params->get('css_code').' ');
$document->addStyleDeclaration('#coolnews' . $modID . ' .fix-padd { position:relative;margin:'.$params->get('container_fix',6).'px;} '); 
$document->addStyleDeclaration('#coolnews' . $modID . ' .img-slice-wrap {display: inline-block;width:' . $ju_image_width . ';}
@media only screen and (min-width: 960px) and (max-width: 1280px) {#coolnews' . $modID . ' .img-slice-wrap {width:' . $ju_image_width_tabl . ';}}
@media only screen and (min-width: 768px) and (max-width: 959px) {#coolnews' . $modID . ' .img-slice-wrap {width:' . $ju_image_width_tabp . ';}}
@media only screen and ( max-width: 767px ) {#coolnews' . $modID . ' .img-slice-wrap {width:' . $ju_image_width_mobl . ';}}
@media only screen and (max-width: 440px) {#coolnews' . $modID . ' .img-slice-wrap {width:' . $ju_image_width_mobp . ';}}');
?>
<div id="coolnews<?php echo $modID; ?>" class="row">
<?php foreach ($ga_items as $item) : ?>
<div class="img-slice-wrap img-slice-wrap-two">
<div class="fix-padd">
<div class="inner-box">
<div class="image-box">
<?php if (!empty($item->ol_target_url)) : ?><a href="<?php echo $item->ol_target_url; ?>"><?php endif; ?><img src="<?php echo $item->ol_image; ?>"  alt="<?php echo $item->ol_title; ?>" ><?php if (!empty($item->ol_target_url)) : ?></a><?php endif; ?>
</div>
<div class="lower-box">
<div class="post-meta">
<ul class="clearfix">
<?php if (!empty($item->ol_info)) : ?><li> <?php echo $item->ol_info; ?></li><?php endif; ?>
</ul>
</div>
<?php if (!empty($item->ol_title)) : ?><h5><?php if (!empty($item->ol_target_url)) : ?><a href="<?php echo $item->ol_target_url; ?>"><?php endif; ?><?php echo $item->ol_title; ?><?php if (!empty($item->ol_target_url)) : ?></a><?php endif; ?></h5><?php endif; ?>	
<?php if (!empty($item->ol_text)) : ?><div class="text"><?php echo $item->ol_text; ?></div><?php endif; ?>	
<?php if (!empty($item->ol_target_url)) : ?>   <div class="link-box"><a href="<?php echo $item->ol_target_url; ?>"><span class="fa fa-angle-right"></span></a></div><?php endif; ?>	
</div>
</div>
</div>	
</div>						
<?php endforeach; ?>  
</div>
