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
<div class="eb-comment<?php echo $comment->depth ? ' is-child' . ' depth-' . $comment->depth : ''; ?><?php echo $comment->isLike ? ' is-like' : '';?><?php echo $comment->isModerated() ? ' is-moderated' : '';?>" data-comment-item data-id="<?php echo $comment->id;?>"
	<?php
		$depth = $comment->depth;

		if ($depth == 1) {
			if ($rtl) {
				echo 'style="margin-right:' . $depth*65 . 'px;"';
			} else {
				echo 'style="margin-left:' . $depth*65 . 'px;"';	
			}
		} else if ($depth > 1) {
			if ($rtl) {
				echo 'style="margin-right:' . ($depth*50 + 15) . 'px;"';
			} else {
				echo 'style="margin-left:' . ($depth*50 + 15) . 'px;"';
			}
		}
	?>>
	<a id="comment-<?php echo $comment->id;?>"></a>
	
	<?php if ($comment->isModerated()) { ?>
	<div class="under-moderation">
		<?php echo JText::_('COM_EASYBLOG_COMMENT_POSTED_UNDER_MODERATION');?>
	</div>
	<?php } ?>

	<div class="row-table align-top">
		<div class="col-cell cell-avatar cell-tight">
			<a href="<?php echo $comment->created_by != 0 ? $comment->author->getProfileLink() : 'javascript:void(0);';?>" class="eb-comment-avatar eb-avatar">
				<img src="<?php echo $comment->author->getAvatar(); ?>" width="50" height="50" title="<?php echo $this->html('string.escape', $comment->author->getName());?>" />
			</a>
		</div>
		<div class="col-cell cell-content eb-comment-content">

			<div class="eb-comment-head text-muted row-table">
				<div class="col-cell">
				<?php if ($comment->created_by == 0) { ?>
					<a href="javascript:void(0);" title="<?php echo $this->html('string.escape', $comment->author->getName());?>"><?php echo JText::_('COM_EASYBLOG_GUEST').' - '.$comment->name;?></a>
				<?php } else { ?>
					<a href="<?php echo $comment->author->getProfileLink();?>" title="<?php echo $this->html('string.escape', $comment->author->getName());?>"><?php echo $comment->author->getName();?></a>
				<?php } ?>
					<?php echo JText::_('COM_EASYBLOG_ON');?> <?php echo JHTML::date($comment->created, JText::_('DATE_FORMAT_LC2')); ?>
				</div>

				<?php
				if (
						(EB::isSiteAdmin() || $this->acl->get('manage_comment') || ($this->my->id == $comment->created_by && $this->acl->get('edit_comment') ) && !$this->my->guest) ||
						(EB::isSiteAdmin() || ($this->my->id == $comment->created_by && $this->acl->get('delete_comment') ) && !$this->my->guest)
					) {
				?>
				<div class="col-cell text-right">
					<div class="eb-comment-admin dropdown">
						<b class="dropdown-toggle_" data-bp-toggle="dropdown">
							<i class="fa fa-cog"></i>
							<i class="fa fa-caret-down"></i>
						</b>
						<ul class="dropdown-menu">
							<?php if (EB::isSiteAdmin() || $this->acl->get('manage_comment') || ($this->my->id == $comment->created_by && $this->acl->get('edit_comment') ) && !$this->my->guest) { ?>
							<li>
								<a href="javascript:void(0);" data-comment-edit>
									<i class="fa fa-pencil"></i> <?php echo JText::_('COM_EASYBLOG_COMMENTS_EDIT');?>
								</a>
							</li>
							<?php } ?>

							<?php if (EB::isSiteAdmin() || ($this->my->id == $comment->created_by && $this->acl->get('delete_comment') ) && !$this->my->guest) { ?>
							<li>
								<a href="javascript:void(0);" data-comment-delete>
									<i class="fa fa-trash-o"></i> <?php echo JText::_('COM_EASYBLOG_COMMENTS_DELETE');?>
								</a>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<?php } ?>
			</div>

			<div data-comment-body>

				<div class="eb-comment-body" data-comment-preview>
					<?php if ($comment->title && $this->config->get('comment_show_title')) { ?>
					<div class="eb-comment-title"><b><?php echo $comment->title;?></b></div>
					<?php } ?>

					<?php echo $comment->comment;?>
				</div>

				<?php if (!$comment->isModerated()) { ?>
				<div class="eb-comment-foot text-muted text-small">
					<?php if ($this->config->get('comment_likes')) { ?>
						<span class="eb-comment-heart" data-eb-provide="tooltip" data-original-title="<?php echo $comment->likesAuthor;?>" data-comment-like-tooltip data-placement="bottom">
							<i class="fa fa-heart"></i> <b data-comment-like-counter><?php echo $comment->likesCount;?></b>
						</span>

						<?php if (!$this->my->guest) { ?>
						<span class="eb-comment-likes">
							<a href="javascript:void(0);" class="like-comment" data-comment-like><?php echo JText::_('COM_EASYBLOG_COMMENTS_LIKE');?></a>
							<a href="javascript:void(0);" class="unlike-comment" data-comment-unlike><?php echo JText::_('COM_EASYBLOG_COMMENTS_UNLIKE');?></a>
						</span>
						<?php } ?>
					<?php } ?>

					<?php if (($this->acl->get('allow_comment') && !$this->my->guest) || ($this->acl->get('allow_comment') && $this->my->guest)) { ?>
					<span class="eb-comment-reply">
						<a href="javascript:void(0);" class="hide" data-comment-reply-cancel><?php echo JText::_('COM_EASYBLOG_COMMENTS_CANCEL');?></a>
						<a href="javascript:void(0);" data-comment-reply data-depth="<?php echo $comment->depth + 1;?>"><?php echo JText::_('COM_EASYBLOG_COMMENTS_REPLY');?></a>
					</span>
					<?php } ?>
				</div>
				<?php } ?>
			</div>

			<div class="eb-comment-editor form-group hide mt-15" data-comment-edit-editor>
				<textarea class="textarea" data-comment-edit-textarea><?php echo $comment->raw;?></textarea>
				<div class="hide" data-comment-edit-raw><?php echo $comment->raw;?></div>
				<div class="eb-comment-editor-actions text-right mt-10">
					<a href="javascript:void(0);" class="btn btn-default btn-sm" data-comment-edit-cancel><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></a>
					<a href="javascript:void(0);" class="btn btn-primary btn-sm" data-comment-edit-update><?php echo JText::_('COM_EASYBLOG_UPDATE_COMMENT_BUTTON'); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
