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
		"{confirmButton}"  : "[data-archive-button]",
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
	<title><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_ARCHIVE_DIALOG_TITLE' ); ?></title>
	<content>
		<form data-conversation-archive-form action="<?php echo JRoute::_( 'index.php' );?>" method="post">
			<p><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_ARCHIVE_DIALOG_CONFIRMATION' ); ?></p>
			<?php foreach( $ids as $id ){ ?>
			<input type="hidden" name="id[]" value="<?php echo $id;?>" />
			<?php } ?>
			<input type="hidden" name="option" value="com_easysocial" />
			<input type="hidden" name="task" value="archive" />
			<input type="hidden" name="controller" value="conversations" />
			<input type="hidden" name="<?php echo FD::token();?>" value="1" />
		</form>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-es btn-sm"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></button>
		<button data-archive-button type="button" class="btn btn-es-danger btn-sm"><?php echo JText::_('COM_EASYSOCIAL_YES_ARCHIVE_BUTTON'); ?></button>
	</buttons>
</dialog>
