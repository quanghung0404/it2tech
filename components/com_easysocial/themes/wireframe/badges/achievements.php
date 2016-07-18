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
<?php if( $user->id != $this->my->id ){ ?>
<div class="mb-15">
	<?php echo $this->loadTemplate( 'site/profile/mini.header' , array( 'user' => $user ) ); ?>
</div>
<?php  } ?>

<div class="es-container" data-achievements>
	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>
	<div class="es-sidebar" data-sidebar>

		<?php echo $this->render( 'module' , 'es-achievements-sidebar-top' ); ?>

		<div class="es-widget">
			<div class="es-widget-head">
				<div class="pull-left widget-title"><?php echo JText::_( 'COM_EASYSOCIAL_BADGES_ACHIEVEMENTS' );?></div>
			</div>

			<div class="es-widget-body">
				<ul class="widget-list widget-list-with-count fd-nav fd-nav-stacked">
					<li class="filter-item active"
						data-friends-filter
						data-filter="all"
						data-title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FRIENDS' );?>"
						data-userid="<?php echo $user->id; ?>"
						data-url=""
					>
						<a href="javascript:void(0);">
							<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_ALL_ACHIEVEMENTS' );?>
						</a>
						<span class="es-count-no pull-right" data-total-achievements><?php echo $totalBadges;?></span>
					</li>
				</ul>
			</div>
		</div>

		<?php echo $this->render( 'module' , 'es-achievements-sidebar-bottom' ); ?>

	</div>


	<div class="es-content">

		<?php echo $this->render( 'module' , 'es-achievements-before-contents' ); ?>

		<div class="es-achievements" data-achievements-content>
			<ul class="achievements-list">
				<?php if( $badges ){ ?>
					<?php foreach( $badges as $badge ){ ?>

						<?php echo $this->render( 'module' , 'es-achievements-between-achievement' ); ?>

						<?php echo $this->loadTemplate( 'site/badges/achievements.item' , array( 'badge' => $badge ) ); ?>
					<?php } ?>
				<?php } else { ?>
				<li class="empty text-center mt-20" data-achievements-empty>
					<i class="icon-es-empty-follow mb-10"></i>
					<div>
						<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_NO_ACHIEVEMENTS_YET' ); ?>
					</div>
				</li>
				<?php } ?>
			</ul>
		</div>

		<?php echo $this->render( 'module' , 'es-achievements-after-contents' ); ?>

	</div>

</div>
