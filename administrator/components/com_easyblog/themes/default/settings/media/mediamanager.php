<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_MEDIA_GENERAL_TITLE');?></b>
			</div>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'main_media_manager', 'COM_EASYBLOG_SETTINGS_MEDIA_ENABLE_IMAGE_MANAGER', 'COM_EASYBLOG_SETTINGS_MEDIA_ENABLE_IMAGE_MANAGER_DESC'); ?>

	            <?php echo $this->html('settings.toggle', 'main_media_manager_place_shared_media', 'COM_EASYBLOG_SETTINGS_MEDIA_ENABLE_SHARED_MEDIA', 'COM_EASYBLOG_SETTINGS_ENABLE_SHARED_MEDIA_DESC'); ?>

	            <?php echo $this->html('settings.smalltext', 'main_media_manager_items_per_page', 'COM_EASYBLOG_SETTINGS_MEDIA_ITEMS_PER_PAGE', 'COM_EASYBLOG_SETTINGS_MEDIA_ITEMS_PER_PAGE_DESC'); ?>
            </div>
		</div>
	</div>

	<div class="col-lg-6">&nbsp;</div>
</div>
