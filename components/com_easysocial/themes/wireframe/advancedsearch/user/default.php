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
<div class="es-dashboard" data-adv-search>
	<div class="es-container">

		<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
			<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
		</a>

		<div class="es-sidebar" data-sidebar data-advsearch-sidebar>

			<?php echo $this->render( 'widgets' , 'user' , 'search' , 'sidebarTop' ); ?>

			<?php echo $this->includeTemplate( 'site/advancedsearch/user/sidebar', array( 'fid' => $fid, 'filters' => $filters ) ); ?>

			<?php echo $this->render( 'widgets' , 'user' , 'search' , 'sidebarBottom' ); ?>

		</div>

		<div class="es-content">
			<i class="loading-indicator fd-small"></i>

			<div data-advsearch-content >
				<?php echo $this->includeTemplate( 'site/advancedsearch/user/default.content', array( 'fid' => $fid, 'displayOptions' => $displayOptions ) ); ?>
			</div>
		</div>

	</div>
</div>
