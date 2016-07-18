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

$fieldsets = $this->form->getFieldsets();
if (isset($fieldsets['core']))   unset($fieldsets['core']);
if (isset($fieldsets['params'])) unset($fieldsets['params']);

foreach ($fieldsets as $group => $fieldset): // Iterate through the form fieldsets
	$fields = $this->form->getFieldset($group);
	if (count($fields)):
?>
<fieldset id="users-profile-custom" class="users-profile-custom-<?php echo $group;?>">
	<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
	<legend><?php echo JText::_($fieldset->label); ?></legend>
	<?php endif;?>
	<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
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
	<?php else : ?>
	<?php foreach ($fields as $field): ?>
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
	<?php endif;?>
</fieldset>
	<?php endif;?>
<?php endforeach;?>
