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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="es-container es-groups" data-es-groups>
	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>

	<div class="es-sidebar" data-sidebar>
		<?php echo $this->render('module', 'es-groups-sidebar-top', 'site/dashboard/sidebar.module.wrapper'); ?>

		<div class="es-widget">
			<div class="es-widget-create mr-10">
				<?php if ($this->my->isSiteAdmin() || $this->access->allowed( 'groups.create' ) && !$this->access->intervalExceeded('groups.limit', $this->my->id) ){ ?>
				<a class="btn btn-es-primary btn-create btn-block" href="<?php echo FRoute::groups( array( 'layout' => 'create' ) );?>">
					<?php echo JText::_('COM_EASYSOCIAL_GROUPS_START_YOUR_GROUP' );?>
				</a>
				<?php } ?>
			</div>

			<hr class="es-hr mt-15 mb-10">

			<div class="es-widget-body">
				<ul class="widget-list widget-list-with-count fd-nav fd-nav-stacked" data-es-groups-filters >
					<li class="filter-item<?php echo $filter == 'all' && !$activeCategory ? ' active' : '';?>" data-es-groups-filters-type="all">
						<a href="<?php echo FRoute::groups();?>" title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_GROUPS' , true );?>"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_FILTER_ALL_GROUPS' );?></a>

						<span class="es-count-no pull-right" data-total-groups><?php echo $totalGroups;?></span>
					</li>
					<li class="filter-item<?php echo $filter == 'featured' && !$activeCategory ? ' active' : '';?>" data-es-groups-filters-type="featured">
						<a href="<?php echo FRoute::groups( array( 'filter' => 'featured' ) );?>" title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_FEATURED' , true );?>"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_FILTER_FEATURED_GROUPS' );?></a>

						<span class="es-count-no pull-right" data-total-featured><?php echo $totalFeaturedGroups;?></span>
					</li>
					<?php if (FD::user()->id != 0) { ?>
					<li class="filter-item<?php echo $filter == 'mine' && !$activeCategory ? ' active' : '';?>" data-es-groups-filters-type="mine">
						<a href="<?php echo FRoute::groups( array( 'filter' => 'mine' ) );?>" title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_MY_GROUPS' , true );?>"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_FILTER_MY_GROUPS' );?></a>

						<span class="es-count-no pull-right" data-total-created><?php echo $totalCreatedGroups;?></span>
					</li>
					<li class="filter-item<?php echo $filter == 'invited' && !$activeCategory ? ' active' : '';?>" data-es-groups-filters-type="invited">
						<a href="<?php echo FRoute::groups( array( 'filter' => 'invited' ) );?>" title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_GROUPS_FILTER_INVITED' , true );?>" ><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_INVITED' );?></a>
						<span class="es-count-no pull-right" data-total-invites><?php echo $totalInvites;?></span>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>

		<div class="es-widget">
			<div class="es-widget-head">
				<div class="pull-left widget-title"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORIES_SIDEBAR_TITLE' );?></div>
			</div>

			<div class="es-widget-body">
				<?php if ($categories) { ?>
				<ul class="widget-list widget-list-with-count fd-nav fd-nav-stacked" data-es-groups-categories>
					<?php foreach( $categories as $category ){ ?>
					<li data-es-groups-category data-es-groups-category-id="<?php echo $category->id;?>" data-es-groups-sort="latest"
						class="<?php echo $activeCategory && $activeCategory->id == $category->id ? 'active' : '';?>"
					>
						<a href="<?php echo FRoute::groups( array('filter' => 'all' ,'ordering' => 'latest', 'categoryid' => $category->getAlias() ) );?>" title="<?php echo $this->html( 'string.escape' , $category->get( 'title' ) );?>"><?php echo $category->get( 'title' );?></a>
						<span data-total-groups="<?php echo $category->getTotalGroups(array('types' => $this->my->isSiteAdmin() ? 'all' : 'user'));?>" class="es-count-no pull-right"><?php echo $category->getTotalGroups(array('types' => $this->my->isSiteAdmin() ? 'all' : 'user'));?></span>
					</li>
					<?php } ?>
				</ul>
				<?php } else { ?>
				<div class="empty text-center">
					<i class="icon-es-empty-group mb-10"></i>
					<div class="small"><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_NO_CATEGORY_CREATED_YET' );?></div>
				</div>
				<?php } ?>
			</div>
		</div>

		<?php echo $this->render('module', 'es-groups-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
	</div>


	<div class="es-content">
        <i class="loading-indicator fd-small"></i>
		
		<?php echo $this->render('module' , 'es-groups-before-contents'); ?>

		<div class="es-group-listing es-responsive" data-es-groups-content>

			<?php echo $this->includeTemplate('site/groups/default.items'); ?>
		</div>

		<?php echo $this->render('module', 'es-groups-after-contents'); ?>
	</div>
</div>
