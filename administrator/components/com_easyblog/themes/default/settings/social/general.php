<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
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
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SOCIAL_BUTTONS');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SOCIAL_BUTTONS_INFO'); ?></div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIAL_BUTTONS_BUTTON_SIZE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIAL_BUTTONS_BUTTON_SIZE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIAL_BUTTONS_BUTTON_SIZE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<select name="social_button_size" class="form-control">
							<option value="large"<?php echo $this->config->get('social_button_size') == 'large' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SOCIAL_BUTTONS_SIZE_LARGE');?></option>
							<option value="small"<?php echo $this->config->get('social_button_size') == 'small' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_SOCIAL_BUTTONS_SIZE_SMALL');?></option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
	</div>
</div>
