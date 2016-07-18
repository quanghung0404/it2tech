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
<?php if( $this->my->id != $currentUser->id ){ ?>
	<!-- Include cover section -->
	<?php echo $this->loadTemplate( 'site/profile/mini.header' , array( 'user' => $currentUser ) ); ?>
<?php } ?>

<div class="es-container" data-followers>
	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>
	<div class="es-sidebar" data-sidebar>
		<?php echo $this->render( 'module' , 'es-followers-sidebar-top' ); ?>
		<div class="es-widget">
			<div class="es-widget-head">
				<div class="pull-left widget-title"><?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_SIDEBAR_TITLE' );?></div>
			</div>

			<div class="es-widget-body">
				<ul class="widget-list widget-list-with-count fd-nav fd-nav-stacked">
					<li class="follower-filter<?php echo $active == 'followers' ? ' active' : '';?>"
						data-followers-filter
						data-followers-filter-type="followers"
						data-followers-filter-title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FOLLOWERS' );?>"
						data-followers-filter-id="<?php echo $user->id;?>"
						data-followers-filter-url="<?php echo $filterFollowers;?>"
					>
							<a href="javascript:void(0);">
								<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_FOLLOWERS' );?>
								<span class="es-count-no" data-followers-count><?php echo $totalFollowers;?></span>
							</a>
					</li>

					<li class="follower-filter<?php echo $active == 'following' ? ' active' : '';?>"
						data-followers-filter
						data-followers-filter-type="following"
						data-followers-filter-title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_FOLLOWING' );?>"
						data-followers-filter-id="<?php echo $user->id;?>"
						data-followers-filter-url="<?php echo $filterFollowing;?>"
					>
							<a href="javascript:void(0);" class="<?php echo $active == 'following' ? ' active' : '';?>">
								<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_FOLLOWING' );?>
								<span class="es-count-no pull-right" data-following-count><?php echo $totalFollowing;?></span>
							</a>
					</li>

					<?php if ($user->id == $this->my->id) { ?>
					<li class="follower-filter<?php echo $active == 'suggest' ? ' active' : '';?>"
						data-followers-filter
						data-followers-filter-type="suggest"
						data-followers-filter-title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_PEOPLE_TO_FOLLOW' );?>"
						data-followers-filter-id="<?php echo $user->id;?>"
						data-followers-filter-url="<?php echo $filterSuggest;?>"
					>
							<a href="javascript:void(0);" class="<?php echo $active == 'suggest' ? ' active' : '';?>">
								<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_SUGGEST' );?>
								<span class="es-count-no pull-right" data-suggest-count><?php echo $totalSuggest;?></span>
							</a>
					</li>
					<?php } ?>

				</ul>
			</div>
		</div>

		<?php echo $this->render( 'module' , 'es-followers-sidebar-bottom' ); ?>
	</div>


	<div class="es-content">
		<?php echo $this->render( 'module' , 'es-followers-before-contents' ); ?>
		<?php echo $this->loadTemplate( 'site/followers/default.items' , array( 'active' => $active ,  'users' => $users , 'currentUser' => $currentUser ) ); ?>

		<div class="es-pagination-footer" data-followers-pagination>
			<?php echo $pagination->getListFooter( 'site' );?>
		</div>
		<?php echo $this->render( 'module' , 'es-followers-after-contents' ); ?>
	</div>

</div>
