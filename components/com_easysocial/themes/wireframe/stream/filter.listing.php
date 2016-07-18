<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="es-dashboard" data-stream-form>

	<div class="es-container">
		<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
			<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
		</a>
		<div class="es-sidebar" data-sidebar data-stream-sidebar>

			<?php echo $this->includeTemplate( 'site/stream/sidebar.filters' ); ?>
		</div>

		<div class="es-content" data-stream-content>

			<i class="loading-indicator fd-small"></i>

			<div data-stream-real-content>
				elloo
			</div>

		</div>
	</div>
</div>


