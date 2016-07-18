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
<li class="friendItem">
	<div class="es-item">
		<div class="es-avatar-wrap pull-left">
			<a href="javascript:void(0);" class="es-avatar pull-left">
				<img src="https://gravatar.com/avatar/<?php echo md5($user->email);?>" />
			</a>
		</div>

		<div class="es-item-body">
			<div class="es-item-detail">
				<div class="es-item-title">
					<a href="javascript:void(0);"><?php echo $user->email;?></a>
				</div>
				<ul class="fd-reset-list es-friends-links list-inline">
					<li>
						<?php if ($user->registered_id) { ?>
							<?php echo JText::sprintf('COM_EASYSOCIAL_FRIENDS_INVITED_REGISTERED_AS', $this->html('html.user', $user->registered_id)); ?>
						<?php } else { ?>
							<?php echo JText::sprintf('COM_EASYSOCIAL_FRIENDS_INVITED_ON', '<b>' . $this->html('string.date', $user->created, JText::_('DATE_FORMAT_LC1')) . '</b>'); ?>
						<?php } ?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</li>
