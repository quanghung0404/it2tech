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
<li class="widget-filter custom-filter<?php echo $filter->favicon ? ' has-fonticon' : '';?><?php echo $hide ? ' hide' : '';?><?php echo $filterId == $filter->alias ? ' active' : '';?>"
	data-profileSidebar-menu
	data-sidebar-app-filter
	data-type="appFilter"
>
	<a  href="javascript:void(0);"
		data-profileFeeds-item
		data-type="appFilter"
		data-url="<?php echo FRoute::profile(array('id' => $user->id, 'type' => 'appFilter', 'filterid' => $filter->alias));?>"
		data-filterid="<?php echo $filter->alias;?>"
		data-user="<?php echo $user->id;?>"
		data-title="<?php echo $this->html('string.escape', $filter->title); ?>" 
		class="data-profilefeeds-item"
	>
		<span class="es-app-filter">
			<span class="filter-title"><?php echo $filter->title;?></span>
		</span>
	</a>
</li>
