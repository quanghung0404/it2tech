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
<a class="btn-es btn-friends" href="javascript:void(0);" data-id="<?php echo $user->id;?>" data-popbox-friends-friends data-bs-toggle="dropdown">
	<i class="fa fa-check  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_POPOVER_FRIENDS' );?>
</a>
<ul style="position: absolute; top:20px; left:-60px; *width: 280px;" role="menu" class="dropdown-menu dropdown-arrow-topright" data-friends-submenu>
	<li>
		<a href="javascript:void(0);" tabindex="-1" data-popbox-friends-friends-cancel data-friendid="<?php echo $user->getFriend( $this->my->id )->id;?>"><?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_POPOVER_REMOVE_FRIEND' );?></a>
	</li>
</ul>
