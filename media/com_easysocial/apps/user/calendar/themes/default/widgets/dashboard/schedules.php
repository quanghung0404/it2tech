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
<div class="es-widget">
	<div class="es-widget-head">
		<div class="pull-left widget-title">
			<?php echo JText::_( 'APP_CALENDAR_UPCOMING_APPOINTMENTS' ); ?>
		</div>
	</div>
	<div class="es-widget-body">
		<?php if( $appointments ){ ?>
		<ul class="list-unstyled">
			<?php foreach( $appointments as $appointment ){?>
			<li>
				<div class="fd-small">
					<a href="<?php echo $app->getCanvasUrl( array( 'schedule_id' => $appointment->id , 'customView' => 'item' , 'uid' => $this->my->id , 'type' => SOCIAL_TYPE_USER ) );?>"
					data-es-provide="tooltip"
					data-original-title="<?php echo $this->html( 'string.escape' , $appointment->get( 'title' ) );?>"
					data-original-content="<?php echo $this->html( 'string.escape' , $appointment->get( 'description' ) );?>"
					data-placement="bottom"
					><?php echo $appointment->get( 'title' ); ?></a>
				</div>
				<div class="mt-5">
					<span class="fd-small"><?php echo $appointment->getStartDate()->format( JText::_('COM_EASYSOCIAL_DATE_DMY') );?></span>
				</div>
			</li>
			<?php } ?>
		</ul>
		<?php } else { ?>
		<div class="fd-small"><?php echo JText::_( 'APP_CALENDAR_NO_APPOINTMENTS' ); ?></div>
		<?php } ?>
	</div>
</div>
