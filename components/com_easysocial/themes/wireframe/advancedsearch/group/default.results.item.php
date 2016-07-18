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

<li class="<?php echo $group->isMember() && !$group->isOwner() ? 'is-member' : '';?>
	<?php echo $group->isInvited() && !$group->isMember() ? 'is-invited' : '';?>
	<?php echo $group->isOwner() ? ' is-owner' : '';?>
	<?php echo !$group->isMember() && !$group->isInvited() ? 'is-guest' : '';?>"
	data-id="<?php echo $group->id;?>"
	data-type="<?php echo $group->isOpen() ? 'open' : 'closed';?>"
	data-groups-item
	data-search-item
>
    <?php echo $this->loadTemplate('site/groups/default.items.group', array('group' => $group, 'featured' => $group->isFeatured())); ?>
</li>
