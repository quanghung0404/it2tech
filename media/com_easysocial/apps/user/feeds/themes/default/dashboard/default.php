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
<div class="app-feeds" data-feeds>
	<div class="es-filterbar row-table">
		<div class="col-cell filterbar-title"><?php echo JText::_( 'APP_USER_FEEDS_MANAGE_FEEDS' ); ?></div>

		<div class="col-cell cell-tight">
			<a class="btn btn-es-primary btn-sm pull-right" href="javascript:void(0);" data-feeds-create><?php echo JText::_( 'APP_FEEDS_NEW_FEED' ); ?></a>
		</div>
	</div>

	<div class="app-contents<?php echo !$feeds ? ' is-empty' : '';?>" data-app-contents>
		<p class="app-info">
			<?php echo JText::_( 'APP_FEEDS_DASHBOARD_INFO' ); ?>
		</p>

		<div class="app-contents-data">
			<ul class="list-unstyled feeds-list" data-feeds-lists>
				<?php if( $feeds ){ ?>
					<?php foreach( $feeds as $feed ){ ?>
						<?php echo $this->loadTemplate( 'themes:/apps/user/feeds/dashboard/default.item' , array( 'feed' => $feed ) ); ?>
					<?php } ?>
				<?php } ?>
			</ul>
		</div>

		<div class="empty" data-feeds-empty>
			<i class="fa fa-database"></i>
			<?php echo JText::_( 'APP_FEEDS_NO_FEEDS_YET' ); ?>
		</div>
	</div>

</div>
