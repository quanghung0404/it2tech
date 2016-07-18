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
<a href="javascript:void(0);" class="btn btn-es btn-sm" data-es-followers-follow data-es-followers-id="<?php echo $user->id;?>" data-es-show-popbox="false">
	<i class="fa fa-share-alt  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_FOLLOW_USER_BUTTON' );?>
</a>
