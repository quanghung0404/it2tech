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


JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();

?>
<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
<div class="remind <?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
	<?php endif; ?>

	<form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=remind.remind'); ?>" method="post" class="form-validate form-horizontal">

		<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
		<p><?php echo JText::_($fieldset->label); ?></p>

		<fieldset>
			<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field): ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $field->label; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</fieldset>
		<?php endforeach; ?>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary validate"><?php echo JText::_('JSUBMIT'); ?></button>
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
<?php else : ?>
<div class="com-user remind <?php echo $this->pageclass_sfx?>">
	<div class="default-remind">
		<?php if ($this->params->get('show_page_heading')) : ?>
		<h2 class="componentheading">
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h2>
		<?php endif; ?>

		<form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=remind.remind'); ?>" method="post" class="josForm form-validate">
			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
			<p><?php echo JText::_($fieldset->label); ?></p>	
			
			<div class="jsn-formRow clearafter">
			<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field): ?>
				<div class="jsn-formRow-lable"><?php echo $field->label; ?></div>
				<div class="jsn-formRow-input"><?php echo $field->input; ?></div>
			<?php endforeach; ?>
			</div>
				
			<?php endforeach; ?>
			<div>
				<button type="submit"><?php echo JText::_('JSUBMIT'); ?></button>
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
</div>
<?php endif; ?>