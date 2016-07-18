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

$timeformat = $params->get('agenda_timeformat', '12') == '24' ? JText::_('COM_EASYSOCIAL_DATE_DMY24H') : JText::_('COM_EASYSOCIAL_DATE_DMY12H');
?>
<a href="<?php echo FRoute::apps( array( 'layout' => 'canvas' , 'uid' => $user->getAlias() , 'type' => SOCIAL_TYPE_USER , 'id' => $app->getAlias() ) );?>">&larr; <?php echo JText::_( 'APP_CALENDAR_CANVAS_RETURN_TO_CALENDAR' );?></a>

<div class="row">
	<div class="col-md-12">
		<h3 class="pull-left ml-10"><?php echo $calendar->get( 'title' ); ?></h3>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="event-time muted">
			<img src="<?php echo $app->getIcon();?>" class="mr-5" /> <?php echo $calendar->getStartDate()->format( $timeformat );?> - <?php echo $calendar->getEndDate()->format( $timeformat );?>
		</div>
		<hr />

		<p class="mt-20"><?php echo $calendar->get( 'description' ); ?></p>
	</div>
</div>

<div class="es-action-wrap">
	<ul class="list-unstyled es-action-feedback">
		<li><a href="javascript:void(0);" class="fd-small"><?php echo $likes->button();?></a></li>
	</ul>
</div>

<div data-stream-counter class="es-stream-counter<?php echo ( $likes->getCount() == 0 ) ? ' hide' : ''; ?>">
	<div class="es-stream-actions"><?php echo $likes->toHTML(); ?></div>
</div>

<div class="es-stream-actions">
	<?php echo $comments->getHTML( array( 'hideEmpty' => false ) );?>
</div>
