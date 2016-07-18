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

$active = 'active';
?>
<div class="es-widget es-widget-borderless">
	<div class="es-widget-head">
		<i class="icon-es-settings"></i>
		<?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_SIDEBAR_PREFERENCES' );?>
	</div>

	<div class="es-widget-body">
		<ul class="fd-nav fd-nav-stacked">

		<?php
			foreach( $privacy as $group => $items ) {
		?>
			<li class="<?php echo $active; ?>"
				data-profile-privacy-item
				data-group="<?php echo $group; ?>"

			>
				<a href="javascript:void('0');">
					<i class="icon-es-settings"></i>
					<?php echo JText::_( ucfirst( $group ) );?>
				</a>
			</li>
		<?php
				$active = '';
			}
		?>

		</ul>
	</div>
</div>
