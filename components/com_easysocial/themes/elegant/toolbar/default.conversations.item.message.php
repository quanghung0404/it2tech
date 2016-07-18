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
<div class="object-title">
	<?php echo FD::get( 'String' )->namesToStream( $conversation->getParticipants( $this->my->id ) , false , 5 ); ?>
</div>

<?php if( $conversation->getLastMessage($this->my->id) ){ ?>
<div class="object-content fd-small mt-5">
	<?php if( $conversation->getLastMessage($this->my->id)->created_by == $this->my->id ){ ?>
	<i class="ies-share-2 ies-small"
		data-es-provide="tooltip"
		data-placement="bottom"
		data-original-title="<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATION_YOU_HAVE_REPLIED_HERE' );?>"
	></i>
	<?php } ?>
	<?php echo $conversation->getLastMessage( $this->my->id )->getIntro( 80 ); ?>
</div>
<?php } ?>

<div class="object-timestamp mt-5">
	<small><?php echo FD::date( $conversation->lastreplied )->toLapsed(); ?></small>
</div>
