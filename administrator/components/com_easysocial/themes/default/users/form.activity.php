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
<?php if( $activities ){ ?>
<ul class="es-user-activities">
	<?php foreach( $activities as $activity ){ ?>
	<li class="type-<?php echo $activity->favicon; ?>">
		<div class="row-fluid activity-meta">
			<div class="activity-title pull-left">
				<?php echo $activity->title; ?>
			</div>

			<div class="activity-type pull-right">
				<span class="label es-stream-type"><?php echo JText::_( 'COM_EASYSOCIAL_STREAM_CONTEXT_TITLE_' . strtoupper( $activity->context ) );?></span>
			</div>
		</div>
		<div class="activity-date">
			<em><?php echo $activity->friendlyDate;?></em>
		</div>

		<?php if( $activity->content ){ ?>
		<div class="activity-content"><?php echo $activity->content; ?></div>
		<?php } ?>
	</li>
	<?php } ?>
</ul>
<?php } else { ?>
<?php } ?>
