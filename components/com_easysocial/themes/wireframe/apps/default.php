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
<div class="es-container" data-apps>
	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="fa fa-grid-view  mr-5"></i> 
		<?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>

	<div class="es-sidebar" data-sidebar>
		<?php echo $this->render( 'module' , 'es-apps-sidebar-top' ); ?>

		<div class="es-widget es-widget-borderless">
			<div class="es-widget-head">
				<?php echo JText::_( 'COM_EASYSOCIAL_APPS' );?>
			</div>

			<div class="es-widget-body">
				<ul class="fd-nav fd-nav-stacked">
					<li class="apps-filter-item<?php echo $filter == 'browse' ? ' active' :'';?>"
						data-apps-filter
						data-apps-filter-type="browse"
						data-apps-filter-group="user"
						data-apps-filter-url="">
						<a href="<?php echo FRoute::apps();?>" title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_BROWSE_APPS' , true );?>" data-apps-filter-link>
							<?php echo JText::_( 'COM_EASYSOCIAL_APPS_BROWSE_APPS' );?>
						</a>
					</li>
					<li class="apps-filter-item<?php echo $filter == 'mine' ? ' active' :'';?>"
						data-apps-filter
						data-apps-filter-type="mine"
						data-apps-filter-group="user">
						<a href="<?php echo FRoute::apps( array( 'filter' => 'mine' ) );?>" title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_YOUR_APPS' , true );?>" data-apps-filter-link>
							<?php echo JText::_( 'COM_EASYSOCIAL_APPS_YOUR_APPS' );?>
						</a>
					</li>
				</ul>
			</div>
		</div>

		<?php echo $this->render( 'module' , 'es-apps-sidebar-bottom' ); ?>
	</div>

	<div class="es-content">
		<?php echo $this->render( 'module' , 'es-apps-before-contents' ); ?>

		<div class="es-filterbar row-table mb-15">
			<div class="col-cell cell-mid">
				<?php if( $filter == 'mine' ){ ?>
				<b data-page-apps-title><?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_YOUR_APPS' ); ?></b>
				<?php } else { ?>
				<b data-page-apps-title><?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_BROWSE_APPS' ); ?></b>
				<?php }?>
			</div>

			<?php if( $this->template->get( 'apps_sorting' ) ){ ?>
				<div class="col-cell">
					<div class="btn-group btn-group-view-apps pull-right" data-apps-sorting style="<?php echo $filter == 'mine' ? 'display: none;' : '';?>">
						<a class="btn btn-es btn-sm alphabetical<?php echo $sort == 'alphabetical' ? ' active' : '';?>"
							data-apps-sort
							data-apps-sort-type="alphabetical"
							data-apps-sort-group="user"
							data-apps-sort-url="<?php echo FRoute::apps( array( 'sort' => 'alphabetical' ) );?>"
							data-es-provide="tooltip"
							data-placement="bottom"
							data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_APPS_SORT_ALPHABETICALLY' , true );?>"
						>
							<i class="fa fa-sort-alpha-asc"></i>
							<?php echo JText::_('COM_EASYSOCIAL_APPS_ALPHABETICALLY');?>
						</a>
						<a class="btn btn-es btn-sm recent<?php echo $sort == 'recent' ? ' active' : '';?>"
							data-apps-sort
							data-apps-sort-type="recent"
							data-apps-sort-group="user"
							data-apps-sort-url="<?php echo FRoute::apps( array( 'sort' => 'recent' ) );?>"
							data-es-provide="tooltip"
							data-placement="bottom"
							data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_APPS_SORT_RECENT_ADDED' , true );?>"
						>
							<i class="fa fa-upload "></i>
							<?php echo JText::_('COM_EASYSOCIAL_APPS_RECENT_ADDED');?>
						</a>
						<a class="btn btn-es btn-sm trending<?php echo $sort == 'trending' ? ' active' : '';?>"
							data-apps-sort
							data-apps-sort-type="trending"
							data-apps-sort-group="user"
							data-apps-sort-url="<?php echo FRoute::apps( array( 'sort' => 'trending' ) );?>"
							data-es-provide="tooltip"
							data-placement="bottom"
							data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_APPS_SORT_TRENDING_APPS' , true );?>"
						>
							<i class="fa fa-fire "></i>
							<?php echo JText::_('COM_EASYSOCIAL_APPS_TRENDING_APPS');?>
						</a>
					</div>
				</div>
			<?php } ?>
		</div>

		<div class="es-apps-listing<?php if( !$apps ){ echo " is-empty"; } ?>" data-apps-listing>
			<?php if( $apps ){ ?>
				<?php foreach( $apps as $app ){ ?>
					<?php echo $this->loadTemplate( 'site/apps/default.item' , array( 'app' => $app ) ); ?>
				<?php } ?>
			<?php } else { ?>
				<?php echo $this->loadTemplate( 'site/apps/default.empty' ); ?>
			<?php } ?>
		</div>

		<?php echo $this->render( 'module' , 'es-apps-after-contents' ); ?>
	</div>
</div>
