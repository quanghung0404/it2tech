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
<div class="app-kunena">
	<div class="row stat-meta">
		<div class="col-sm-3 stat-item">
			<div class="total-posts">
				<div class="center"><?php echo JText::_( 'APP_KUNENA_TOTAL_POSTS' ); ?></div>

				<div class="stat-points"><?php echo $totalPosts;?></div>
			</div>
		</div>
		<div class="col-sm-3 stat-item">
			<div class="total-replies">
				<div class="center"><?php echo JText::_('APP_KUNENA_TOTAL_REPLIES'); ?></div>

				<div class="stat-points"><?php echo $totalReplies;?></div>
			</div>
		</div>

		<div class="col-sm-3 stat-item">
			<div class="total-replies">
				<div class="center"><?php echo JText::_( 'APP_KUNENA_THANKS' ); ?></div>

				<div class="stat-points"><?php echo $thanks;?></div>
			</div>
		</div>
		<div class="col-sm-3 stat-item">
			<div class="total-votes">
				<div class="center"><?php echo JText::_( 'APP_KUNENA_CHART_RECENT_USER_ACTIVITY' ); ?></div>

				<div class="stat-points">
					<span data-kunena-posts-chart><?php echo implode( ',' , $stats ); ?></span>
				</div>
			</div>
		</div>
	</div>

	<?php if( $params->get( 'discuss-recent' , true ) ){ ?>
	<div class="discussions-list">
		<h4><?php echo JText::_( 'APP_KUNENA_RECENT_FORUM_POSTS' ); ?></h4>

		<?php if( $posts ){ ?>
		<ul class="post-items list-unstyled">
			<?php foreach( $posts as $topic ){ ?>
				<?php echo $this->loadTemplate( 'apps:/user/kunena/themes/default/profile/item' , array( 'topic' => $topic , 'kTemplate' => $kTemplate ) ); ?>
			<?php } ?>
		</ul>
		<?php } else { ?>
		<div class="empty">
			<?php echo JText::sprintf( 'APP_KUNENA_EMPTY_POSTS' , $user->getName() ); ?>
		</div>
		<?php } ?>
	</div>
	<?php } ?>
</div>
