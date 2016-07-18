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
<div data-notifications-list class="es-notification-list<?php echo !$items ? ' is-empty' : '';?>">

	<?php if( $items ){ ?>

		<div class="es-notification-tool">
			<span><a href="javascript:void(0);" data-notification-all-read ><?php echo JText::_( 'COM_EASYSOCIAL_MARK_ALL_READ' );?></a></span> | <span><a href="javascript:void(0);" data-notification-all-clear ><?php echo JText::_( 'COM_EASYSOCIAL_CLEAR_ITEMS' );?></a></span>
		</div>

		<?php echo $this->loadTemplate( 'site/notifications/default.item' , array( 'items' => $items ) ); ?>

		<a href="javascript:void(0);" class="btn btn-es btn-loadmore btn-sm"
		   data-notification-loadmore-btn
		   data-startlimit="<?php echo $startlimit;?>"
		><?php echo JText::_( 'COM_EASYSOCIAL_NOTIFICATIONS_LOAD_MORE' );?></a>

	<?php } else { ?>
	<div class="empty es-notifications-empty">
		<?php echo JText::_( 'COM_EASYSOCIAL_NOTIFICATIONS_NO_NOTIFICATIONS' );?>
	</div>
	<?php } ?>
</div>
