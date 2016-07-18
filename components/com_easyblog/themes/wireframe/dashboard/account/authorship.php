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
<div class="eb-box">
	<div class="eb-box-head">
		<i class="fa fa-google-plus"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLE_SETTINGS_TITLE');?>
	</div>
	<div class="eb-box-body">
		<p class="eb-box-lead">
			<?php echo JText::_('COM_EASYBLOG_DASHBOARD_GOOGLE_SETTINGS_DESC'); ?>
		</p>
		<div class="form-horizontal">
			<div class="form-group">
				<label  class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_GOOGLE_PROFILE_URL'); ?></label>
				<div class="col-md-5">
					<input type="text" class="form-control" name="google_profile_url" id="google_profile_url" value="<?php echo $params->get('google_profile_url');?>" />
				</div>
			</div>
			<div class="form-group">
				<label  class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_GOOGLE_PROFILE_URL_DISPLAY'); ?></label>
				<div class="col-md-5">
					<?php echo $this->html('grid.boolean', 'show_google_profile_url', $params->get('show_google_profile_url')); ?>
				</div>
			</div>
		</div>
	</div>
</div>
