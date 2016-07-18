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
<?php if( $location && $this->config->get( 'conversations.location' ) ){ ?>
<div class="small mt-20">
	<i class="fa fa-map-marker "></i> <?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_MESSAGE_POSTED_FROM' );?>
	<a href="javascript:void(0);">
		<?php echo $location->getAddress( 30 ); ?>
	</a>
</div>
<?php } ?>
