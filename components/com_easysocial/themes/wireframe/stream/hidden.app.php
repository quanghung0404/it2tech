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
<div class="es-stream-hide-notice" data-stream-hide-notice >
	<div class="media">
		<div class="media-object pull-left">
			<i class="icon-es-zip mr-5"></i>
		</div>
		<div class="media-body">
			<a href="javascript:void(0);" data-stream-show-app class="btn btn-es-success pull-right"><?php echo JText::_( 'COM_EASYSOCIAL_STREAM_UNDO' ); ?></a>
			<div class="notice-text">
				<?php echo JText::sprintf('COM_EASYSOCIAL_STREAM_ITEM_APP_HIDDEN_SUCCESS' , $context ); ?>
			</div>
		</div>
	</div>
</div>
