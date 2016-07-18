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
<div data-followers-content>

	<div class="es-snackbar">
		<?php if( $active == 'followers' ){ ?>
			<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_FOLLOWERS_TITLE' ); ?>
		<?php } ?>

		<?php if( $active == 'following' ){ ?>
			<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_FOLLOWING_TITLE' ); ?>
		<?php } ?>

		<?php if( $active == 'suggest' ){ ?>
			<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_SUGGEST_TITLE' ); ?>
		<?php } ?>
	</div>

	<ul class="es-item-grid es-item-grid_1col<?php echo !$users ? ' is-empty' : '';?>" data-followers-items>
		<?php if( $users ){ ?>
			<?php foreach( $users as $user ){ ?>
				<?php echo $this->loadTemplate( 'site/followers/default.item' , array( 'user' => $user , 'active' => $active , 'currentUser' => $currentUser ) ); ?>
			<?php } ?>
		<?php } ?>

		<li class="empty center mt-20" data-friends-emptyItems>
			<i class="icon-es-empty-follow mb-10"></i>
			<div>
				<?php if( $active == 'followers' ){ ?>
					<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_NO_FOLLOWERS_YET' ); ?>
				<?php } ?>

				<?php if( $active == 'following' ){ ?>
					<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_NOT_FOLLOWING_YET' ); ?>
				<?php } ?>

				<?php if( $active == 'suggest' ){ ?>
					<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_NO_ONE_TO_FOLLOW' ); ?>
				<?php } ?>
			</div>
		</li>
	</ul>
</div>
