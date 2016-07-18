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
<div class="stream-badges">
	<div class="media mt-20 mb-20">
		<div class="media-object pull-left mr-20">
			<a href="<?php echo $badge->getPermalink();?>"><img src="<?php echo $badge->getAvatar();?>" alt="<?php echo $this->html( 'string.escape' , $badge->get( 'title' ) );?>" /></a>
		</div>
		<div class="media-body">
			<div class="app-title">
				<a href="<?php echo $badge->getPermalink();?>"><b><?php echo $badge->get( 'title' );?></b></a>
			</div>
			<div class="app-description"><?php echo $badge->get( 'description' ); ?></div>
		</div>
	</div>
</div>
