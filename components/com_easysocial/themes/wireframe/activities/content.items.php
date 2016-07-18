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
<div class="es-streams activity-logs">
	<ul data-activities-list class="es-stream-list">
		<?php foreach( $activities as $activity ){ ?>
			<?php echo $this->loadTemplate( 'site/activities/item' , array( 'activity' => $activity, 'active' => $active ) ); ?>
		<?php } ?>


		<li class="pagination" style="border-top: 0px;" data-activity-pagination data-startlimit="<?php echo $nextlimit; ?>" >
			<div>
				<?php if( $nextlimit ){ ?>
					<a class="btn btn-es-primary btn-stream-updates" href="javascript:void(0);"><i class="fa fa-refresh"></i>	<?php echo JText::_( 'COM_EASYSOCIAL_ACTIVITY_LOG_LOAD_PREVIOUS_STREAM_ITEMS' ); ?></a>
				<?php } ?>
			</div>
		</li>

	</ul>
</div>
<?php } else { ?>
<div class="center mt-20">
	<i class="icon-es-empty-activity mb-10"></i>
	<div>
		<?php echo JText::_('COM_EASYSOCIAL_ACTIVITY_NO_ACTIVITY_LOG'); ?>
	</div>
</div>
<?php } ?>
