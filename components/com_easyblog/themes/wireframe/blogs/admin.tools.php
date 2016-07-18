<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if (EB::isSiteAdmin() || ($post->isMine() && !$post->hasRevisionWaitingForApproval()) || ($post->isMine() && $this->acl->get('publish_entry')) || ($post->isMine() && $this->acl->get('delete_entry')) || $this->acl->get('feature_entry') || $this->acl->get('moderate_entry')) { ?>
<div class="eb-post-admin dropdown_ pull-right" data-blog-tools>
	<a id="post-<?php echo $post->id;?>" data-bp-toggle="dropdown" href="javascript:void(0);">
		<i class="fa fa-pencil"></i>
	</a>
	<ul class="dropdown-menu reset-list" role="menu" aria-labelledby="post-<?php echo $post->id;?>">

		<?php if ($this->acl->get('feature_entry') && !$post->isPasswordProtected()) { ?>
		<li class="featured_add<?php echo $post->isFeatured ? ' hide' : '';?>">
			<a href="javascript:void(0);" data-entry-feature data-return="<?php echo base64_encode($return);?>"><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_THIS'); ?></a>
		</li>
		<li class="featured_remove<?php echo $post->isFeatured ? '' : ' hide';?>">
			<a href="javascript:void(0);" data-entry-unfeature data-return="<?php echo base64_encode($return);?>"><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_REMOVE'); ?></a>
		</li>
		<?php } ?>

		<?php if ($this->acl->get('moderate_entry') || EB::isSiteAdmin()) { ?>
			<?php if ($post->isArchived()) { ?>
			<li>
				<a href="javascript:void(0);" data-entry-unarchive data-id="<?php echo $post->id;?>" data-return="<?php echo base64_encode($return);?>">
					<?php echo JText::_('COM_EASYBLOG_UNARCHIVE_POST');?>
				</a>
			</li>
			<?php } else { ?>
			<li>
				<a href="javascript:void(0);" data-entry-archive data-id="<?php echo $post->id;?>" data-return="<?php echo base64_encode($return);?>">
					<?php echo JText::_('COM_EASYBLOG_ARCHIVE_THIS');?>
				</a>
			</li>
			<?php } ?>
		<?php } ?>

		<?php if (EB::isSiteAdmin() || $this->acl->get('moderate_entry') || ($post->isMine() && !$post->hasRevisionWaitingForApproval())) { ?>
		<li>
			<a href="<?php echo EB::_('index.php?option=com_easyblog&view=composer&tmpl=component&uid=' . $post->id); ?>" target="_blank" data-eb-composer><?php echo JText::_('COM_EASYBLOG_ADMIN_EDIT_ENTRY'); ?></a>
		</li>
		<?php } ?>

		<?php if (EB::isSiteAdmin() || ($post->isMine() && $this->acl->get('delete_entry') ) || $this->acl->get('moderate_entry') ) { ?>
		<li class="delete">
			<a href="javascript:void(0);" data-entry-delete data-id="<?php echo $post->id;?>" data-return="<?php echo base64_encode(EBR::_('index.php?option=com_easyblog', false));?>"><?php echo Jtext::_('COM_EASYBLOG_ADMIN_DELETE_ENTRY'); ?></a>
		</li>
		<?php } ?>

		<?php if ($post->isPublished() && (EB::isSiteAdmin() || ($post->isMine() && $this->acl->get('publish_entry')) || $this->acl->get('moderate_entry'))) { ?>
		<li class="unpublish">
			<a href="javascript:void(0);" data-entry-unpublish data-id="<?php echo $post->id;?>" data-return="<?php echo base64_encode($return);?>"><?php echo Jtext::_('COM_EASYBLOG_ADMIN_UNPUBLISH_ENTRY'); ?></a>
		</li>
		<?php } ?>

		<?php if (!$post->isPublished() && (EB::isSiteAdmin() || ($this->acl->get('publish_entry')) || $this->acl->get('moderate_entry'))) { ?>
		<li class="publish">
			<a href="javascript:void(0);" data-entry-publish data-id="<?php echo $post->id;?>" data-return="<?php echo base64_encode($return);?>"><?php echo Jtext::_('COM_EASYBLOG_ADMIN_PUBLISH_ENTRY'); ?></a>
		</li>
		<?php } ?>
	</ul>
</div>
<?php } ?>
