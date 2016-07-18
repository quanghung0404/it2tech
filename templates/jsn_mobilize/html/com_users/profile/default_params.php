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

$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();


JLoader::register('JHtmlUsers', JPATH_COMPONENT . '/helpers/html/users.php');
JHtml::register('users.spacer', array('JHtmlUsers','spacer'));
JHtml::register('users.helpsite', array('JHtmlUsers','helpsite'));
JHtml::register('users.templatestyle', array('JHtmlUsers','templatestyle'));
JHtml::register('users.admin_language', array('JHtmlUsers','admin_language'));
JHtml::register('users.language', array('JHtmlUsers','language'));
JHtml::register('users.editor', array('JHtmlUsers','editor'));

?>
<?php $fields = $this->form->getFieldset('params'); ?>
<?php if (count($fields)): ?>
<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
<fieldset id="users-profile-custom">
	<legend><?php echo JText::_('COM_USERS_SETTINGS_FIELDSET_LABEL'); ?></legend>
	<dl class="dl-horizontal">
	<?php foreach ($fields as $field):
		if (!$field->hidden) :?>
		<dt><?php echo $field->title; ?></dt>
		<dd>
			<?php if (JHtml::isRegistered('users.'.$field->id)):?>
				<?php echo JHtml::_('users.'.$field->id, $field->value);?>
			<?php elseif (JHtml::isRegistered('users.'.$field->fieldname)):?>
				<?php echo JHtml::_('users.'.$field->fieldname, $field->value);?>
			<?php elseif (JHtml::isRegistered('users.'.$field->type)):?>
				<?php echo JHtml::_('users.'.$field->type, $field->value);?>
			<?php else:?>
				<?php echo JHtml::_('users.value', $field->value);?>
			<?php endif;?>
		</dd>
		<?php endif;?>
	<?php endforeach;?>
	</dl>
</fieldset>
<?php else : ?>
<fieldset id="users-profile-custom">
	<legend><?php echo JText::_('COM_USERS_SETTINGS_FIELDSET_LABEL'); ?></legend>
	
	<?php foreach ($fields as $field):?>
		<div class="jsn-formRow clearafter">
			<?php if (!$field->hidden) :?>
			<div class="jsn-formRow-lable"><?php echo $field->title; ?></div>
			<div class="jsn-formRow-input">
				<?php if (JHtml::isRegistered('users.'.$field->id)):?>
					<?php echo JHtml::_('users.'.$field->id, $field->value);?>
				<?php elseif (JHtml::isRegistered('users.'.$field->fieldname)):?>
					<?php echo JHtml::_('users.'.$field->fieldname, $field->value);?>
				<?php elseif (JHtml::isRegistered('users.'.$field->type)):?>
					<?php echo JHtml::_('users.'.$field->type, $field->value);?>
				<?php else:?>
					<?php echo JHtml::_('users.value', $field->value);?>
				<?php endif;?>
			</div>
			<?php endif;?>
		</div>
	<?php endforeach;?>
</fieldset>
<?php endif;?>
<?php endif;?>

