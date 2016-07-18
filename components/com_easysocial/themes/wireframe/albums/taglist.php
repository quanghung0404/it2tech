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
<div class="es-album-taglist es-widget">
	<div><?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_IN_THIS_ALBUM' );?></div>
	<?php if ( empty($tags) ) { ?>
	<span><?php echo JText::_('COM_EASYSOCIAL_ALBUMS_NO_TAGGED_PERSON'); ?></span>
	<?php } else { ?>
	<ul class="widget-list-grid">
		<?php foreach($tags as $tag) { ?>
			<?php $user = FD::user($tag->uid); ?>
			<?php if (!$user->isBlock()) { ?>
			<li>
				<div class="es-avatar-wrap">
					<a href="<?php echo ( $user->isBlock() ) ? 'javascript: void(0);' : $user->getPermalink();?>" class="es-avatar es-avatar-xs"
						data-original-title="<?php echo $this->html( 'string.escape' , $user->getName() );?>"
						data-es-provide="tooltip"
						data-placement="bottom">
						<img src="<?php echo $user->getAvatar();?>" alt="<?php echo $this->html( 'string.escape' , $user->getName() );?>" />
						<?php echo $user->getName();?>
					</a>
					<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $user->isOnline() , 'size' => 'mini' ) ); ?>
				</div>
			</li>
			<?php } ?>
		<?php } ?>
	</ul>
	<?php } ?>
</div>
