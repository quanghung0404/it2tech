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

$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();

?>
<div class="categories-list<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
<div class="page-header">
<?php endif; ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
</div>
<?php endif; ?>
<?php endif; ?>
	<?php if ($this->params->get('show_base_description')) : ?>
	<?php 	//If there is a description in the menu parameters use that; ?>
		<?php if($this->params->get('categories_description')) : ?>
		<div class="category-desc base-desc">
			<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
			<?php echo  JHtml::_('content.prepare', $this->params->get('categories_description'), '', 'com_contact.categories'); ?>
			<?php else: ?>
			<?php echo  JHtml::_('content.prepare',$this->params->get('categories_description')); ?>
			<?php endif; ?>
			</div>
		<?php  else: ?>
			<?php //Otherwise get one from the database if it exists. ?>
			<?php  if ($this->parent->description) : ?>
				<div class="category-desc base-desc">
				<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
				<?php  echo JHtml::_('content.prepare', $this->parent->description, '', 'com_contact.categories'); ?>
				<?php else : ?>
				<?php  echo JHtml::_('content.prepare', $this->parent->description); ?>
				<?php endif; ?>
				</div>
			<?php  endif; ?>
		<?php  endif; ?>
	<?php endif; ?>
<?php
echo $this->loadTemplate('items');
?>
</div>