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
<div class="es-filter">

	<ul class="fd-reset-list friendListItems">
		<li class="friendNavigation searchFriends">
			<input type="text" class="input-full" placeholder="<?php echo JText::_( 'Search for friends' , true );?>" />
		</li>
		<li class="divider">
		</li>
		<li class="friendNavigation suggestFriends">
			<a href="javascript:void(0);">
				<?php echo JText::_( 'Suggest Friends' );?>
			</a>
		</li>
		<li class="divider">
		</li>
		<li class="friendListItem allFriends">
			<a href="javascript:void(0);">
				<?php echo JText::_( 'Mutual Friends' );?> <span class="es-count-no pull-right">(0)</span>
			</a>
		</li>
		<li class="friendNavigation suggestFriends">
			<a href="javascript:void(0);">
				<?php echo JText::_( 'Recently Added' );?>
			</a>
		</li>
	</ul>

</div>
