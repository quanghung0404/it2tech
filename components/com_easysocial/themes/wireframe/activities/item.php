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

$active = ( isset( $active ) ) ? $active : '';
?>
<li class="type-<?php echo $activity->context; ?> es-stream-mini"
	data-id="<?php echo $activity->uid;?>"
	data-current-state="<?php echo $activity->isHidden; ?>"
	data-activity-item
>
	<div class="es-stream activityData <?php echo ( $activity->isHidden && $active != 'hidden' ) ? ' isHidden' : '' ; ?>">
		<div class="es-stream-type"></div>
		<div class="es-stream-control btn-group pull-right">
			<a class="btn-control" href="javascript:void(0);" data-bs-toggle="dropdown">
				<i class="fa fa-caret-down"></i>
			</a>
			<ul class="dropdown-menu fd-reset-list">
				<li>
					<a href="javascript:void(0);" data-activity-toggle >
						<?php echo ( $activity->isHidden ) ? JText::_('COM_EASYSOCIAL_ACTIVITY_SHOW') : JText::_('COM_EASYSOCIAL_ACTIVITY_HIDE'); ?>
					</a>
				</li>
				<?php if( $this->my->id == $activity->actors[0]->id ) { ?>
				<li>
					<a href="javascript:void(0);" data-activity-delete >
						<?php echo JText::_('COM_EASYSOCIAL_ACTIVITY_DELETE'); ?>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>

		<?php echo $activity->privacy; ?>


		<div class="es-activity">
			<div class="activity-title mb-10">
				<?php echo $activity->title; ?>
			</div>

			<?php if( $activity->content ){ ?>
			<div class="activity-content">
				<blockquote>
					<?php echo $activity->content; echo $activity->meta; ?>
				</blockquote>
			</div>
			<?php } ?>

			<div class="activity-meta">
				<i class="fa fa-clock-o-2 "></i> <span><?php echo $activity->friendlyDate;?></span>
			</div>
		</div>


	</div>
</li>
