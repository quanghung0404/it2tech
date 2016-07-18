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
<?php if( $messages ){ ?>
	<?php
		$curDay = '';
		foreach( $messages as $message ){
			if( $curDay != $message->day )
			{
				$curDay = $message->day;
				$date 	= FD::date( $message->created );

				$dateText = ( $message->day > 0 ) ? $date->toFormat( 'F d Y' ) : JText::_('COM_EASYSOCIAL_CONVERSATIONS_TODAY');

	?>

		<li class="conversation-date">
			<span class="conversation-timestamp"><?php echo $dateText; ?></span>
		</li>
		<?php } ?>

		<?php echo $this->loadTemplate( 'site/conversations/read.item.' . $message->getType() , array( 'message' => $message ) ); ?>
	<?php } ?>
<?php } ?>
