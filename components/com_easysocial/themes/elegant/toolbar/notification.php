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
<li class="bar-activity bar-icon pull-right notificationUpdate">
	<a href="javascript:void(0);" class="loadNotification"><?php echo JText::_( 'COM_EASYSOCIAL_NOTIFICATIONS_NEW_UPDATES' );?></a>

	<?php if( $this->newNotifications ){ ?>
	<b class="notificationCount"><?php echo $this->newNotifications;?></b>
	<?php } ?>

	<div class="notificationDropdown">
		<div class="scroller">

			<!-- Scroller library -->
			<div class="scrollbar">
				<div class="track">
					<div class="thumb">
						<div class="end"></div>
					</div>
				</div>
			</div>

			<div class="viewport">
				<div class="overview notificationList">
				</div>
			</div>

		</div>
	</div>

</li>
