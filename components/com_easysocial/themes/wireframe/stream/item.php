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

$streamDateDisplay 	= $this->template->get( 'stream_datestyle' );
$streamDate 		= $stream->lapsed;

if( $streamDateDisplay == 'datetime' )
{
	$streamDate = $stream->created->toFormat( $this->template->get( 'stream_dateformat_format', 'Y-m-d H:i' ) );
}
?>
<?php if ($stream->cluster_id && $stream->cluster_type) { ?>
	<?php if ($stream->cluster_type == 'group') { ?>
		<?php echo $this->html('html.miniheader', FD::group($stream->cluster_id)); ?>
	<?php } else { ?>
		<?php echo $this->html('html.miniheader', FD::event($stream->cluster_id)); ?>
	<?php } ?>
<?php } else { ?>
	<?php if( $this->my->id != $stream->actor->id ){ ?>
		<?php echo $this->includeTemplate( 'site/profile/mini.header' , array( 'showCover' => false , 'user' => $stream->actor ) ); ?>
	<?php } ?>
<?php } ?>

<div class="es-container">
	<div class="es-streams" data-streams>
		<ul data-stream-list class="es-stream-list fd-reset-list">
			<li class="type-<?php echo $stream->favicon; ?>
				streamItem<?php echo $stream->display == SOCIAL_STREAM_DISPLAY_FULL ? ' es-stream-full' : ' es-stream-mini';?>
				stream-context-<?php echo $stream->context; ?>
				<?php echo $stream->bookmarked ? ' is-bookmarked' : '';?>"
				data-id="<?php echo $stream->uid;?>"
				data-ishidden="0"
				data-streamItem
				data-context="<?php echo $stream->context; ?>"
			>
				<div class="es-stream" data-stream-item >

					<?php if( FD::user()->id != 0 && ( $this->access->allowed( 'stream.hide' ) || $this->access->allowed( 'reports.submit' ) || ( $this->access->allowed( 'stream.delete', false ) || FD::user()->isSiteAdmin() ) ) ){ ?>
					<div class="es-stream-control btn-group pull-right">
						<a class="btn-control" href="javascript:void(0);" data-bs-toggle="dropdown">
							<i class="fa fa-caret-down"></i>
						</a>
						<ul class="dropdown-menu fd-reset-list">

							<?php if ($this->config->get('stream.bookmarks.enabled')) { ?>
							<li class="add-bookmark" data-stream-bookmark-add>
								<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_STREAM_BOOKMARK');?></a>
							</li>
							<li class="remove-bookmark" data-stream-bookmark-remove>
								<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_STREAM_REMOVE_BOOKMARK');?></a>
							</li>
							<li class="divider">
							</li>
							<?php } ?>

							<?php if ($stream->editablepoll) { ?>
							<li data-stream-polls-edit>
								<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_STREAM_EDIT_POLLS');?></a>
							</li>
							<?php } ?>


							<?php if( $this->access->allowed( 'stream.hide' ) ){ ?>
							<li data-stream-hide>
								<a href="javascript:void(0);"><?php echo JText::_( 'COM_EASYSOCIAL_STREAM_HIDE' );?></a>
							</li>
							<li data-stream-hide-app>
								<a href="javascript:void(0);"><?php echo JText::sprintf( 'COM_EASYSOCIAL_STREAM_HIDE_APP' , $stream->context );?></a>
							</li>
							<?php } ?>

							<?php if( $this->access->allowed( 'reports.submit' ) ){ ?>
							<li>
								<?php echo FD::reports()->getForm( 'com_easysocial' , SOCIAL_TYPE_STREAM , $stream->uid , JText::sprintf( 'COM_EASYSOCIAL_STREAM_REPORT_ITEM_TITLE' , $stream->actor->getName() ) , JText::_( 'COM_EASYSOCIAL_STREAM_REPORT_ITEM' ) , '' , JText::_( 'COM_EASYSOCIAL_STREAM_REPORT_ITEM_DESC' ) , FRoute::stream( array( 'id' => $stream->uid , 'external' => true ) ) ); ?>
							</li>
							<?php } ?>

							<?php if( ( $this->access->allowed( 'stream.delete', false ) && $this->my->id == $stream->actor->id ) || FD::user()->isSiteAdmin() ){ ?>
							<li data-stream-delete>
								<a href="javascript:void(0);"><?php echo JText::_( 'COM_EASYSOCIAL_STREAM_DELETE' );?></a>
							</li>
							<?php } ?>

						</ul>
					</div>
					<?php } ?>

					<?php if( $stream->display == SOCIAL_STREAM_DISPLAY_FULL ) { ?>
						<div class="es-stream-meta">
							<div class="media">
								<div class="media-object pull-left">
									<div class="es-avatar es-avatar-sm es-stream-avatar" data-comments-item-avatar="">
										<?php if ($stream->actor->id) { ?>
										<a href="<?php echo $stream->actor->getPermalink();?>"><img src="<?php echo $stream->actor->getAvatar();?>" alt="<?php echo $this->html( 'string.escape' , $stream->actor->getName() );?>" /></a>
										<?php } else { ?>
											<img src="<?php echo $stream->actor->getAvatar();?>" alt="<?php echo $this->html( 'string.escape' , $stream->actor->getName() );?>" />
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
									</div>
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
									</div>
								</div>
							</div>
						</div>

		 				<?php if( $stream->display == SOCIAL_STREAM_DISPLAY_FULL ) { ?>

							<div class="es-stream-content" data-stream-content>
								<?php echo $stream->content; ?>
								<?php echo $stream->meta; ?>
							</div>

							<?php if ($showTranslations && $this->config->get('stream.translations.bing') && $this->config->get('stream.translations.bingid') && $this->config->get('stream.translations.bingsecret')) { ?>
							<div class="es-stream-translations">
								<span class="translate-loader" data-stream-translate-loader><i class="fd-loading"></i></span>
								<a href="javascript:void(0);" class="translate-link" data-stream-translate><?php echo JText::_('COM_EASYSOCIAL_STREAM_SEE_TRANSLATION');?></a>
							</div>
							<?php } ?>
							
							<?php if ($stream->editable || $stream->editablepoll) { ?>
							<div class="es-stream-editor" data-stream-editor></div>
							<?php } ?>

							<?php if( isset( $stream->preview ) && !empty( $stream->preview ) ){ ?>
							<div class="es-stream-preview">
								<?php echo $stream->preview; ?>
							</div>
							<?php } ?>

						<?php } ?>

					<?php } else { ?>
						<div class="es-stream-content">
							<?php echo $stream->title; ?>
						</div><!-- stream-content -->
					<?php } ?>

					<?php echo $actions; ?>

				</div>
			</li>

		</ul>
	</div>
</div>
