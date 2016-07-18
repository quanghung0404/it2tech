<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<li class="reply-item<?php echo $answer && $answer->id == $reply->id ? ' is-answer-item' : '';?>" data-reply-item data-id="<?php echo $reply->id;?>">
	<a id="reply-<?php echo $reply->id;?>"></a>
	<?php if ($group->isAdmin() || $this->my->isSiteAdmin() || $reply->created_by == $this->my->id || $question->created_by == $this->my->id) { ?>
	<div class="pull-right btn-group">
		<a class="dropdown-toggle_ loginLink btn btn-dropdown" data-bs-toggle="dropdown" href="javascript:void(0);">
			<i class="icon-es-dropdown"></i>
		</a>

		<ul class="dropdown-menu dropdown-menu-user messageDropDown">
			<?php if (($question->created_by == $this->my->id || $this->my->isSiteAdmin() || $group->isAdmin()) && (!$answer || $reply->id != $answer->id)) { ?>
			<li>
				<a href="javascript:void(0);" data-reply-accept-answer><?php echo JText::_( 'APP_GROUP_DISCUSSIONS_ACCEPT_ANSWER' ); ?></a>
			</li>
			<li class="divider"></li>
			<?php } ?>

			<?php if ($reply->created_by == $this->my->id || $this->my->isSiteAdmin() || $group->isAdmin()) { ?>
			<li>
				<a href="javascript:void(0);" data-reply-edit><?php echo JText::_('APP_GROUP_DISCUSSIONS_EDIT_REPLY' ); ?></a>
			</li>
			<li>
				<a href="javascript:void(0);" data-reply-delete><?php echo JText::_('APP_GROUP_DISCUSSIONS_DELETE_REPLY' ); ?></a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } ?>

	<div class="media">
		<div class="media-object pull-left">
			<img src="<?php echo $reply->author->getAvatar();?>" title="<?php echo $this->html( 'string.escape' , $reply->author->getName() );?>" class="es-avatar" data-popbox="module://easysocial/profile/popbox" data-user-id="<?php echo $reply->author->id; ?>" />
		</div>

		<div class="media-body">



			<div class="reply-author">
				<a href="<?php echo $reply->author->getPermalink();?>" data-popbox="module://easysocial/profile/popbox" data-user-id="<?php echo $reply->author->id; ?>"><?php echo $reply->author->getName();?></a>
				<span class="label label-success label-answer-item"><i class="fa fa-support "></i> <?php echo JText::_( 'APP_GROUP_DISCUSSIONS_ACCEPTED_ANSWER' ); ?></span>
			</div>

			<div class="reply-content" data-reply-display-content>
				<?php echo $reply->getContent(); ?>
			</div>

			<form data-reply-form class="reply-form reply-content-edit">
				<div class="alert alert-dismissable alert-error alert-empty" style="display:none;">
					<button type="button" class="close" data-bs-dismiss="alert">×</button>
					<?php echo JText::_( 'APP_GROUP_DISCUSSIONS_EMPTY_REPLY_ERROR' ); ?>
				</div>

				<?php echo FD::bbcode()->editor( 'reply_content' , $reply->content , array( 'files' => $files , 'uid' => $group->id , 'type' => SOCIAL_TYPE_GROUP ) , array( 'data-reply-content' => '' ) ); ?>

				<div class="form-actions">
					<button type="button" class="pull-left btn btn-es-danger btn-sm" data-reply-edit-cancel><?php echo JText::_( 'COM_EASYSOCIAL_CANCEL_BUTTON' ); ?></a>
					<button type="button" class="pull-right btn btn-es-primary btn-sm" data-reply-edit-update><?php echo JText::_( 'COM_EASYSOCIAL_UPDATE_BUTTON' ); ?></a>
				</div>
			</form>

			<div class="reply-footer">
				<i class="fa fa-clock-o "></i> <?php echo FD::date( $reply->created )->toLapsed();?>
			</div>
		</div>
	</div>
</li>
