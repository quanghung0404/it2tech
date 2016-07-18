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
<div class="stream-calendar">
	<div class="media mt-10">
		<div class="media-object pull-left mt-5">
			<img src="<?php echo $app->getIcon( SOCIAL_APPS_ICON_LARGE );?>" />
		</div>

		<div class="media-body">
			<div class="app-title">
				<b><a href="<?php echo $calendar->getPermalink();?>"><?php echo $calendar->get( 'title' );?></a></b>
			</div>
			<div>
				<span><?php echo $calendar->getStartDate()->format( $timeformat ); ?></span> -
				<span><?php echo $calendar->getEndDate()->format( $timeformat );?></span>
				<?php if( $calendar->all_day ){ ?>
				( <?php echo JText::_( 'APP_CALENDAR_ALL_DAY_EVENT' ); ?> )
				<?php } ?>
			</div>
		</div>
	</div>
	<hr />

	<div class="mb-20 mt-10 app-description"><?php echo nl2br( $calendar->get( 'description' ) ); ?></div>
</div>
