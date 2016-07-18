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

// return;

$isGuest = ( FD::user()->id == 0 ) ? true : false;
?>
<div class="es-action-wrap fd-cf">
	<ul class="fd-reset-list es-action-feedback">
		<?php if( !$isGuest
					&& $this->config->get( 'stream.likes.enabled' )
					&& isset( $likes )
					&& $likes
					&& $likes instanceof SocialLikes ){ ?>
		<li class="action-title-likes streamAction"
			data-key="likes"
			data-streamItem-actions
		>
			<a data-stream-action-likes href="javascript:void(0);" class="fd-small"><?php echo $likes->button(); ?></a>
		</li>
		<?php } ?>

		<?php // If is guest, then we don't add the action link, but we still show the content if settings enabled
				if( !$isGuest
						&& isset( $comments )
						&& $comments
						&& $this->config->get( 'stream.comments.enabled' )
						&& $this->access->allowed( 'comments.add' )
						&& $commentLink
				) { ?>
		<li class="action-title-comments streamAction"
			data-key="comments"
			data-streamItem-actions
		>
			<a data-stream-action-comments href="javascript:void(0);" class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_STREAM_COMMENT' ); ?></a>
		</li>
		<?php } ?>

		<?php if( !$isGuest && isset( $repost ) && $repost && $this->config->get( 'stream.repost.enabled' ) ){ ?>
		<li class="action-title-repost streamAction"
			data-key="repost"
			data-streamItem-actions
		>
			<a data-stream-action-repost href="javascript:void(0);" class="fd-small"><?php echo $repost->getButton(); ?></a>
		</li>
		<?php } ?>

		<?php if (!$isGuest && isset($sharing) && $sharing && $this->config->get('stream.sharing.enabled')) { ?>
		<li class="action-title-social streamAction"
			data-key="social"
			data-streamItem-actions>
			<span class="fd-small">
				<?php echo $sharing->getHTML(); ?>
			</span>
		</li>
		<?php } ?>

		<?php if ($privacy) { ?>
		<li>
			<?php echo $privacy; ?>
		</li>
		<?php } ?>
	</ul>

	<?php if( !$isGuest && ( ( isset( $likes ) && $likes && $likes instanceof SocialLikes ) || ( isset( $repost ) && $repost && $repost instanceof SocialRepost ) ) ){ ?>
	<div data-stream-counter class="es-stream-counter<?php echo ( !$likes || ( $likes && !$likes->data ) ) && ( !$repost || ( $repost && !$repost->getCount() ) ) ? ' hide' : '';?>">

		<?php if( isset( $likes ) && $likes ){ ?>
		<div class="es-stream-actions action-contents-likes"
			data-streamItem-contents
			data-streamItem-contents-likes
			data-action-contents-likes
		><?php echo $likes->toHTML(); ?></div>
		<?php } ?>

		<?php if( !$isGuest && isset( $repost ) && $repost ){ ?>
		<div class="es-stream-actions action-contents-repost pull-right"
			data-streamItem-contents
			data-streamItem-contents-repost
			data-action-contents-repost
		><?php echo $repost->getHTML(); ?></div>
		<?php } ?>
	</div>
	<?php } ?>

	<?php // If is guest, then we don't add the action link, but we still show the content if settings enabled
			if(
				!empty( $comments ) &&
				(
					(!$isGuest
						&& $this->config->get( 'stream.comments.enabled' )
						&& $this->access->allowed( 'comments.read' )
					) ||
					($isGuest
						&& $this->config->get( 'stream.comments.guestview' )
					)
				)
			) { ?>
	<div class="es-stream-actions action-contents-comments"
		data-streamItem-contents
		data-streamItem-contents-comments
		data-action-contents-comments
	><?php echo $comments->getHtml( array( 'hideEmpty' => true, 'hideForm' => $isGuest ) ); ?></div>
	<?php } ?>


</div>
