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
<div data-relationship-display data-relationship-display-confirm class="data-relationship-display" data-id="<?php echo $relation->id; ?>">
	<?php if( $relation->isConnect() && !empty( $relation->target ) && !$relation->isPending() ) { ?>
	<div data-relationship-display-info class="data-relationship-display-info">
		<div data-relationship-display-type class="data-relationship-display-type">
			<span data-relationship-display-type-label>
				<?php echo (isset($advancedsearchlink) && $advancedsearchlink) ? '<a href="' . $advancedsearchlink . '">' : ''; ?>
				<?php echo $relation->getLabel(); ?>
				<?php echo (isset($advancedsearchlink) && $advancedsearchlink) ? '</a>' : ''; ?>
			</span>
			<?php if( $relation->isConnect() && !empty( $relation->target ) ) { ?>
				<span data-relationship-display-type-connectword><?php echo $relation->getConnectWord(); ?></span>
			<?php } ?>
		</div>
		<?php if( $relation->isConnect() && !empty( $relation->target ) && !$relation->isPending() ) { ?>
		<div class="media">
			<div class="media-object pull-left">
				<div data-relationship-display-target class="data-relationship-display-target">
					<img src="<?php echo $relation->getTargetUser()->getAvatar(SOCIAL_AVATAR_MEDIUM); ?>" data-relationship-display-target-avatar />
				</div>
			</div>
			<div class="media-body">
				<div data-relationship-display-target-name><a href="<?php echo $relation->getTargetUser()->getPermalink(); ?>"><?php echo $relation->getTargetUser()->getName(); ?></a></div>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php } else {
		echo (isset($advancedsearchlink) && $advancedsearchlink) ? '<a href="' . $advancedsearchlink . '">' : '';
		echo $relation->getLabel();
		echo (isset($advancedsearchlink) && $advancedsearchlink) ? '</a>' : '';
	} ?>
</div>
