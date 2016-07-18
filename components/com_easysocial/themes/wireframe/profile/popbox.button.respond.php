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
<a class="btn-es btn-friends" href="javascript:void(0);" data-popbox-friends-respond data-bs-toggle="dropdown">
	<i class="fa fa-refresh  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_POPOVER_RESPOND' );?>
</a>
<ul style="position: absolute; top:20px; left:-40px;" role="menu" class="dropdown-menu dropdown-arrow-topright" data-friends-submenu>
	<li>
		<a href="javascript:void(0);" data-popbox-friends-respond-approve data-friendid="<?php echo $user->getFriend( $this->my->id )->id;?>"><?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_POPOVER_ACCEPT_REQUEST' );?></a>
	</li>
	<li class="divider"></li>
	<li>
		<a href="javascript:void(0);" tabindex="-1" data-popbox-friends-respond-reject data-friendid="<?php echo $user->getFriend( $this->my->id )->id;?>"><?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_POPOVER_REJECT_REQUEST' );?></a>
	</li>
</ul>
