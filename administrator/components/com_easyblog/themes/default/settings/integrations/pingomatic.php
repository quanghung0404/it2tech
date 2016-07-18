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
            	<b><?php echo JText::_('COM_EASYBLOG_INTEGRATIONS_PINGOMATIC_TITLE');?></b>
            </div>
            <div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_pingomatic', 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_PINGOMATIC_ENABLE'); ?>
			</div>
		</div>
	</div>
</div>
