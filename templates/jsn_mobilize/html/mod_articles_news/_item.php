<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// no direct access
defined('_JEXEC') or die;

// Load template framework


$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();

?>
<div class="jsn-article">
<?php if ($params->get('item_title')) : ?>
	<<?php echo $params->get('item_heading'); ?> class="contentheading <?php if (JSNMobilizeTemplateHelper::isJoomla3()) {echo 'newsflash-title';}?>">
		<?php if ($params->get('link_titles') && $item->link != '') : ?>
			<a href="<?php echo $item->link;?>"><?php echo $item->title;?></a>
		<?php else : ?>
			<?php echo $item->title; ?>
		<?php endif; ?>
	</<?php echo $params->get('item_heading'); ?>>
<?php endif; ?>
<?php if (!$params->get('intro_only')) : echo $item->afterDisplayTitle; endif; ?>
<?php echo $item->beforeDisplayContent; ?>
<?php echo $item->introtext; ?>
<?php if (isset($item->link) && $item->readmore && $params->get('readmore')) : echo '<a class="readon" href="'.$item->link.'">'.$item->linkText.'</a>'; endif; ?>
</div>