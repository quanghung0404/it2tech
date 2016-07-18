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
<a href="javascript:void(0);"
	data-stream-notification-button
	data-type="<?php echo $type; ?>"
	data-uid="<?php echo $uid; ?>"
	data-since="<?php echo $currentdate; ?>"
	class="btn btn-es-primary btn-stream-updates mb-20"
><i class="fa fa-refresh  mr-5"></i> <?php echo JText::sprintf( 'COM_EASYSOCIAL_STREAM_NEW_UPDATES' ); ?></a>
