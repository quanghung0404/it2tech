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
<li class="widget-filter custom-filter <?php echo $filterId == $filter->id ? 'active' : '';?>"
	data-es-group-filter
	data-dashboardSidebar-menu
	data-type="<?php echo  SOCIAL_TYPE_GROUP; ?>"
	data-id="<?php echo $group->id; ?>"
	data-fid="<?php echo $filter->id; ?>"
>

	<a href="<?php echo FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias() , 'type' => 'filterForm' , 'filterId' => $filter->getAlias() ) );?>"
		class="data-dashboardfeeds-filter-edit custom-filter-edit"
		data-dashboardFeeds-Filter-edit
		data-id="<?php echo $filter->id; ?>"
	>
		<i class="fa fa-pencil"></i>
	</a>

	<a  href="<?php echo FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias() , 'filterId' => $filter->getAlias() ) );?>"
		data-es-group-stream
		data-type="custom"
		data-dashboardFeeds-item
		data-id="<?php echo $filter->id; ?>"
		data-url="<?php echo FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias() , 'filterId' => $filter->getAlias() ) );?>"
		data-title="<?php echo $this->html( 'string.escape' , $filter->title ); ?>"
		class="data-dashboardfeeds-item"
	>
		<i class="fa fa-list mr-5"></i> <?php echo $filter->title; ?>
	</a>
</li>
