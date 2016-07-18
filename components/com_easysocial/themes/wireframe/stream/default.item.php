<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<li class="type-<?php echo $stream->favicon; ?> streamItem<?php echo $stream->display == SOCIAL_STREAM_DISPLAY_FULL ? ' es-stream-full' : ' es-stream-mini';?>
	es-stream-wrapper
	stream-context-<?php echo $stream->context; ?>
	stream-actor-<?php echo $stream->actor->id; ?>
	<?php echo $stream->isModerated() ? ' is-moderated' : '';?>
	<?php echo $stream->sticky ? ' is-sticky' : '';?>
	<?php echo $stream->bookmarked ? ' is-bookmarked' : '';?>"
	data-id="<?php echo $stream->uid;?>"
	data-ishidden="0"
	data-context="<?php echo $stream->context; ?>"
	data-actor="<?php echo $stream->actor->id; ?>"
	data-streamItem
>

	<div class="es-stream" data-stream-item >

		<?php if ($stream->lastaction) { ?>
			<div class="es-stream-header"><?php echo $stream->lastaction; ?></div>
		<?php } ?>

		<div class="es-moderated-note">
			<span class="alert alert-info"><?php echo JText::_('COM_EASYSOCIAL_POST_IS_PENDING_MODERATION');?></span>
		</div>
		

		<?php if (!$this->my->guest &&
    		(
     			($stream->isModerated() && $stream->isCluster() && $stream->cluster_type == SOCIAL_TYPE_GROUP && ($this->my->isSiteAdmin() || $stream->getCluster()->isAdmin() || $stream->getCluster()->isOwner())) ||
     			$this->access->allowed('stream.hide') || $this->access->allowed('reports.submit') || $stream->editable || ($this->access->allowed('stream.delete', false) || $this->my->isSiteAdmin()))) { ?>
		<div class="es-stream-control btn-group pull-right">
			<a class="btn-control" href="javascript:void(0);" data-bs-toggle="dropdown">
				<i class="fa fa-caret-down"></i>
			</a>
			<ul class="dropdown-menu fd-reset-list">

				<?php if ($this->config->get('stream.bookmarks.enabled') && !$stream->isModerated()) { ?>
				<li class="add-bookmark" data-stream-bookmark-add>
					<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_STREAM_BOOKMARK');?></a>
				</li>
				<li class="remove-bookmark" data-stream-bookmark-remove>
					<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_STREAM_REMOVE_BOOKMARK');?></a>
				</li>
				<?php } ?>

				<?php if ($stream->canSticky()) { ?>
				<li class="add-sticky" data-stream-sticky-add>
					<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_STREAM_PIN_ITEM');?></a>
				</li>
				<li class="remove-sticky" data-stream-sticky-remove>
					<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_STREAM_UNPIN_ITEM');?></a>
				</li>
				<?php } ?>

				<?php if (($this->config->get('stream.bookmarks.enabled') && !$stream->isModerated()) || $stream->canSticky()) { ?>
				<li class="divider"></li>
				<?php } ?>

				<?php if ($stream->editablepoll) { ?>
				<li data-stream-polls-edit>
					<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_STREAM_EDIT_POLLS');?></a>
				</li>
				<?php } ?>

				<?php if ($stream->editable) { ?>
				<li data-stream-edit>
					<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_STREAM_EDIT');?></a>
				</li>
				<?php } ?>

				<?php if ($this->access->allowed('stream.hide') && !$stream->isModerated()) { ?>
				<li data-stream-hide>
					<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_STREAM_HIDE');?></a>
				</li>

				<?php if( $this->my->id != $stream->actor->id ) { ?>
					<li data-stream-hide-actor>
						<a href="javascript:void(0);"><?php echo JText::_( 'COM_EASYSOCIAL_STREAM_HIDE_ACTOR' );?></a>
					</li>
				<?php } ?>

					<?php if( $stream->context != 'story' ){ ?>
					<li data-stream-hide-app>
						<a href="javascript:void(0);"><?php echo JText::_( 'COM_EASYSOCIAL_STREAM_HIDE_APP' );?></a>
					</li>
					<?php } ?>
				<?php } ?>

				<?php if( $this->config->get('reports.enabled') && $this->access->allowed( 'reports.submit' ) && !$stream->actor->isViewer() ){ ?>
				<li>
					<?php echo FD::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_STREAM , $stream->uid , JText::sprintf( 'COM_EASYSOCIAL_STREAM_REPORT_ITEM_TITLE' , $stream->actor->getName() ) , JText::_( 'COM_EASYSOCIAL_STREAM_REPORT_ITEM' ) , '' , JText::_( 'COM_EASYSOCIAL_STREAM_REPORT_ITEM_DESC' ) , FRoute::stream( array( 'id' => $stream->uid , 'layout' => 'item' , 'external' => true ) ) ); ?>
				</li>
				<?php } ?>

				<?php if($stream->deleteable) { ?>
				<li data-stream-delete>
					<a href="javascript:void(0);"><?php echo JText::_( 'COM_EASYSOCIAL_STREAM_DELETE' );?></a>
				</li>
				<?php } ?>

				<?php if ($stream->isModerated() && $stream->isCluster() && $stream->cluster_type == SOCIAL_TYPE_GROUP && ($this->my->isSiteAdmin() || $stream->getCluster()->isAdmin() || $stream->getCluster()->isOwner())) { ?>
				<li class="divider"></li>
				<li data-stream-publish>
					<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_STREAM_PUBLISH_POST');?></a>
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php } ?>

		<div class="es-stream-meta">
			<div class="media">
				<div class="media-object pull-left">
					<?php if ($this->config->get('stream.pin.enabled')) { ?>
					<div class="es-stream-sticky-label" data-es-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_STREAM_YOU_HAVE_PINNED_THIS_STREAM');?>"><i class="fa fa-star"></i></div>
					<?php } ?>
					<div class="es-avatar es-avatar-sm es-stream-avatar" data-comments-item-avatar="">
						<?php if ($stream->actor->id) { ?>
							<?php echo $this->loadTemplate('site/avatar/default', array('user' => $stream->actor)); ?>						
						<?php } else { ?>
							<img src="<?php echo $stream->actor->getAvatar();?>" alt="<?php echo $this->html('string.escape', $stream->actor->getName());?>" />
						<?php } ?>
					</div>
				</div>

				<div class="media-body">
					<?php if ($this->config->get('stream.bookmarks.enabled')) { ?>
					<span class="bookmark pull-left mr-5" data-es-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_BOOKMARK_YOU_HAVE_BOOKMARKED_THIS_STREAM');?>">
						<i class="fa fa-star" pull-right></i>
					</span>
					<?php } ?>

					<div class="es-stream-title">
						<?php echo $stream->title; ?>

						<?php if ($this->config->get('stream.timestamp.enabled')) { ?>
							<?php if ($stream->display == SOCIAL_STREAM_DISPLAY_MINI) { ?>
							<time class="ml-5">
								&mdash; <?php echo $stream->friendlyDate; ?>
							</time>
							<?php } ?>
						<?php } ?>
					</div>

					<?php if ($stream->display == SOCIAL_STREAM_DISPLAY_FULL) { ?>
					<div class="es-stream-meta-footer">
						<span class="text-muted">
							<?php echo $stream->label;?>
							<b>&middot;</b>
						</span>

						<?php if ($this->config->get('stream.timestamp.enabled')) { ?>
						<time>
							<a href="<?php echo FRoute::stream( array( 'id' => $stream->uid , 'layout' => 'item' ) ); ?>"><?php echo $stream->friendlyDate; ?></a>
						</time>
						<?php } ?>

						<?php if ($stream->edited != '0000-00-00 00:00:00') { ?>
						<span class="es-edit-text" data-es-provide="tooltip" data-original-title="<?php echo JText::sprintf('COM_EASYSOCIAL_STREAM_LAST_EDITED_ON', FD::date($stream->edited)->format(JText::_('DATE_FORMAT_LC2'), true));?>">
							&middot; <?php echo JText::_('COM_EASYSOCIAL_STREAM_EDITED');?>
						</span>
						<?php } ?>

						<span class="es-editing-text"><?php echo JText::_('COM_EASYSOCIAL_STREAM_EDITING');?></span>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<?php if ($stream->display == SOCIAL_STREAM_DISPLAY_FULL) { ?>
			<div class="es-stream-content" data-stream-content>
				<?php echo $stream->content; ?>
				<?php echo $stream->meta; ?>
			</div>

			<?php if (JString::trim($stream->content) && $showTranslations && $this->config->get('stream.translations.bing') && $this->config->get('stream.translations.bingid') && $this->config->get('stream.translations.bingsecret')) { ?>
			<div class="es-stream-translations">
				<span class="translate-loader" data-stream-translate-loader><i class="fd-loading"></i></span>
				<a href="javascript:void(0);" class="translate-link" data-stream-translate><?php echo JText::_('COM_EASYSOCIAL_STREAM_SEE_TRANSLATION');?></a>
			</div>
			<?php } ?>

			<?php if ($stream->editable || $stream->editablepoll) { ?>
			<div class="es-stream-editor" data-stream-editor></div>
			<?php } ?>

			<?php if (isset($stream->preview) && !empty($stream->preview)) { ?>
			<div class="es-stream-preview"><?php echo $stream->preview; ?></div>
			<?php } ?>
		<?php } ?>

		<?php echo $stream->actions; ?>
	</div>

	<div class="es-stream-published es-stream-published-notice">
		<?php echo JText::_('COM_EASYSOCIAL_STREAM_ITEM_PUBLISHED'); ?>
	</div>

</li>
