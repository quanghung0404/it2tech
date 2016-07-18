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
<div data-albums class="es-container es-albums es-media-group <?php echo (empty($albums)) ? '' : 'has-albums'; ?> is-user">

	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>

	<div class="es-sidebar" data-sidebar>

		<?php echo $this->render('module', 'es-albums-all-sidebar-top'); ?>

		<div class="es-widget">

			<?php if( $lib->canCreateAlbums() ){ ?>
			<div class="es-widget-head center">
				<div class="btn btn-es-primary btn-create">
					<a href="<?php echo $lib->getCreateLink();?>"><i class="fa fa-plus  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_CREATE_ALBUM' );?></a>
				</div>
			</div>
			<?php } ?>

			<div class="es-widget-body">
				<ul class="widget-list fd-nav fd-nav-stacked" data-es-albums-filters>
					<li class="filter-item active" data-es-albums-filters-type="all">
						<a href="<?php echo FRoute::albums(array('layout' => 'all'));?>" title="<?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_FILTER_ALL_ALBUMS' , true );?>"><?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_FILTER_ALL_ALBUMS' );?></a>
					</li>
				</ul>
			</div>
		</div>

		<?php echo $this->render('module', 'es-albums-all-sidebar-bottom'); ?>
	</div>

	<div class="es-content">
		<div class="row">
			<div class="col-md-12 ml-20">
				<h5 class="pull-left"><?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_PHOTO_ALBUMS' ); ?></h5>

				<div class="btn-group btn-group-view-apps pull-right mr-20" data-apps-sorting>

					<a class="btn btn-es recent<?php echo $sorting == 'created' ? ' active' : '';?>"
						data-albums-sort
						data-albums-sort-type="recent"
						data-es-provide="tooltip"
						data-placement="bottom"
						data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_SORT_CREATION_DATE' , true );?>"
						href="<?php echo FRoute::albums( array( 'layout' => 'all' , 'sort' => 'created' ) );?>"
						title="<?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_SORT_CREATION_DATE' , true );?>"
					>
						<i class="fa fa-upload "></i>
					</a>
					<a class="btn btn-es alphabetical<?php echo $sorting == 'alphabetical' ? ' active' : '';?>"
						data-albums-sort
						data-albums-sort-type="alphabetical"
						data-es-provide="tooltip"
						data-placement="bottom"
						data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_SORT_ALPHABETICALLY' , true );?>"
						href="<?php echo FRoute::albums( array( 'layout' => 'all' , 'sort' => 'alphabetical' ) );?>"
						title="<?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_SORT_ALPHABETICALLY' , true );?>"
					>
						<i class="fa fa-bars "></i>
					</a>
					<a class="btn btn-es trending<?php echo $sorting == 'popular' ? ' active' : '';?>"
						data-albums-sort
						data-albums-sort-type="trending"
						data-es-provide="tooltip"
						data-placement="bottom"
						data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_SORT_POPULAR' , true );?>"
						href="<?php echo FRoute::albums( array( 'layout' => 'all' , 'sort' => 'popular' ) );?>"
						title="<?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_SORT_POPULAR' , true );?>"
					>
						<i class="fa fa-fire "></i>
					</a>
				</div>
			</div>
		</div>

		<hr />

		<div class="es-content-wrap ml-20" data-albums-content>
			<?php echo $this->render( 'module' , 'es-albums-before-contents' ); ?>
			<?php echo $this->includeTemplate( 'site/albums/all.items' ); ?>
			<?php echo $this->render( 'module' , 'es-albums-after-contents' ); ?>
		</div>
	</div>



</div>



