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
<ul class="fd-reset-list discussion-items app-contents-list">
	<?php foreach( $discussions as $discussion ){ ?>
	<li class="discussion-item<?php echo $discussion->answer_id ? ' is-resolved' : '';?><?php echo $discussion->lock ? ' is-locked' : '';?><?php echo !$discussion->last_reply_id ? ' is-unanswered' : '';?>">
		<div class="media">
			<div class="media-object">
				<img src="<?php echo $discussion->author->getAvatar();?>" class="es-avatar" />
			</div>

			<?php if( $params->get( 'display_total_hits' , true ) || $params->get( 'display_total_replies' , true ) ){ ?>
			<div class="media-stats">
				<ul class="list-unstyled">
					<?php if( $params->get( 'display_total_hits' , true ) ){ ?>
					<li class="stats-hits">
						<span><?php echo $discussion->hits;?></span> <?php echo JText::_( 'APP_GROUP_DISCUSSIONS_HITS' ); ?>
					</li>
					<?php } ?>

					<?php if( $params->get( 'display_total_replies' , true ) ){ ?>
					<li class="stats-replies">
						<span><?php echo $discussion->total_replies;?></span> <?php echo JText::_( 'APP_GROUP_DISCUSSIONS_REPLIES' ); ?>
					</li>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>

			<div class="media-body">
				<h3>
					<a href="<?php echo FRoute::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP, 'id' => $app->getAlias(), 'discussionId' => $discussion->id), false);?>">
						<?php echo $discussion->get( 'title' ); ?>
					</a>
					<span class="label label-success label-resolved"><?php echo JText::_( 'APP_GROUP_DISCUSSIONS_RESOLVED' ); ?></span>
					<span class="label label-warning label-locked"><i class="fa fa-lock locked-icon"></i> <?php echo JText::_( 'APP_GROUP_DISCUSSIONS_LOCKED' ); ?></span>
					<span class="label label-danger label-unanswered"><?php echo JText::_( 'APP_GROUP_DISCUSSIONS_UNANSWERED' ); ?></span>
				</h3>

				<ul class="fd-reset-list discussion-meta">
					<?php if( $params->get( 'display_started_by' , true ) ){ ?>
					<li>
						<?php echo JText::sprintf( 'APP_GROUP_DISCUSSIONS_STARTED_BY_ON' , $this->html( 'html.user' , $discussion->author->id ) , FD::date( $discussion->created )->format( JText::_( 'DATE_FORMAT_LC1' ) ) ); ?>
					</li>
					<?php } ?>

					<?php if( $params->get( 'display_last_replied' , true ) && $discussion->lastreply ){ ?>
					<li>
						<?php echo JText::sprintf( 'APP_GROUP_DISCUSSIONS_LAST_REPLIED_BY' , $this->html( 'html.user' , $discussion->lastreply->author->id ) ); ?>
					</li>
					<?php } ?>
				</ul>
			</div>



		</div>
	</li>
	<?php } ?>
</ul>

<div class="empty empty-hero">
	<i class="fa fa-database"></i>
	<?php echo JText::_( 'APP_GROUP_DISCUSSIONS_EMPTY' ); ?>
</div>

<div class="mt-20 pagination-wrapper text-center">
	<?php echo $pagination->getListFooter( 'site' ); ?>
</div>
