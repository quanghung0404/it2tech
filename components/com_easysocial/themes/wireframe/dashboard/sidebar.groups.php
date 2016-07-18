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
<div class="es-widget es-widget-borderless">
	<div class="es-widget-head">
        <div class="widget-title pull-left">
            <?php echo JText::_('COM_EASYSOCIAL_DASHBOARD_SIDEBAR_GROUPS');?>
        </div>
	</div>

	<div class="es-widget-body">
		<ul class="fd-nav fd-nav-stacked feed-items" data-dashboard-groups>
			<?php if( $groups ){ ?>
				<?php $x = 1; ?>
				<?php foreach( $groups as $group ){ ?>
					<li class="widget-filter<?php echo $groupId == $group->id ? ' active' : '';?><?php echo $this->template->get('dashboard_groups_total') != 0 && $x > $this->template->get('dashboard_groups_total') ? ' hide' :'';?>"
						data-dashboard-group-item
						data-dashboardSidebar-menu
						data-type="group"
						data-id="<?php echo $group->id;?>">
						<a href="<?php echo FRoute::dashboard( array( 'type' => 'group' , 'groupId' => $group->getAlias() ) );?>"
							title="<?php echo $this->html( 'string.escape' , $this->my->getName() ) . ' - ' . $this->html( 'string.escape' , $group->getName() ); ?>">
							<?php echo $group->getName(); ?>
						</a>
					</li>
					<?php $x++; ?>
				<?php } ?>

				<?php if ($this->template->get('dashboard_groups_total') != 0 && count($groups) > $this->template->get('dashboard_groups_total')) { ?>
				<li>
					<a href="javascript:void(0);" class="filter-more" data-groups-filters-showall><?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_SHOW_MORE_GROUPS' ); ?></a>
				</li>
				<?php } ?>
			<?php } else { ?>
			<li class="empty fd-small">
				<?php echo JText::_( 'COM_EASYSOCIAL_DASHBOARD_SIDEBAR_NO_GROUPS_YET' ); ?>
			</li>
			<?php } ?>
		</ul>
	</div>

</div>
