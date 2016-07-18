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
<?php if ($this->my->canDeleteUser($user) || $this->my->canBanUser($user)) { ?>
<a href="javascript:void(0);" class="btn btn-block btn-es btn-sm"
	data-bs-toggle="dropdown"
>
	<i class="fa fa-cog mr-5"></i>
	<span><?php echo JText::_('COM_EASYSOCIAL_PROFILE_ADMIN_TOOLS');?></span>
	<i class="fa fa-caret-down"></i>
</a>

<ul class="dropdown-menu dropdown-arrow-topleft dropdown-admintool" data-admintool-dropdown>

	<?php if ($this->my->canBanUser($user)) { ?>
		<?php if (!$user->isBlock()) { ?>
		<li data-admintool-banuser>
			<a href="javascript:void(0);" data-admintool-ban data-id="<?php echo $user->id;?>"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_BAN_THIS_USER');?></a>
		</li>
		<?php } else { ?>
		<li>
			<a href="javascript:void(0);" data-id="<?php echo $user->id;?>" data-admintool-unban><?php echo JText::_('COM_EASYSOCIAL_PROFILE_UNBAN_USER');?></a>
		</li>
		<?php } ?>
	<?php } ?>

	<?php if ($this->my->canDeleteUser($user)) { ?>
	<li data-admintool-deleteuser>
		<a href="javascript:void(0);" data-admintool-delete data-id="<?php echo $user->id;?>"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_DELETE_THIS_USER');?></a>
	</li>
	<?php } ?>
</ul>
<?php } ?>
