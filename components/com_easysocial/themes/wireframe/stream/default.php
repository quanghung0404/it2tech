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

$isguest	= ( isset( $guest ) ) ? $guest : false;
$iscluster	= ( isset( $iscluster ) ) ? $iscluster : false;
?>
<div class="es-streams <?php echo ( count( $streams) == 0 ) ? ' no-stream' : ''; ?>"
	 data-streams
	 data-streams-wrapper
     data-currentdate="<?php echo FD::date()->toMySQL(); ?>"
     data-excludeids=""
>
	<?php if( $view == 'profile' ){ ?>
		<?php echo $this->render( 'module' , 'es-profile-before-story' ); ?>
	<?php } ?>

	<?php if( $view == 'dashboard' ){ ?>
		<?php echo $this->render( 'module' , 'es-dashboard-before-story' ); ?>
	<?php } ?>

	<?php if (!empty($story)) { echo $story->html(); } ?>

	<?php if( $view == 'dashboard' ){ ?>
		<?php echo $this->render( 'module' , 'es-dashboard-after-story' ); ?>
	<?php } ?>

	<?php if( $view == 'profile' ){ ?>
		<?php echo $this->render( 'module' , 'es-profile-after-story' ); ?>
	<?php } ?>

	<!-- Notifications bar -->
	<div data-stream-notification-bar></div>

	<ul class="es-stream-list fd-reset-list <?php echo ($streams && is_array($streams)) ? '' : ' is-empty';?>"
	    data-stream-list>

	<?php if (($streams && is_array($streams)) || (isset($stickies) && $stickies && is_array($stickies))) { ?>

		<!-- sticky posts -->
		<?php if (isset($stickies) && $stickies && is_array($stickies)) { ?>
			<?php foreach( $stickies as $sticky ){ ?>
				<?php echo $this->loadTemplate('site/stream/default.item', array('stream' => $sticky, 'showTranslations' => $showTranslations)); ?>
			<?php } ?>
		<?php } ?>

		<?php if ($streams && is_array($streams)) { ?>
	    	<?php foreach ($streams as $stream) { ?>
				<?php echo $this->loadTemplate('site/stream/default.item' , array('stream' => $stream, 'showTranslations' => $showTranslations)); ?>

				<?php if ($view == 'profile') { ?>
					<?php echo $this->render('module', 'es-profile-between-streams'); ?>
				<?php } ?>

				<?php if ($view == 'dashboard') { ?>
					<?php echo $this->render('module', 'es-dashboard-between-streams'); ?>
				<?php } ?>
			<?php } ?>
		<?php } ?>


			<?php if ($this->config->get('stream.pagination.style') == 'loadmore') { ?>

				<?php if ($isguest && isset($nextlimit)) { ?>
					<?php if (FD::user()->id != 0) { ?>
						<li class="pagination" data-stream-pagination-guest data-nextlimit="<?php echo $nextlimit; ?>" data-context="<?php echo $this->html('string.escape', $context);?>">
							<?php if ($nextlimit) { ?>
								<a class="btn btn-es-primary btn-stream-updates" href="javascript:void(0);">
									<i class="fa fa-refresh"></i> <?php echo JText::_( 'COM_EASYSOCIAL_STREAM_LOAD_PREVIOUS_STREAM_ITEMS' ); ?>
								</a>
							<?php } ?>
						</li>
					<?php } ?>
				<?php } else { ?>
					<li class="pagination"
						data-stream-pagination<?php echo ($iscluster) ? '-cluster' : '';?>
						data-nextlimit="<?php echo $nextlimit; ?>"
						data-context="<?php echo $this->html('string.escape', $context);?>">
						<?php if ($nextlimit){ ?>
							<a class="btn btn-es-primary btn-stream-updates" href="javascript:void(0);">
								<i class="fa fa-refresh"></i> <?php echo JText::_( 'COM_EASYSOCIAL_STREAM_LOAD_PREVIOUS_STREAM_ITEMS' ); ?>
							</a>
						<?php } ?>
					</li>
				<?php } ?>

			<?php } else {  ?>

				<?php if ($pagination) { ?>
					<li>
						<div class="es-pagination-footer">
							<?php echo $pagination; ?>
						</div>
					</li>
				<?php } ?>

			<?php } ?>

		<?php } else { ?>
			<li class="empty center">
				<i class="fa fa-bullseye"></i>
				<div><?php echo $empty;?></div>
			</li>
		<?php } ?>
	</ul>
</div>
