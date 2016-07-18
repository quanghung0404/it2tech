<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-entry-moderate">
	<h4 class="reset-heading"><?php echo JText::_('COM_EASYBLOG_PREVIEWING_UNPUBLISHED_POST');?></h4>
	<div class="mt-10 mb-15"><?php echo JText::_('COM_EASYBLOG_PREVIEWING_UNPUBLISHED_POST_INFO'); ?></div>

		<div class="clearfix" style="margin-top: 15px;">
        <?php if ($this->acl->get('publish_entry') || $this->acl->get('moderate_entry') || $this->acl->get('moderate_pending')) { ?>
			<a class="btn btn-primary" href="javascript:void(0);" data-blog-preview-publish>
				<i class="fa fa-check-circle"></i>
				&nbsp; <?php echo JText::_('COM_EASYBLOG_POST_PUBLISH_POST');?>
			</a>
        <?php } ?>
		</div>
</div>
