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
<li class="widget-filter custom-filter <?php echo $filter->favicon ? 'has-fonticon' : '';?> <?php if ($context == $filter->alias) { ?>active<?php } ?>"
	style="<?php if ($hide) { ?>display: none;<?php } ?>"
	data-es-group-filter
	data-dashboardSidebar-menu
	data-type="<?php echo  SOCIAL_TYPE_GROUP; ?>"
	data-id="<?php echo $group->id; ?>"
	data-fid="<?php echo '0'; ?>"
>
	<a href="javascript:void(0);"
		data-id="<?php echo $filter->alias;?>"
		data-url="<?php echo FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias(), 'app' => $filter->alias ) );?>"
		data-title="<?php echo $this->html( 'string.escape' , $filter->title ); ?>"
		data-es-group-app-filter
		class="data-dashboardfeeds-item"
	><?php echo $filter->title;?></a>
</li>
