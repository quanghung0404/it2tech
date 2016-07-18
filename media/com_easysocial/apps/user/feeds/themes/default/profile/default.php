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
<div class="app-user-feeds-wrapper profile<?php echo !$feeds ? ' is-empty' : '';?>" data-feeds>
	<?php if( $feeds ){ ?>
	<ul class="list-unstyled feeds-list" data-feeds-list>
		<?php if( $feeds ){ ?>
			<?php foreach( $feeds as $feed ){ ?>
				<?php echo $this->loadTemplate( 'themes:/apps/user/feeds/profile/default.item' , array( 'totalDisplayed' => $totalDisplayed , 'feed' => $feed , 'params' => $params ) ); ?>
			<?php } ?>
		<?php } ?>
	</ul>
	<?php } else { ?>
	<div class="empty">
		<i class="fa fa-rss-square"></i>
		<?php echo JText::_( 'APP_FEEDS_NO_FEED_YET' ); ?>
	</div>
	<?php } ?>

</div>
