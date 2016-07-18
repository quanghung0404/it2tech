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
<div class="app-k2">

	<div class="app-contents<?php echo !$items ? ' is-empty' : '';?>">
		<ul class="fd-reset-list k2-list">
			<?php if( $items ){ ?>
				<?php foreach( $items as $item ){ ?>
					<?php echo $this->loadTemplate( 'themes:/apps/user/k2/profile/item' , array( 'item' => $item ) ); ?>
				<?php } ?>
			<?php } ?>
		</ul>

		<div class="empty">
			<i class="fa fa-droplet"></i>
			<?php echo JText::sprintf( 'APP_USER_K2_NO_ARTICLES_CURRENTLY', $user->getName() ); ?>
		</div>

	</div>

</div>
