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
<div class="panel">
	<div class="panel-head">
		<b><?php echo JText::_( 'COM_EASYSOCIAL_USERS_ACHIEVEMENTS' ); ?></b>
		<p><?php echo JText::_( 'COM_EASYSOCIAL_USERS_BADGES_INFO' ); ?></p>
	</div>

	<div class="panel-body">
		<ul class="es-user-badges mb-20 mt-15">
			<?php if( $badges ){ ?>
				<?php foreach( $badges as $badge ){ ?>
					<li>
						<a href="javascript:void(0);" class="btn-delete" data-delete-badge data-id="<?php echo $badge->id;?>" data-userid="<?php echo $user->id;?>">×</a>
						<img src="<?php echo $badge->getAvatar();?>" alt="<?php echo $this->html( 'string.escape' , $badge->get( 'title' ) );?>" />
						<div class="mt-15"><?php echo $badge->get( 'title' );?></div>
						<div class="mt-5 fd-small"><?php echo $badge->getAchievedDate()->format( JText::_('COM_EASYSOCIAL_DATE_DMY') ); ?></div>
					</li>
				<?php } ?>
			<?php } ?>

			<?php if( !$badges ){ ?>
			<li class="empty">
				<img src="<?php echo rtrim( JURI::root() , '/' );?>/media/com_easysocial/badges/empty.png" />
				<div class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_USERS_NO_BADGES_YET' ); ?></div>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
