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
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_TITLE');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_PUBLISHERS_CODE_DESC'); ?></div>
			</div>

			<div class="panel-body">
				<div class="has-tip">
					<textarea name="social_sharethis_publishers" class="inputbox full-width" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get('social_sharethis_publishers');?></textarea>
					<div class="notice full-width"><?php echo JText::sprintf('COM_EASYBLOG_SETTINGS_SOCIALSHARE_SHARETHIS_PUBLISHERS_INSTRUCTIONS', 'http://easyblog.io/administrators/configuration/sharethis_configuration');?></div>
				</div>
			</div>
		</div>
	</div>
</div>
