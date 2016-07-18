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
<div class="row mb-10 mt-10">
	<div class="col-md-12">
		<div class="media">
			<div class="media-object pull-right">
				<ul class="list-unstyled">
					<li class="stats-hits">
						<span><?php echo $discussion->hits;?></span> <?php echo JText::_( 'APP_GROUP_DISCUSSIONS_HITS' ); ?>
					</li>
					<li class="stats-replies">
						<span><?php echo $discussion->total_replies;?></span> <?php echo JText::_( 'APP_GROUP_DISCUSSIONS_REPLIES' ); ?>
					</li>
				</ul>
			</div>

			<div class="media-body">
				<div class="discussion-title"><a href="<?php echo $permalink;?>"><?php echo $discussion->title; ?></a></div>
				<div class="discussion-meta fd-small">
					<i class="fa fa-calendar"></i>&nbsp; <?php echo JText::sprintf( 'APP_GROUP_DISCUSSIONS_CONTENT_REPLIED_ON_META' , FD::date( $reply->created )->format(JText::_('DATE_FORMAT_LC1')) ); ?>
				</div>
			</div>
		</div>

		<hr />

		<p class="mb-10 mt-10 blog-description">
			<?php echo strip_tags( $content ); ?>
		</p>

		<div class="mt-5">
			<a href="<?php echo $permalink;?>"><?php echo JText::_( 'APP_GROUP_DISCUSSIONS_VIEW_DISCUSSION' ); ?> &rarr;</a>
		</div>
	</div>
</div>
