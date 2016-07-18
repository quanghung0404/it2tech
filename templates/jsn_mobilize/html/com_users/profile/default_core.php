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

jimport('joomla.user.helper');

$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();

?>

<fieldset id="users-profile-core">
	<legend>
		<?php echo JText::_('COM_USERS_PROFILE_CORE_LEGEND'); ?>
	</legend>
	<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
	<dl class="dl-horizontal">
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?>
		</dt>
		<dd>
			<?php echo $this->data->name; ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?>
		</dt>
		<dd>
			<?php echo htmlspecialchars($this->data->username); ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>
		</dt>
		<dd>
			<?php echo JHtml::_('date', $this->data->registerDate); ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>
		</dt>

		<?php if ($this->data->lastvisitDate != '0000-00-00 00:00:00'){?>
			<dd>
				<?php echo JHtml::_('date', $this->data->lastvisitDate); ?>
			</dd>
		<?php }
		else {?>
			<dd>
				<?php echo JText::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
			</dd>
		<?php } ?>

	</dl>
	<?php else : ?>
	<div class="jsn-formRow clearafter">
		<div class="jsn-formRow-lable">
			<?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?>
		</div>
		<div class="jsn-formRow-input">
			<?php echo $this->data->name; ?>
		</div>
	</div>
	<div class="jsn-formRow clearafter">
		<div class="jsn-formRow-lable">
			<?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?>
		</div>
		<div class="jsn-formRow-input">
			<?php echo $this->data->username; ?>
		</div>
	</div>
	<div class="jsn-formRow clearafter">
		<div class="jsn-formRow-lable">
			<?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>
		</div>
		<div class="jsn-formRow-input">
			<?php echo JHTML::_('date',$this->data->registerDate); ?>
		</div>
	</div>
	<div class="jsn-formRow clearafter">
		<div class="jsn-formRow-lable">
			<?php echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?>
		</div>

		<?php if ($this->data->lastvisitDate != '0000-00-00 00:00:00'){?>
			<div class="jsn-formRow-input">
				<?php echo JHTML::_('date',$this->data->lastvisitDate); ?>
			</div>
		<?php }
		else {?>
			<div class="jsn-formRow-input">
				<?php echo JText::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
			</div>
		<?php } ?>
	</div>
	<?php endif; ?>	
</fieldset>
