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
defined('_JEXEC') or die;

// Load template framework


JHtml::_('behavior.tooltip');
$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();

?>
<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
<div class="profile <?php echo $this->pageclass_sfx?>">
<?php if (JFactory::getUser()->id == $this->data->id) : ?>
<ul class="btn-toolbar pull-right">
	<li class="btn-group">
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->data->id);?>">
			<i class="icon-user"></i> <?php echo JText::_('COM_USERS_Edit_Profile'); ?></a>
	</li>
</ul>
<?php endif; ?>
<?php if ($this->params->get('show_page_heading')) : ?>
<div class="page-header">
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
</div>
<?php endif; ?>

<?php echo $this->loadTemplate('core'); ?>

<?php echo $this->loadTemplate('params'); ?>

<?php echo $this->loadTemplate('custom'); ?>

</div>
<?php else : ?>

<div class="com-user profile<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h2 class="componentheading">
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h2>
	<?php endif; ?>

	<?php echo $this->loadTemplate('core'); ?>

	<?php echo $this->loadTemplate('params'); ?>

	<?php echo $this->loadTemplate('custom'); ?>

	<?php if (JFactory::getUser()->id == $this->data->id) : ?>
	<a href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->data->id);?>">
		<?php echo JText::_('COM_USERS_Edit_Profile'); ?></a>
	<?php endif; ?>
</div>
<?php endif; ?>
