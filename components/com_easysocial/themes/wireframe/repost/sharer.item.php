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
<div class="popbox-header">
	<div class="es-title"><?php echo JText::_( 'COM_EASYSOCIAL_REPOST_VIEW_ALL_AUTHORS_TITLE' ); ?></div>
</div>

<div class="popbox-body">
	<ul class="es-avatar-list pl-5 pb-10" data-repost-authors-list>
	<?php foreach( $users as $user ){ ?>
	<li class="mr-10">
		<div class="es-avatar-wrap">
			<a data-original-title="<?php echo $this->html( 'string.escape' , $user->getName() );?>" data-placement="bottom" data-es-provide="tooltip" class="es-avatar" href="<?php echo $user->getPermalink();?>">
				<img src="<?php echo $user->getAvatar();?>" />
			</a>
			<?php echo $this->loadTemplate( 'site/utilities/user.online.state' , array( 'online' => $user->isOnline() , 'size' => 'mini' ) ); ?>
		</div>
	</li>
	<?php } ?>
	</ul>
</div>
