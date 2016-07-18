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


JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
?>

<div class="com-weblink <?php echo $this->pageclass_sfx; ?>">
	<div class="category-list">
		<?php if ($this->params->def('show_page_heading', 1)) : ?>
		<h2 class="componentheading"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h2>
		<?php endif; ?>
		<?php if($this->params->get('show_category_title', 1)) : ?>
		<?php echo JHtml::_('content.prepare', $this->category->title, '', 'com_weblinks.category'); ?>
		<?php endif; ?>
		<?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
		<div class="contentdescription clearafter">
			<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
			<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
			<?php endif; ?>
			<?php if ($this->params->get('show_description') && $this->category->description) : ?>
			<?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_weblinks.category'); ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<?php echo $this->loadTemplate('items'); ?>
		<?php if (!empty($this->children[$this->category->id])&& $this->maxLevel != 0) : ?>
		<div class="cat-children">
			<h3><?php echo JText::_('JGLOBAL_SUBCATEGORIES') ; ?></h3>
			<?php echo $this->loadTemplate('children'); ?> </div>
		<?php endif; ?>
	</div>
</div>