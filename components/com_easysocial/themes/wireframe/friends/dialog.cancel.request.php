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
<dialog>
	<width>400</width>
	<height>150</height>
	<selectors type="json">
	{
		"{cancelRequestButton}"  : "[data-cancelrequest-button]",
		"{cancelButton}"  : "[data-cancel-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYSOCIAL_FRIENDS_CANCEL_REQUEST_DIALOG_TITLE' ); ?></title>
	<content>
		<p><?php echo JText::sprintf( 'COM_EASYSOCIAL_FRIENDS_CANCEL_REQUEST_DIALOG_CONFIRMATION' , $user->getName() ); ?></p>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-es"><?php echo JText::_( 'COM_EASYSOCIAL_CANCEL_BUTTON' ); ?></button>
		<button data-cancelrequest-button type="button" class="btn btn-es-danger"><?php echo JText::_( 'COM_EASYSOCIAL_YES_CANCEL_FRIEND_REQUEST_BUTTON' ); ?></button>
	</buttons>
</dialog>
