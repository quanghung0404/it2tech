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
<div class="apps-user-notes-wrapper" data-profile-user-apps-notes>

	<h3>
		<?php echo $note->get( 'title' ); ?>
	</h3>

	<div class="fd-small">
		<?php echo $this->html( 'string.date' , $note->created ); ?>
	</div>

	<hr />

	<div class="note-contents">
		<?php echo $note->content; ?>
	</div>

	<div class="note-actions mt-20">
		<a href="#"><?php echo JText::_( 'Comment' ); ?></a> &bull;
		<a href="#"><?php echo JText::_( 'Like' ); ?></a> &bull;
		<a href="#"><?php echo JText::_( 'Follow' ); ?></a>
	</div>
</div>
