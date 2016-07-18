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

if (!isset($showCategoryHeader) || (isset($showCategoryHeader) && $showCategoryHeader)) {
$showCategoryHeader = true;
}

?>
<?php echo $this->loadTemplate('site/groups/default.items.category', array('activeCategory' => isset($activeCategory) ? $activeCategory : false, 'showCategoryHeader' => $showCategoryHeader)); ?>

<?php if (!isset($showSorting) || (isset($showSorting) && $showSorting)) { ?>
<div class="mb-15 row-table">
    <div class="col-cell">&nbsp;</div>
    <div class="col-cell cell-tight" style="vertical-align: middle">
        <div class="btn-group btn-group-sort row-table" data-es-groups-sort>
            <a href="<?php echo FRoute::groups(array('filter' => $filter , 'ordering' => 'latest', 'categoryid' => $activeCategory ? $activeCategory->getAlias() : '' ));?>" 
                data-es-provide="tooltip" 
                data-placement="bottom" 
                data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_SORT_BY_CREATED_DATE', true);?>" 
                data-groups-sort
                data-type="latest"
                data-filter="<?php echo $filter; ?>" 
                <?php if ($activeCategory) { ?>
                data-categoryid="<?php echo $activeCategory->id;?>"
                <?php } ?>
                title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_SORT_BY_CREATED_DATE', true);?>"
                class="btn btn-es col-cell trending <?php echo $ordering == 'latest' ? ' active' : '';?>" >
                <i class="fa fa-calendar"></i>
                <?php echo JText::_('COM_EASYSOCIAL_GROUPS_SORTING_CREATED_DATE');?>
            </a>

            <a href="<?php echo FRoute::groups(array('filter' => $filter , 'ordering' => 'name', 'categoryid' => $activeCategory ? $activeCategory->getAlias() : ''  ));?>" 
                data-es-provide="tooltip" 
                data-placement="bottom" 
                data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_SORT_BY_NAME', true);?>" 
                data-groups-sort
                data-type="name"
                data-filter="<?php echo $filter; ?>" 
                <?php if ($activeCategory) { ?>
                data-categoryid="<?php echo $activeCategory->id;?>"
                <?php } ?>
                title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_SORT_BY_NAME', true);?>"
                class="btn btn-es col-cell recent <?php echo $ordering == 'name' ? ' active' : '';?>" >
                <i class="fa fa-sort-alpha-asc"></i>
                <?php echo JText::_('COM_EASYSOCIAL_GROUPS_SORTING_NAME');?>
            </a>

            <a href="<?php echo FRoute::groups(array('filter' => $filter , 'ordering' => 'popular', 'categoryid' => $activeCategory ? $activeCategory->getAlias() : ''  ));?>"
                data-es-provide="tooltip" 
                data-placement="bottom" 
                data-original-title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_SORT_BY_POPULAR', true);?>" 
                data-groups-sort
                data-type="popular"
                data-filter="<?php echo $filter; ?>"
                <?php if ($activeCategory) { ?>
                data-categoryid="<?php echo $activeCategory->id;?>"
                <?php } ?>
                title="<?php echo JText::_('COM_EASYSOCIAL_GROUPS_SORT_BY_POPULAR', true);?>"
                class="btn btn-es col-cell recent <?php echo $ordering == 'popular' ? ' active' : '';?>" >
                <i class="fa fa-flash"></i>
                <?php echo JText::_('COM_EASYSOCIAL_GROUPS_SORTING_POPULAR');?>
            </a>
        </div>
    </div>
</div>
<?php } ?>

<div data-es-groups-list>
    <div class="es-featured-section<?php echo !$featuredGroups ? ' is-empty' : '';?>">
    	<?php if ($featuredGroups) { ?>
    	<ul class="list-media fd-reset-list list-media-group">
    		<?php foreach ($featuredGroups as $group) { ?>
    		<li class="is-featured <?php echo $group->isMember() ? 'is-member' : '';?> <?php echo $group->isInvited() && !$group->isMember() ? 'is-invited' : '';?> <?php echo !$group->isMember() && !$group->isInvited() ? 'is-guest' : '';?>"
    			data-groups-featured-item
    			data-id="<?php echo $group->id;?>"
    			data-type="<?php echo $group->isOpen() ? 'open' : 'closed';?>"
    		>
    			<?php echo $this->loadTemplate( 'site/groups/default.items.group' , array( 'group' => $group , 'featured' => true ) ); ?>
    		</li>
    		<?php } ?>
    	</ul>
    	<?php } ?>

    	<?php if ($filter == 'featured') { ?>
    	<div class="empty empty-hero">
    		<i class="fa fa-users"></i>
    		<div><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_NO_FEATURED_GROUPS_YET' );?></div>
    	</div>
    	<?php } ?>
    </div>

    <div class="media-listing<?php echo !$groups ? ' is-empty' : '';?>">

    	<?php if ($groups) { ?>
    	<ul class="list-media fd-reset-list list-media-group" data-groups-list>
    		<?php foreach ($groups as $group) { ?>
    		<li class="<?php echo $group->isMember() && !$group->isOwner() ? 'is-member' : '';?> 
    			<?php echo $group->isInvited() && !$group->isMember() ? 'is-invited' : '';?> 
    			<?php echo $group->isOwner() ? ' is-owner' : '';?>
    			<?php echo !$group->isMember() && !$group->isInvited() ? 'is-guest' : '';?>"
    			data-id="<?php echo $group->id;?>"
    			data-type="<?php echo $group->isOpen() ? 'open' : 'closed';?>"
    			data-groups-item
    		>
    			<?php echo $this->loadTemplate('site/groups/default.items.group', array('group' => $group, 'featured' => false)); ?>
    		</li>
    		<?php } ?>
    	</ul>
    	<?php } else { ?>

            <?php if ($filter == 'invited') { ?>
    		<div class="empty empty-hero">
    			<i class="fa fa-users"></i>
    			<div><?php echo JText::_('COM_EASYSOCIAL_GROUPS_NO_INVITED_GROUPS_YET');?></div>
    		</div>
    		<?php } ?>


    		<?php if ($filter != 'featured' && $filter != 'invited') { ?>
    		<div class="empty empty-hero">
    			<i class="fa fa-users"></i>
    			<div><?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_NO_GROUPS_YET');?></div>
    		</div>
    		<?php } ?>
    	<?php } ?>
    </div>

    <div class="text-center">
        <div class="list-pagination">
            <?php echo $pagination->getListFooter( 'site' );?>
        </div>
    </div>
</div>