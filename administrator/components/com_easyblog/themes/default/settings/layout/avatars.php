<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATARS_TITLE');?></b>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_AVATARS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_AVATARS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_AVATARS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_avatar', $this->config->get('layout_avatar')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CATEGORY_AVATARS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CATEGORY_AVATARS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_CATEGORY_AVATARS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_categoryavatar', $this->config->get('layout_categoryavatar')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TEAMBLOG_AVATARS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TEAMBLOG_AVATARS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_TEAMBLOG_AVATARS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_teamavatar', $this->config->get('layout_teamavatar')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_TITLE');?></b>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_LINK_AUTHOR_NAME'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_LINK_AUTHOR_NAME'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_LINK_AUTHOR_NAME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'layout_avatar_link_name', $this->config->get('layout_avatar_link_name')); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php
							$nameFormat = array();
							$avatarIntegration[] = JHTML::_('select.option', 'default', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_DEFAULT'));
							$avatarIntegration[] = JHTML::_('select.option', 'easysocial', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_EASYSOCIAL'));
							$avatarIntegration[] = JHTML::_('select.option', 'jfbconnect', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_JFBCONNECT'));
							$avatarIntegration[] = JHTML::_('select.option', 'communitybuilder', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_CB'));
							$avatarIntegration[] = JHTML::_('select.option', 'gravatar', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_GRAVATAR'));
							$avatarIntegration[] = JHTML::_('select.option', 'jomsocial', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_JOMSOCIAL'));
							$avatarIntegration[] = JHTML::_('select.option', 'kunena', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_KUNENA'));
							$avatarIntegration[] = JHTML::_('select.option', 'k2', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_K2'));
							$avatarIntegration[] = JHTML::_('select.option', 'phpbb', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_PHPBB'));
							$avatarIntegration[] = JHTML::_('select.option', 'mightytouch', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_MIGHTYREGISTRATION'));
							$avatarIntegration[] = JHTML::_('select.option', 'anahita', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_ANAHITA'));
							$avatarIntegration[] = JHTML::_('select.option', 'jomwall', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_JOMWALL'));
							$avatarIntegration[] = JHTML::_('select.option', 'easydiscuss', JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_AVATAR_INTEGRATIONS_EASYDISCUSS'));
							echo JHTML::_('select.genericlist', $avatarIntegration, 'layout_avatarIntegration', 'class="form-control" data-avatar-source', 'value', 'text', $this->config->get('layout_avatarIntegration' , 'default' ) );
						?>
					</div>
				</div>

				<div class="form-group hidden" data-phpbb-path>
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_PHPBB_PATH'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_PHPBB_PATH'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_PHPBB_PATH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<input type="text" name="layout_phpbb_path" class="form-control" value="<?php echo $this->config->get('layout_phpbb_path', '' );?>" />
					</div>
				</div>
			</div>
		</div>
	</div>
</div>