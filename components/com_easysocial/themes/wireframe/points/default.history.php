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
<?php if( $user->id != $this->my->id ){ ?>
<div class="mb-15">
	<?php echo $this->loadTemplate( 'site/profile/mini.header' , array( 'user' => $user ) ); ?>
</div>
<?php  } ?>

<div data-points-history>
	<?php if( $user->id == $this->my->id ){ ?>
	<div class="view-heading">
		<h3><?php echo JText::_( 'COM_EASYSOCIAL_POINTS_HISTORY_TITLE' );?></h3>
		<p><?php echo JText::_( 'COM_EASYSOCIAL_POINTS_HISTORY_DESC' ); ?></p>
	</div>
	<?php } ?>

	<div class="es-container">

		<div class="es-pointshistory">

			<ul class="fd-reset-list es-timeline-points" data-points-history-timeline>
				<?php echo $this->includeTemplate( 'site/points/default.history.item' ); ?>
			</ul>
			<button class="btn btn-mini btn-loadmore"<?php echo $pagination->total < count( $histories ) ? ' disabled="disabled"' : '';?>
				data-points-history-pagination
				data-current="<?php echo $pagination->pagesCurrent * $pagination->limit;?>"
			><?php echo JText::_( 'COM_EASYSOCIAL_LOAD_MORE' );?></button>
		</div>
	</div>
</div>
